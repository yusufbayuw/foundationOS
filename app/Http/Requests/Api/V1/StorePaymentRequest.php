<?php

namespace App\Http\Requests\Api\V1;

use App\Support\CurrentTenant;
use Illuminate\Validation\Rule;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;

class StorePaymentRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Payment::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(CurrentTenant $currentTenant): array
    {
        $tenantId = $currentTenant->id();

        return [
            'student_invoice_id' => [
                'required',
                'integer',
                Rule::exists(StudentInvoice::class, 'id')->where('tenant_id', $tenantId),
            ],
            'chart_of_account_id' => [
                'required',
                'integer',
                Rule::exists(ChartOfAccount::class, 'id')->where('tenant_id', $tenantId),
            ],
            'payment_number' => ['required', 'string', 'max:50'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_channel' => ['nullable', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,verified,rejected'],
            'proof_file' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
