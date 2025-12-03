<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Investment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function __construct(private readonly FinanceService $finance) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->finance->resolveRange($request->date('from'), $request->date('to'));

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

        $resumen = $this->finance->resumen($sales, $expenses, $nonInventoryPurchases, $investments);
        $movimientosCaja = $this->finance->movimientos($sales, $expenses, $nonInventoryPurchases, $investments);
        $cashflowDataset = $this->finance->cashflowDataset($sales, $expenses, $nonInventoryPurchases, $investments, $from, $to);
        $ingresosVsGastosDataset = $this->finance->ingresosVsGastosDataset($ingresos, $egresos, $invertido);

        return view('finance.index', [
            'rango' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'resumen' => $resumen,
            'movimientosCaja' => $movimientosCaja,
            'cashflowDataset' => $cashflowDataset,
            'ingresosVsGastosDataset' => $ingresosVsGastosDataset,
        ]);
    }
}
