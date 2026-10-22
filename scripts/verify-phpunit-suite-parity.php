<?php

/**
 * Ensure the consolidated PHPUnit+coverage execution discovers the same tests
 * and runs under the same PHPUnit environment as the previous full runner.
 *
 * Coverage source filters may differ; testsuite selection must not.
 */
declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php scripts/verify-phpunit-suite-parity.php phpunit.xml phpunit.coverage.xml\n");
    exit(2);
}

function readConfiguration(string $path): array
{
    $doc = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $loaded = $doc->load($path, LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    if (! $loaded) {
        throw new RuntimeException("Cannot parse PHPUnit configuration: {$path}");
    }

    $root = $doc->documentElement;
    if (! $root instanceof DOMElement || $root->tagName !== 'phpunit') {
        throw new RuntimeException("Invalid PHPUnit configuration root: {$path}");
    }

    $xpath = new DOMXPath($doc);
    $suites = [];
    foreach ($xpath->query('/phpunit/testsuites/testsuite') as $node) {
        if (! $node instanceof DOMElement) {
            continue;
        }

        $name = trim($node->getAttribute('name'));
        if ($name === '' || array_key_exists($name, $suites)) {
            throw new RuntimeException("Empty/duplicate PHPUnit suite name in {$path}");
        }

        $entries = [];
        foreach ($node->childNodes as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }
            if (! in_array($child->tagName, ['directory', 'file'], true)) {
                throw new RuntimeException("Unsupported suite selector in {$path}: {$child->tagName}");
            }
            $entries[] = [$child->tagName, trim($child->textContent), $child->getAttribute('suffix')];
        }

        if ($entries === []) {
            throw new RuntimeException("Empty PHPUnit suite: {$name}");
        }
        $suites[$name] = $entries;
    }

    if ($suites === []) {
        throw new RuntimeException("No PHPUnit test suites configured: {$path}");
    }

    $environment = [];
    foreach ($xpath->query('/phpunit/php/*') as $node) {
        if (! $node instanceof DOMElement) {
            continue;
        }
        if (! in_array($node->tagName, ['env', 'ini', 'const', 'var', 'server'], true)) {
            throw new RuntimeException("Unsupported PHPUnit php environment directive: {$node->tagName}");
        }
        $key = $node->tagName . ':' . $node->getAttribute('name');
        if (array_key_exists($key, $environment)) {
            throw new RuntimeException("Duplicate PHPUnit environment key: {$key}");
        }
        $environment[$key] = [
            $node->getAttribute('value'),
            $node->getAttribute('force'),
        ];
    }

    return [
        'bootstrap' => $root->getAttribute('bootstrap'),
        'suites' => $suites,
        'environment' => $environment,
    ];
}

try {
    $normal = readConfiguration($argv[1]);
    $coverage = readConfiguration($argv[2]);
    if ($normal !== $coverage) {
        foreach (['bootstrap', 'suites', 'environment'] as $key) {
            if ($normal[$key] !== $coverage[$key]) {
                fwrite(STDERR, "PHPUnit config mismatch in {$key}; refusing optimized CI.\n");
            }
        }
        exit(1);
    }

    echo "Verified exact PHPUnit test suite selection and environment parity.\n";
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
