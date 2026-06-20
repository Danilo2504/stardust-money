<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required',
            'code' => 'required|unique:expenses,code,'.$this->route('expense'),
            'amount' => 'required|numeric|gt:0',
            'category_id' => 'nullable|exists:categories,id',
            'expense_date' => 'required|date',
            'type' => 'required|in:one_time,recurring_child,installment',
            'notes' => 'nullable',
            'draft' => 'boolean',
            'recurring_expense_id' => 'required_if:type,recurring_child',
            'installment_group_id' => 'required_if:type,installment',
            'installment_number' => 'required_if:type,installment',
        ];
    }
}
