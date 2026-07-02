<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogListPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage-inventory');
    }

    public function rules(): array
    {
        return [
            'sector' => 'required|string',
            'item_ids' => 'required|array|min:1|max:500',
            'item_ids.*' => 'required|integer|exists:items,id',
        ];
    }

    /**
     * @return list<int>
     */
    public function itemIds(): array
    {
        return collect($this->validated('item_ids'))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function sector(): string
    {
        return (string) $this->validated('sector');
    }
}
