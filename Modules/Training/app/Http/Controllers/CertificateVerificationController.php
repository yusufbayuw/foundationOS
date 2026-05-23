<?php

namespace Modules\Training\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Training\Services\TrainingCertificateService;

class CertificateVerificationController extends Controller
{
    public function __construct(
        private readonly TrainingCertificateService $certificateService,
    ) {}

    public function show(string $token): JsonResponse
    {
        $certificate = $this->certificateService->verify($token);

        if (! $certificate) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => true,
            'certificate_number' => $certificate->certificate_number,
            'participant' => $certificate->enrollment?->participant_name,
            'issued_at' => $certificate->issued_at?->toIso8601String(),
        ]);
    }
}
