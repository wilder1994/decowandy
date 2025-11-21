<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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

        $saleItems = SaleItem::with(['sale', 'item'])
            ->whereHas('sale', function ($query) use ($from, $to, $metodo) {
                $query->whereBetween('sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

                if ($metodo !== 'all') {
                    $query->where('payment_method', $metodo);
                }
            })
            ->when($categoria !== 'all', fn ($query) => $query->where('category', $categoria))
            ->get();

        $ventas = (int) $saleItems->sum('line_total');
        $costoVenta = (int) $saleItems->sum(function (SaleItem $item) {
            $costoUnitario = (float) optional($item->item)->cost;

            return (int) round($costoUnitario * (float) $item->quantity);
        });

        $expenses = Expense::whereBetween('date', [$from->toDateString(), $to->toDateString()])->get();
        $purchases = Purchase::where('to_inventory', false)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $gastos = (int) ($expenses->sum('amount') + $purchases->sum('total'));
        $utilidadNeta = $ventas - $costoVenta - $gastos;

        $ventasPorCategoria = $this->ventasPorCategoria($saleItems);
        $cashflowDataset = $this->cashflowDataset($saleItems, $expenses, $purchases, $from, $to);

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
            'totales' => [
                'ingresos' => $ventas,
                'cogs' => $costoVenta,
                'gastos' => $gastos,
                'utilidad' => $utilidadNeta,
            ],
            'ventasPorCategoria' => $ventasPorCategoria,
            'cashflowDataset' => $cashflowDataset,
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

    private function cashflowDataset(Collection $sales, Collection $expenses, Collection $purchases, Carbon $from, Carbon $to): array
    {
        $period = new \DatePeriod($from->copy()->startOfDay(), new \DateInterval('P1D'), $to->copy()->addDay());

        $labels = [];
        $entradas = [];
        $salidas = [];

        foreach ($period as $day) {
            $dateKey = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');

            $dailyIncome = (int) $sales->filter(fn (SaleItem $item) => $item->sale && $item->sale->sold_at && $item->sale->sold_at->toDateString() === $dateKey)
                ->sum('line_total');

            $dailyExpenses = (int) $expenses->where('date', $dateKey)->sum('amount');
            $dailyPurchases = (int) $purchases->where('date', $dateKey)->sum('total');

            $entradas[] = $dailyIncome;
            $salidas[] = $dailyExpenses + $dailyPurchases;
        }

        return [
            'labels' => $labels,
            'entradas' => $entradas,
            'salidas' => $salidas,
        ];
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
