<?php

namespace Modules\Printing\Services;

use App\Support\TypedValue;
use InvalidArgumentException;
use Modules\Printing\Data\ResolvedPrintTemplate;
use Modules\Printing\Models\PrintTemplate;

class PrintTemplateResolver
{
    /**
     * @var array<string, array{view: string, paper: string, orientation: string}>
     */
    private const DEFAULTS = [
        'finance_student_invoice' => [
            'view' => 'finance::pdf.student-invoice',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'finance_customer_invoice' => [
            'view' => 'finance::pdf.customer-invoice',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'finance_payment_receipt' => [
            'view' => 'finance::pdf.payment-receipt',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_study_plan' => [
            'view' => 'campus::pdf.study-plan',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_study_result' => [
            'view' => 'campus::pdf.study-result',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_thesis_letter' => [
            'view' => 'campus::pdf.thesis-letter',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_transcript' => [
            'view' => 'campus::pdf.transcript',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_wisuda' => [
            'view' => 'campus::pdf.wisuda',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'campus_yudisium' => [
            'view' => 'campus::pdf.yudisium',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'procurement_vendor_bill' => [
            'view' => 'procurement::pdf.vendor-bill',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'procurement_goods_receipt' => [
            'view' => 'procurement::pdf.goods-receipt',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'procurement_request_for_quotation' => [
            'view' => 'procurement::pdf.request-for-quotation',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'procurement_purchase_requisition' => [
            'view' => 'procurement::pdf.purchase-requisition',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'procurement_purchase_order' => [
            'view' => 'procurement::pdf.purchase-order',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'school_report_card' => [
            'view' => 'school::pdf.report-card',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'school_attendance_recap' => [
            'view' => 'school::pdf.attendance-recap',
            'paper' => 'a4',
            'orientation' => 'landscape',
        ],
        'school_student_achievement' => [
            'view' => 'school::pdf.student-achievement',
            'paper' => 'a4',
            'orientation' => 'portrait',
        ],
        'school_grade_ledger' => [
            'view' => 'school::pdf.grade-ledger',
            'paper' => 'a4',
            'orientation' => 'landscape',
        ],
    ];

    public function resolve(string $code, int $tenantId): ResolvedPrintTemplate
    {
        $defaults = self::DEFAULTS[$code] ?? null;

        if ($defaults === null) {
            throw new InvalidArgumentException("Unknown print template code [{$code}].");
        }

        $override = PrintTemplate::query()
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->where('status', 'active')
            ->value('meta');

        $meta = is_array($override) ? $override : [];

        return new ResolvedPrintTemplate(
            code: $code,
            view: TypedValue::string($meta['view'] ?? $defaults['view']),
            paper: TypedValue::string($meta['paper'] ?? $defaults['paper']),
            orientation: TypedValue::string($meta['orientation'] ?? $defaults['orientation']),
        );
    }

    /**
     * @return list<string>
     */
    public function registeredCodes(): array
    {
        return array_keys(self::DEFAULTS);
    }
}
