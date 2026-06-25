<?php

namespace Modules\Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Library\Enums\LoanIssueType;

class OpacMarkLoanIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'loan_id' => ['required', 'integer'],
            'issue_type' => ['required', Rule::enum(LoanIssueType::class)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
