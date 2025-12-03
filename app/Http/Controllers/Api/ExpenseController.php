<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;


class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request)
    {
        $expense = Expense::create([
            'date' => $request->date('date'),
            'concept' => $request->string('concept')->value(),
            'category' => $request->string('category')->value(),
            'amount' => (int) $request->integer('amount'),
            'note' => $request->input('note'),
            'user_id' => optional(auth()->user())->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'expense' => $expense], 201);
        }

        return redirect()
            ->route('expenses.index')
            ->with('status', 'Gasto registrado correctamente.');
    }

    public function index(Request $request)
    {
        // --- 1) Datos completos para las tarjetas (mes actual) ---

        // Rango de fechas del mes actual
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth   = now()->endOfMonth()->toDateString();

        $monthQuery = Expense::query()
            ->whereBetween('date', [$startOfMonth, $endOfMonth]);

        $totalMonth = (int) $monthQuery->clone()->sum('amount');

        $daysWithExpenses = (int) $monthQuery->clone()
            ->select('date')
            ->distinct()
            ->count();

        $avgDaily = $daysWithExpenses > 0
            ? intdiv($totalMonth, $daysWithExpenses)
            : 0;

        $topCategory = $monthQuery->clone()
            ->select('category', \DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->value('category');

        $lastExpense = Expense::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // --- 2) Filtros para la tabla de abajo ---

        $from           = $request->query('from');
        $to             = $request->query('to');
        $categoryFilter = $request->query('category', 'all');

        $query = Expense::query();

        if ($from) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('date', '<=', $to);
        }

        if ($categoryFilter && $categoryFilter !== 'all') {
            $query->where('category', $categoryFilter);
        }

        $expenses = $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('expenses.index', [
            'expenses'    => $expenses,
            'totalMonth'  => $totalMonth,
            'avgDaily'    => $avgDaily,
            'topCategory' => $topCategory,
            'lastExpense' => $lastExpense,
        ]);
    }

}
