<?php

namespace Modules\Legal\Contracts;

interface EsignProvider
{
    public function providerKey(): string;

    /**
     * @param  array<string, mixed>  $context
     */
    public function requestSignature(string $documentPath, array $context = []): array;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function handleCallback(array $payload): void;
}
