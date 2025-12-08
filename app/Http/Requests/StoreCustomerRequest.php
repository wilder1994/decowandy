<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id ?? null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('customers', 'document')->ignore($customerId),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
