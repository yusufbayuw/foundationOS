<?php

namespace Modules\Counseling\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnonymousReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
