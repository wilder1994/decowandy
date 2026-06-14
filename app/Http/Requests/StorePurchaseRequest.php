<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-inventory');
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);

        if (! is_array($items)) {
            return;
        }

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            if (isset($item['barcode']) && is_string($item['barcode'])) {
                $items[$index]['barcode'] = trim($item['barcode']);
            }
        }

        $this->merge(['items' => $items]);
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:50'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'to_inventory' => ['required', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.total_cost' => ['required', 'integer', 'min:0'],
            'items.*.item_id' => [
                'nullable',
                'integer',
                Rule::exists('items', 'id')->where(fn ($query) => $query->where('active', true)),
            ],
            'items.*.barcode' => ['nullable', 'string', 'max:64'],
            'items.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.color' => ['nullable', 'string', 'max:40'],
            'items.*.scan_mode' => ['nullable', 'in:unit,pack'],
            'items.*.pack_size' => ['nullable', 'integer', 'min:1'],
            'items.*.min_stock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
