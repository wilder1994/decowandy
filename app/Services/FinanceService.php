<?php

namespace App\Services;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceService
{
    public function resolveRange(?string $from, ?string $to): array
    {
        if (!$from || !$to) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();

            return [$start, $end];
        }

        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();

        return [$start, $end];
    }

    public function resumen(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments): array
    {
        $ingresos = (int) $sales->sum('total');
        $egresos = (int) ($expenses->sum('amount') + $purchases->sum('total'));
        $invertido = (int) $investments->sum('amount');

        $utilidad = $ingresos - $egresos;
        $recuperado = max(0, $utilidad);
        $porRecuperar = max($invertido - $recuperado, 0);
        $porcentajeRecuperado = $invertido > 0 ? round(($recuperado / $invertido) * 100, 1) : 0;
        $estadoCaja = $ingresos - $egresos - $invertido;

        return [
            'ingresos' => $ingresos,
            'egresos' => $egresos,
            'invertido' => $invertido,
            'utilidad' => $utilidad,
            'recuperado' => $recuperado,
            'por_recuperar' => $porRecuperar,
            'porcentaje_recuperado' => $porcentajeRecuperado,
            'caja' => $estadoCaja,
        ];
    }

    public function movimientos(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments): Collection
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

        return $listado->sortByDesc('fecha')->values();
    }

    public function cashflowDataset(Collection $sales, Collection $expenses, Collection $purchases, Collection $investments, Carbon $from, Carbon $to): array
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

    public function ingresosVsGastosDataset(int $ingresos, int $egresos, int $invertido): array
    {
        return [
            'labels' => ['Ingresos', 'Egresos', 'Inversión'],
            'data' => [
                $ingresos,
                $egresos,
                $invertido,
            ],
        ];
    }
}
