<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabelSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-inventory');
    }

    public function rules(): array
    {
        return [
            'lines' => 'required|array|min:1|max:100',
            'lines.*.item_id' => 'required|integer|exists:items,id',
            'lines.*.quantity' => 'required|integer|min:1|max:999',
        ];
    }

    /**
     * @return list<array{item_id: int, quantity: int}>
     */
    public function normalizedLines(): array
    {
        return collect($this->validated('lines'))
            ->map(fn (array $line) => [
                'item_id' => (int) $line['item_id'],
                'quantity' => (int) $line['quantity'],
            ])
            ->values()
            ->all();
    }
}
