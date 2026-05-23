<?php

namespace Modules\Training\Services;

use Illuminate\Support\Str;
use Modules\Core\Support\QrCodeGenerator;
use Modules\Training\Models\TrainingCertificate;
use Modules\Training\Models\TrainingEnrollment;

class TrainingCertificateService
{
    public function __construct(
        private readonly QrCodeGenerator $qrCodeGenerator,
    ) {}

    public function issue(TrainingEnrollment $enrollment): TrainingCertificate
    {
        $existing = TrainingCertificate::query()
            ->where('training_enrollment_id', $enrollment->getKey())
            ->first();

        if ($existing) {
            return $existing;
        }

        $number = 'TC-'.$enrollment->tenant_id.'-'.str_pad((string) $enrollment->getKey(), 6, '0', STR_PAD_LEFT);
        $token = Str::uuid()->toString();

        return TrainingCertificate::query()->create([
            'tenant_id' => $enrollment->tenant_id,
            'training_enrollment_id' => $enrollment->getKey(),
            'certificate_number' => $number,
            'verification_token' => $token,
            'issued_at' => now(),
        ]);
    }

    public function verify(string $token): ?TrainingCertificate
    {
        return TrainingCertificate::query()
            ->where('verification_token', $token)
            ->with('enrollment')
            ->first();
    }

    public function qrSvg(TrainingCertificate $certificate): string
    {
        return $this->qrCodeGenerator->svg(route('training.certificate.verify', ['token' => $certificate->verification_token], false));
    }
}
