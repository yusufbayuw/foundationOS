<?php

namespace Modules\Employee\Services;

use Modules\Employee\Models\LeaveRequest;

class LeaveRequestDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(LeaveRequest $leaveRequest): array
    {
        $leaveRequest->load(['employee', 'substituteEmployee', 'supervisor', 'approver']);

        return [
            'leaveRequest' => $leaveRequest,
            'showSignature' => true,
            'signatureLabel' => 'HR / Atasan',
        ];
    }

    public function filename(LeaveRequest $leaveRequest): string
    {
        return sprintf('LeaveRequest_%s.pdf', (string) $leaveRequest->getKey());
    }
}
