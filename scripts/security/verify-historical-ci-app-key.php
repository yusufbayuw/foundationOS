<?php

/**
 * Operator-only, read-only comparison for FoundationOS issue #122.
 *
 * Run on each authorized deployed environment, as the application runtime
 * user, from the active release checkout with full pre-PR-120 Git history.
 * Prints ONLY MATCH / NO MATCH / NOT VERIFIED; never displays a key, a
 * fingerprint, a source-file excerpt or Laravel config values.
 *
 * IMPORTANT: CLI bootstrap may not reflect stale long-lived PHP-FPM/queue
 * workers or a different deployment. The operator must also establish that
 * the active release, cached config and running workers use this same key.
 */

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;

$root = dirname(__DIR__, 2);

// This is a pre-remediation Git commit ID, not a credential.
$revision = 'c86503e705fab8872b26033b61dc1235deb854e0';
$source = $revision.':.github/workflows/tests.yml';

$unverified = static function (): never {
    fwrite(STDOUT, "NOT VERIFIED\n");
    exit(3);
};

if (PHP_SAPI !== 'cli' || ! is_file($root.'/bootstrap/app.php')) {
    $unverified();
}

// git output is collected privately in memory and is never echoed or logged.
$lines = [];
$status = 1;
$command = 'git -C '.escapeshellarg($root).' show '.escapeshellarg($source).' 2>/dev/null';
exec($command, $lines, $status);

if ($status !== 0) {
    $unverified();
}

$historicalWorkflow = implode("\n", $lines);
if (! preg_match('/^\h*APP_KEY\h*:\h*["\x27]?(base64:[A-Za-z0-9+\/=]{40,})["\x27]?\h*$/m', $historicalWorkflow, $matches)) {
    $unverified();
}

try {
    $app = require $root.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();
    $activeKey = config('app.key');
} catch (Throwable) {
    $unverified();
}

if (! is_string($activeKey) || $activeKey === '') {
    $unverified();
}

$isMatch = hash_equals($matches[1], $activeKey);

// Do not print keys, partial keys, hashes, input lengths or exception traces.
fwrite(STDOUT, $isMatch ? "MATCH\n" : "NO MATCH\n");
exit($isMatch ? 2 : 0);
