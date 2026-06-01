<?php

namespace Modules\EOffice\Services;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Core\Support\QrCodeGenerator;
use Modules\EOffice\Models\Letter;

class LetterDocumentService
{
    public function __construct(
        private readonly ?QrCodeGenerator $qrCodeGenerator = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function assemble(Letter $letter): array
    {
        $letter->load(['organization']);

        $token = $this->resolveVerificationToken($letter);
        $verificationUrl = $token !== null
            ? route('letters.verify', ['token' => $token])
            : null;

        $qrSvg = null;

        if ($verificationUrl !== null && $this->qrCodeGenerator !== null) {
            $qrSvg = $this->qrCodeGenerator->svg($verificationUrl, 120);
        }

        return [
            'letter' => $letter,
            'verificationUrl' => $verificationUrl,
            'qrSvg' => $qrSvg,
            'showSignature' => true,
            'signatureLabel' => 'Pejabat Penandatangan',
        ];
    }

    public function filename(Letter $letter): string
    {
        $number = str_replace(['/', ' '], '_', $letter->letter_number ?? $letter->code ?? (string) $letter->getKey());

        return sprintf('Surat_%s.pdf', $number);
    }

    protected function resolveVerificationToken(Letter $letter): ?string
    {
        $token = $letter->verification_token ?? data_get($letter->meta, 'verification_token');

        if (is_string($token) && $token !== '') {
            return $token;
        }

        $token = (string) Str::uuid();

        if (Schema::hasColumn($letter->getTable(), 'verification_token')) {
            $letter->forceFill(['verification_token' => $token])->saveQuietly();
        } else {
            $letter->forceFill([
                'meta' => array_merge($letter->meta ?? [], ['verification_token' => $token]),
            ])->saveQuietly();
        }

        return $token;
    }
}
