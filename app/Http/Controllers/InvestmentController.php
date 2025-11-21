<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index()
    {
        $investments = Investment::orderByDesc('date')->orderByDesc('id')->get();
        $total = (int) $investments->sum('amount');

        return view('finance.investments', [
            'investments' => $investments,
            'total' => $total,
        ]);
    }

    public function apiIndex()
    {
        $investments = Investment::orderByDesc('date')->orderByDesc('id')->get();

        return response()->json([
            'ok' => true,
            'data' => $investments,
            'total' => (int) $investments->sum('amount'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $investment = Investment::create($data);

        return response()->json([
            'ok' => true,
            'investment' => $investment,
        ], 201);
    }

    public function update(Request $request, Investment $investment)
    {
        $data = $this->validateData($request);

        $investment->fill($data);
        $investment->save();

        return response()->json([
            'ok' => true,
            'investment' => $investment->refresh(),
        ]);
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();

        return response()->json(['ok' => true]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'concept' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
