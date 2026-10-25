<?php

/**
 * Attest that a configured PHPUnit suite actually executed in the successful
 * consolidated PHPUnit run, using its JUnit XML artifact.
 *
 * This is NOT an independent test run. It replaces only redundant executions
 * of suites that already ran in the full required PHPUnit job.
 *
 * Usage: php scripts/verify-phpunit-suite-evidence.php phpunit.xml coverage/junit.xml Tenant
 */
declare(strict_types=1);

if ($argc !== 4) {
    fwrite(STDERR, "Usage: php scripts/verify-phpunit-suite-evidence.php <config> <junit> <suite>\n");
    exit(2);
}

[$command, $configuration, $report, $requested] = $argv;

try {
    $loadXml = static function (string $path): DOMDocument {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->load($path, LIBXML_NONET | LIBXML_NOBLANKS);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            throw new RuntimeException("Unable to parse XML evidence: {$path}");
        }

        return $document;
    };

    $config = $loadXml($configuration);
    $reportXml = $loadXml($report);
    $configXPath = new DOMXPath($config);
    $reportXPath = new DOMXPath($reportXml);

    $directories = [];
    $files = [];
    $suiteFound = false;

    foreach ($configXPath->query('/phpunit/testsuites/testsuite') as $suite) {
        if (! $suite instanceof DOMElement || $suite->getAttribute('name') !== $requested) {
            continue;
        }

        if ($suiteFound) {
            throw new RuntimeException("Duplicate PHPUnit suite: {$requested}");
        }

        $suiteFound = true;

        foreach ($suite->childNodes as $selector) {
            if (! $selector instanceof DOMElement) {
                continue;
            }

            $rawPath = trim($selector->textContent);
            $absolutePath = str_starts_with($rawPath, DIRECTORY_SEPARATOR)
                ? $rawPath
                : dirname($configuration).'/'.$rawPath;
            $selectorPath = realpath($absolutePath);

            if ($selectorPath === false) {
                throw new RuntimeException("Missing configured suite selector for {$requested}");
            }

            if ($selector->tagName === 'directory') {
                $directories[] = rtrim($selectorPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            } elseif ($selector->tagName === 'file') {
                $files[] = $selectorPath;
            } else {
                throw new RuntimeException("Unsupported PHPUnit selector: {$selector->tagName}");
            }
        }
    }

    if (! $suiteFound || ($directories === [] && $files === [])) {
        throw new RuntimeException("Unknown or empty PHPUnit suite: {$requested}");
    }

    $executed = 0;
    $successful = 0;
    $failed = 0;
    $skipped = 0;
    $verifiedFiles = [];

    foreach ($reportXPath->query('//testcase') as $testcase) {
        if (! $testcase instanceof DOMElement) {
            continue;
        }

        $file = $testcase->getAttribute('file');
        $ancestor = $testcase->parentNode;

        while ($file === '' && $ancestor instanceof DOMElement) {
            if ($ancestor->tagName === 'testsuite') {
                $file = $ancestor->getAttribute('file');
            }

            $ancestor = $ancestor->parentNode;
        }

        $path = $file !== '' ? realpath($file) : false;
        if ($path === false) {
            continue;
        }

        $belongs = in_array($path, $files, true);

        foreach ($directories as $directory) {
            if (str_starts_with($path, $directory)) {
                $belongs = true;
                break;
            }
        }

        if (! $belongs) {
            continue;
        }

        $verifiedFiles[$path] = true;
        $executed++;

        if ($reportXPath->query('./failure|./error', $testcase)->length !== 0) {
            $failed++;
        } elseif ($reportXPath->query('./skipped', $testcase)->length !== 0) {
            $skipped++;
        } else {
            $successful++;
        }
    }

    if ($executed === 0 || $successful === 0 || $failed > 0) {
        throw new RuntimeException(
            "Suite {$requested}: insufficient successful execution evidence; ".
            "executed={$executed} passed={$successful} failed={$failed} skipped={$skipped}",
        );
    }

    echo "Verified {$requested}: {$executed} executed, {$successful} passed, ".
        "{$skipped} skipped, ".count($verifiedFiles)." test files (full-run JUnit evidence).\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Suite execution attestation failed: '.$exception->getMessage()."\n");
    exit(1);
}
