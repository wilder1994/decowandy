<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Investment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);
        $categoria = $this->sanitizeCategory($request->query('category', 'all'));
        $metodo = $this->sanitizePayment($request->query('payment', 'all'));

        $sales = Sale::with('items')
            ->whereBetween('sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($metodo !== 'all', fn ($query) => $query->where('payment_method', $metodo))
            ->get();

        $saleItems = SaleItem::with(['sale', 'item'])
            ->whereIn('sale_id', $sales->pluck('id'))
            ->when($categoria !== 'all', fn ($query) => $query->where('category', $categoria))
            ->get();

        $expenses = Expense::whereBetween('date', [$from->toDateString(), $to->toDateString()])->get();
        $purchases = Purchase::where('to_inventory', false)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get();
        $investments = Investment::whereBetween('date', [$from->toDateString(), $to->toDateString()])->get();

        $ingresos = (int) $sales->sum('total');
        $egresos = (int) ($expenses->sum('amount') + $purchases->sum('total'));
        $invertido = (int) $investments->sum('amount');

        $utilidad = $ingresos - $egresos;
        $recuperado = max(0, $utilidad);
        $porRecuperar = max($invertido - $recuperado, 0);
        $porcentajeRecuperado = $invertido > 0 ? round(($recuperado / $invertido) * 100, 1) : 0;
        $estadoCaja = $ingresos - $egresos - $invertido;

        $movimientosCaja = $this->composeMovements($sales, $expenses, $purchases, $investments)
            ->sortByDesc('fecha')
            ->values();

        $cashflowDataset = $this->cashflowDataset($sales, $expenses, $purchases, $investments, $from, $to);

        $ingresosVsGastosDataset = [
            'labels' => ['Ingresos', 'Egresos', 'Inversión'],
            'data' => [
                $ingresos,
                $egresos,
                $invertido,
            ],
        ];

        $ventas = (int) $saleItems->sum('line_total');
        $costoVenta = (int) $saleItems->sum(function (SaleItem $item) {
            $costoUnitario = (float) optional($item->item)->cost;

            return (int) round($costoUnitario * (float) $item->quantity);
        });

        $gastos = (int) ($expenses->sum('amount') + $purchases->sum('total'));
        $utilidadNeta = $ventas - $costoVenta - $gastos;

        $ventasPorCategoria = $this->ventasPorCategoria($saleItems);

        $ventasListado = Sale::withCount('items')
            ->whereBetween('sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($metodo !== 'all', fn ($query) => $query->where('payment_method', $metodo))
            ->orderByDesc('sold_at')
            ->limit(8)
            ->get();

        $gastosListado = $this->composeGastosListado($expenses, $purchases);

        return view('reports.index', [
            'filtros' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'category' => $categoria,
                'payment' => $metodo,
            ],
            'resumen' => [
                'caja' => $estadoCaja,
                'invertido' => $invertido,
                'recuperado' => $recuperado,
                'por_recuperar' => $porRecuperar,
                'porcentaje_recuperado' => $porcentajeRecuperado,
                'utilidad' => $utilidad,
            ],
            'totales' => [
                'ingresos' => $ventas,
                'cogs' => $costoVenta,
                'gastos' => $gastos,
                'utilidad' => $utilidadNeta,
            ],
            'ingresosVsGastosDataset' => $ingresosVsGastosDataset,
            'ventasPorCategoria' => $ventasPorCategoria,
            'cashflowDataset' => $cashflowDataset,
            'movimientosCaja' => $movimientosCaja,
            'ventasListado' => $ventasListado,
            'gastosListado' => $gastosListado,
        ]);
    }

    private function resolveRange(Request $request): array
    {
        $from = $request->date('from');
        $to = $request->date('to');

        if (!$from || !$to) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();

            return [$start, $end];
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        return [$start, $end];
    }

    private function sanitizeCategory(string $category): string
    {
        $allowed = ['all', 'diseno', 'impresion', 'papeleria'];

        return in_array($category, $allowed, true) ? $category : 'all';
    }

    private function sanitizePayment(string $payment): string
    {
        $allowed = ['all', 'cash', 'transfer', 'card', 'mixed', 'other'];

        return in_array($payment, $allowed, true) ? $payment : 'all';
    }

    private function ventasPorCategoria(Collection $items): array
    {
        $labels = [
            'diseno' => 'Diseño',
            'impresion' => 'Impresión',
            'papeleria' => 'Papelería',
        ];

        $dataset = ['labels' => [], 'data' => []];

        foreach ($labels as $key => $label) {
            $dataset['labels'][] = $label;
            $dataset['data'][] = (int) $items->where('category', $key)->sum('line_total');
        }

        return $dataset;
    }

    private function cashflowDataset(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments, Carbon $from, Carbon $to): array
    {
        $period = new \DatePeriod($from->copy()->startOfDay(), new \DateInterval('P1D'), $to->copy()->addDay());

        $labels = [];
        $entradas = [];
        $salidas = [];

        foreach ($period as $day) {
            $dateKey = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');

            $dailyIncome = (int) $sales->filter(fn (Sale $sale) => $sale->sold_at && $sale->sold_at->toDateString() === $dateKey)
                ->sum('total');

            $dailyExpenses = (int) $expenses->where('date', $dateKey)->sum('amount');
            $dailyPurchases = (int) $purchases->where('date', $dateKey)->sum('total');
            $dailyInvestments = (int) $investments->where('date', $dateKey)->sum('amount');

            $entradas[] = $dailyIncome;
            $salidas[] = $dailyExpenses + $dailyPurchases + $dailyInvestments;
        }

        return [
            'labels' => $labels,
            'entradas' => $entradas,
            'salidas' => $salidas,
        ];
    }

    private function composeMovements(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments): Collection
    {
        $listado = collect();

        foreach ($sales as $sale) {
            $listado->push([
                'fecha' => $sale->sold_at instanceof Carbon ? $sale->sold_at : Carbon::parse($sale->sold_at),
                'concepto' => $sale->sale_code ? 'Venta ' . $sale->sale_code : 'Venta #' . $sale->id,
                'metodo' => $sale->payment_method,
                'monto' => (int) $sale->total,
                'tipo' => 'entrada',
            ]);
        }

        foreach ($expenses as $expense) {
            $listado->push([
                'fecha' => Carbon::parse($expense->date),
                'concepto' => $expense->concept,
                'metodo' => 'gasto',
                'monto' => (int) $expense->amount * -1,
                'tipo' => 'salida',
            ]);
        }

        foreach ($purchases as $purchase) {
            $listado->push([
                'fecha' => Carbon::parse($purchase->date),
                'concepto' => 'Compra ' . ($purchase->supplier ?: $purchase->category),
                'metodo' => $purchase->to_inventory ? 'inventario' : 'gasto',
                'monto' => (int) $purchase->total * -1,
                'tipo' => 'salida',
            ]);
        }

        foreach ($investments as $investment) {
            $listado->push([
                'fecha' => Carbon::parse($investment->date),
                'concepto' => 'Inversión: ' . $investment->concept,
                'metodo' => 'inversión',
                'monto' => (int) $investment->amount * -1,
                'tipo' => 'salida',
            ]);
        }

        return $listado;
    }

    private function composeGastosListado(Collection $expenses, Collection $purchases): Collection
    {
        $listado = collect();

        foreach ($expenses as $expense) {
            $listado->push([
                'fecha' => Carbon::parse($expense->date),
                'concepto' => $expense->concept,
                'categoria' => $expense->category,
                'monto' => (int) $expense->amount,
            ]);
        }

        foreach ($purchases as $purchase) {
            $listado->push([
                'fecha' => Carbon::parse($purchase->date),
                'concepto' => 'Compra ' . $purchase->category,
                'categoria' => $purchase->category,
                'monto' => (int) $purchase->total,
            ]);
        }

        return $listado->sortByDesc('fecha')->values()->take(10);
    }
}
