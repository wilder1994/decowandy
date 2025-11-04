<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'concept' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|integer|min:0',
            'note' => 'nullable|string|max:255',
        ];
    }
}
