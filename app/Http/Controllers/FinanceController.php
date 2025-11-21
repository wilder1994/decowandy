<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Investment;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $sales = Sale::with('items')
            ->whereBetween('sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->orderByDesc('sold_at')
            ->get();

        $expenses = Expense::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('date')
            ->get();

        $nonInventoryPurchases = Purchase::where('to_inventory', false)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('date')
            ->get();

        $investments = Investment::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('date')
            ->get();

        $ingresos = (int) $sales->sum('total');
        $egresos = (int) ($expenses->sum('amount') + $nonInventoryPurchases->sum('total'));
        $invertido = (int) $investments->sum('amount');

        $utilidad = $ingresos - $egresos;
        $recuperado = max(0, $utilidad);
        $porRecuperar = max($invertido - $recuperado, 0);
        $porcentajeRecuperado = $invertido > 0 ? round(($recuperado / $invertido) * 100, 1) : 0;

        $estadoCaja = $ingresos - $egresos - $invertido;

        $movimientosCaja = $this->composeMovements($sales, $expenses, $nonInventoryPurchases, $investments)
            ->sortByDesc('fecha')
            ->values();

        $cashflowDataset = $this->cashflowDataset($sales, $expenses, $nonInventoryPurchases, $investments, $from, $to);

        $ingresosVsGastosDataset = [
            'labels' => ['Ingresos', 'Egresos', 'Inversión'],
            'data' => [
                $ingresos,
                $egresos,
                $invertido,
            ],
        ];

        return view('finance.index', [
            'rango' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'resumen' => [
                'caja' => $estadoCaja,
                'invertido' => $invertido,
                'recuperado' => $recuperado,
                'por_recuperar' => $porRecuperar,
                'porcentaje_recuperado' => $porcentajeRecuperado,
                'utilidad' => $utilidad,
            ],
            'movimientosCaja' => $movimientosCaja,
            'cashflowDataset' => $cashflowDataset,
            'ingresosVsGastosDataset' => $ingresosVsGastosDataset,
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

    private function composeMovements(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments): Collection
    {
        $movements = collect();

        foreach ($sales as $sale) {
            $movements->push([
                'fecha' => $sale->sold_at instanceof Carbon ? $sale->sold_at : Carbon::parse($sale->sold_at),
                'concepto' => $sale->sale_code ? 'Venta ' . $sale->sale_code : 'Venta #' . $sale->id,
                'metodo' => $sale->payment_method,
                'monto' => (int) $sale->total,
                'tipo' => 'entrada',
            ]);
        }

        foreach ($expenses as $expense) {
            $movements->push([
                'fecha' => Carbon::parse($expense->date),
                'concepto' => $expense->concept,
                'metodo' => 'gasto',
                'monto' => (int) $expense->amount * -1,
                'tipo' => 'salida',
            ]);
        }

        foreach ($purchases as $purchase) {
            $movements->push([
                'fecha' => Carbon::parse($purchase->date),
                'concepto' => 'Compra ' . ($purchase->supplier ?: $purchase->category),
                'metodo' => $purchase->to_inventory ? 'inventario' : 'gasto',
                'monto' => (int) $purchase->total * -1,
                'tipo' => 'salida',
            ]);
        }

        foreach ($investments as $investment) {
            $movements->push([
                'fecha' => Carbon::parse($investment->date),
                'concepto' => 'Inversión: ' . $investment->concept,
                'metodo' => 'inversión',
                'monto' => (int) $investment->amount * -1,
                'tipo' => 'salida',
            ]);
        }

        return $movements;
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
}
