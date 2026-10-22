<?php

$proxies = array_values(array_filter(
    array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', ''))),
    static fn (string $proxy): bool => $proxy !== '',
));

if (in_array('*', $proxies, true)) {
    throw new InvalidArgumentException(
        'TRUSTED_PROXIES may not contain wildcard (*). Configure explicit proxy IP addresses or CIDR ranges.',
    );
}

return [
    // Only explicit peers may provide X-Forwarded-* metadata.
    'trusted_proxies' => $proxies,
];
