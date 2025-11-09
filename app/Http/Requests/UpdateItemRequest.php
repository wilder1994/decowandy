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
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|required|string|in:product,service',
            'sector' => 'sometimes|required|string|in:papeleria,impresion,diseno',
            'sale_price' => 'sometimes|required|numeric|min:0',
            'cost' => 'sometimes|nullable|numeric|min:0',
            'stock' => 'sometimes|nullable|integer|min:0',
            'min_stock' => 'sometimes|nullable|integer|min:0',
            'unit' => 'sometimes|nullable|string|max:30',
            'featured' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ];
    }
}
