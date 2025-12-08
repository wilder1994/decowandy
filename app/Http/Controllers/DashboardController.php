<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();
        $monthStart = $today->copy()->startOfMonth();
        $prevMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth();
        $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();

        $salesToday = Sale::whereDate('sold_at', $today)->sum('total');
        $salesYesterday = Sale::whereDate('sold_at', $yesterday)->sum('total');
        $salesMonth = Sale::whereBetween('sold_at', [$monthStart, $today->endOfDay()])->sum('total');
        $salesPrevMonth = Sale::whereBetween('sold_at', [$prevMonthStart, $prevMonthEnd])->sum('total');

        $expensesToday = Expense::whereDate('date', $today)->sum('amount');

        $lowStock = Stock::query()
            ->with('item:id,name,sector')
            ->whereColumn('quantity', '<=', 'min_threshold')
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        $lastSales = Sale::query()
            ->withCount('items')
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        // Cashflow last 14 days (ventas y gastos)
        $dates = collect();
        for ($i = 13; $i >= 0; $i--) {
            $dates->push($today->copy()->subDays($i));
        }
        $salesPerDay = Sale::select(DB::raw('DATE(sold_at) as d'), DB::raw('SUM(total) as t'))
            ->whereDate('sold_at', '>=', $today->copy()->subDays(13))
            ->groupBy('d')
            ->pluck('t', 'd');
        $expensesPerDay = Expense::select(DB::raw('date as d'), DB::raw('SUM(amount) as t'))
            ->whereDate('date', '>=', $today->copy()->subDays(13))
            ->groupBy('d')
            ->pluck('t', 'd');
        $cashflow = [
            'labels' => [],
            'ingresos' => [],
            'egresos' => [],
        ];
        foreach ($dates as $d) {
            $key = $d->toDateString();
            $cashflow['labels'][] = $d->format('d/m');
            $cashflow['ingresos'][] = (int) ($salesPerDay[$key] ?? 0);
            $cashflow['egresos'][] = (int) ($expensesPerDay[$key] ?? 0);
        }

        // Top items por cantidad vendida (10)
        $topItems = SaleItem::select('item_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('item_id')
            ->orderByDesc('qty')
            ->limit(10)
            ->with('item:id,name')
            ->get()
            ->map(fn ($row) => [
                'name' => $row->item->name ?? ('Ítem '.$row->item_id),
                'qty' => (int) $row->qty,
            ]);

        $sectorTotals = SaleItem::select('category', DB::raw('SUM(line_total) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $kpis = [
            'sales_today' => $salesToday,
            'sales_yesterday' => $salesYesterday,
            'sales_month' => $salesMonth,
            'sales_prev_month' => $salesPrevMonth,
            'expenses_today' => $expensesToday,
            'low_stock_count' => $lowStock->count(),
            'sectors' => [
                'papeleria' => (int) ($sectorTotals['papeleria'] ?? 0),
                'impresion' => (int) ($sectorTotals['impresion'] ?? 0),
                'diseno' => (int) ($sectorTotals['diseno'] ?? 0),
            ],
        ];

        return view('dashboard', compact('kpis', 'lowStock', 'lastSales', 'cashflow', 'topItems'));
    }
}
