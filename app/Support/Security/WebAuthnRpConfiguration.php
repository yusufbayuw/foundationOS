<?php

namespace App\Support\Security;

use RuntimeException;

final class WebAuthnRpConfiguration
{
    /**
     * Fail closed: accepted WebAuthn origins and RP IDs must be explicitly
     * configured, not inferred from untrusted HTTP forwarding headers.
     *
     * Only an exact HTTPS hostname match is currently supported; a future
     * verified multi-subdomain policy requires separate security review.
     */
    public static function assertValid(string $rpId, mixed $origins): void
    {
        if (! is_array($origins) || count($origins) !== 1 || ! is_string($origins[0])) {
            throw new RuntimeException('WebAuthn requires exactly one explicit trusted origin.');
        }

        if ($rpId === '' || ! preg_match('/\A[a-z0-9][a-z0-9.-]*\z/D', $rpId)) {
            throw new RuntimeException('Invalid WebAuthn relying party ID.');
        }

        $parsed = parse_url($origins[0]);

        if (
            ! is_array($parsed)
            || ($parsed['scheme'] ?? null) !== 'https'
            || ($parsed['host'] ?? null) !== $rpId
            || isset($parsed['user'])
            || isset($parsed['pass'])
            || isset($parsed['path'])
            || isset($parsed['query'])
            || isset($parsed['fragment'])
        ) {
            throw new RuntimeException('Invalid WebAuthn origin / RP ID configuration.');
        }
    }
}
