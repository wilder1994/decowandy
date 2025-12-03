<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Investment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(private readonly FinanceService $finance) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->finance->resolveRange($request->date('from'), $request->date('to'));
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

        $resumen = $this->finance->resumen($sales, $expenses, $purchases, $investments);
        $movimientosCaja = $this->finance->movimientos($sales, $expenses, $purchases, $investments);
        $cashflowDataset = $this->finance->cashflowDataset($sales, $expenses, $purchases, $investments, $from, $to);
        $ingresosVsGastosDataset = $this->finance->ingresosVsGastosDataset($ingresos, $egresos, $invertido);

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
            'resumen' => $resumen,
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
        $labels = config('decowandy.sectors');

        $dataset = ['labels' => [], 'data' => []];

        foreach ($labels as $key => $label) {
            $dataset['labels'][] = $label;
            $dataset['data'][] = (int) $items->where('category', $key)->sum('line_total');
        }

        return $dataset;
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
