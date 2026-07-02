<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-inventory');
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('barcode') && is_string($this->barcode)) {
            $this->merge([
                'barcode' => trim($this->barcode),
            ]);
        }
    }

    public function rules(): array
    {
        $item = $this->route('item');

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
            'barcode' => ['sometimes', 'nullable', 'string', 'max:64', Rule::unique('items', 'barcode')->ignore($item?->id)],
            'color' => 'sometimes|nullable|string|max:40',
            'scan_mode' => 'sometimes|nullable|in:unit,pack',
            'pack_size' => 'sometimes|nullable|integer|min:1',
            'barcode_source' => 'sometimes|nullable|in:manufacturer,internal',
            'internal_sku' => 'sometimes|nullable|string|max:64',
            'featured' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
            'generate_barcode' => 'sometimes|boolean',
        ];
    }
}
