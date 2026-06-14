<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:product,service',
            'sector' => 'required|string|in:impresion,diseno',
            'sale_price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:30',
            'barcode' => ['nullable', 'string', 'max:64', Rule::unique('items', 'barcode')],
            'color' => 'nullable|string|max:40',
            'scan_mode' => 'nullable|in:unit,pack',
            'pack_size' => 'nullable|integer|min:1',
            'barcode_source' => 'nullable|in:manufacturer,internal',
            'internal_sku' => 'nullable|string|max:64',
            'featured' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ];
    }
}
