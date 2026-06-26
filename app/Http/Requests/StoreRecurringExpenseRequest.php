<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRecurringExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0|max:9999.9999',
            'category_id' => 'nullable|exists:categories,id',
            'custom_interval_value' => 'required|integer|min:1',
            'custom_interval_unit' => 'required|in:days,weeks,months,years',
            'next_due_date' => 'required|date',
            'is_active' => 'boolean',
        ];
    }
}
