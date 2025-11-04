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
            'concept' => $request->string('concept'),
            'category' => $request->string('category'),
            'amount' => (int)$request->integer('amount'),
            'note' => $request->input('note'),
            'user_id' => optional(auth()->user())->id,
        ]);
        return redirect()
            ->route('expenses.index')
            ->with('status', 'Gasto registrado correctamente.');
    }

    public function index(Request $request)
    {
        // --- 1) Datos completos para las tarjetas (mes actual) ---

        // Todos los gastos, del más nuevo al más viejo
        $allExpenses = Expense::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Rango de fechas del mes actual
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth   = now()->endOfMonth()->toDateString();

        // Gastos del mes actual
        $monthExpenses = $allExpenses->whereBetween('date', [$startOfMonth, $endOfMonth]);

        // Total del mes
        $totalMonth = $monthExpenses->sum('amount');

        // Promedio diario: total / días con gasto
        $daysWithExpenses = $monthExpenses->groupBy('date')->count();
        $avgDaily = $daysWithExpenses > 0
            ? intdiv($totalMonth, $daysWithExpenses)
            : 0;

        // Categoría con más gasto (en el mes)
        $topCategory = $monthExpenses
            ->groupBy('category')
            ->sortByDesc(function ($group) {
                return $group->sum('amount');
            })
            ->keys()
            ->first(); // puede ser null

        // Último gasto registrado (de todos)
        $lastExpense = $allExpenses->first(); // o null si no hay gastos

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
