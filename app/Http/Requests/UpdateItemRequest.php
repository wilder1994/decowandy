<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|in:Papelería,Impresión,Diseño,Papeleria',
            'price' => 'sometimes|required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
