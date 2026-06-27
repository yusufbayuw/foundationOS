<?php

namespace Modules\Legal\Services\Esign;

use Modules\Legal\Contracts\EsignProvider;

class ManualUploadEsignProvider implements EsignProvider
{
    public function providerKey(): string
    {
        return 'manual';
    }

    /**
     * @return array<string, mixed>
     */
    public function requestSignature(string $documentPath, array $context = []): array
    {
        return [
            'provider' => $this->providerKey(),
            'status' => 'awaiting_upload',
            'document_path' => $documentPath,
        ];
    }

    public function handleCallback(array $payload): void
    {
        // Manual provider stores signed PDF via Filament upload — no webhook.
    }
}
