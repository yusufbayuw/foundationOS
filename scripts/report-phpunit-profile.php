<?php

/**
 * Produce a safe, actionable profile of actual PHPUnit test invocations from
 * the consolidated JUnit report. No test payload, environment, or secrets.
 */
declare(strict_types=1);

if ($argc !== 2) {
    fwrite(STDERR, "Usage: php scripts/report-phpunit-profile.php <junit.xml>\n");
    exit(2);
}

try {
    $document = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $loaded = $document->load($argv[1], LIBXML_NONET | LIBXML_NOBLANKS);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    if (! $loaded) {
        throw new RuntimeException('Invalid PHPUnit JUnit report.');
    }

    $xpath = new DOMXPath($document);
    $files = [];

    foreach ($xpath->query('//testcase') as $case) {
        if (! $case instanceof DOMElement) {
            continue;
        }

        $path = $case->getAttribute('file');
        $parent = $case->parentNode;

        while ($path === '' && $parent instanceof DOMElement) {
            if ($parent->tagName === 'testsuite') {
                $path = $parent->getAttribute('file');
            }
            $parent = $parent->parentNode;
        }

        if ($path === '') {
            $path = '(not attributed)';
        } else {
            $root = realpath(dirname(__DIR__));
            $resolved = realpath($path) ?: $path;
            $path = $root !== false && str_starts_with($resolved, $root.DIRECTORY_SEPARATOR)
                ? substr($resolved, strlen($root) + 1)
                : basename($resolved);
        }

        $files[$path] ??= ['cases' => 0, 'seconds' => 0.0, 'failures' => 0];
        $files[$path]['cases']++;
        $files[$path]['seconds'] += max(0.0, (float) $case->getAttribute('time'));
        $files[$path]['failures'] += $xpath->query('./failure|./error', $case)->length > 0 ? 1 : 0;
    }

    if ($files === []) {
        throw new RuntimeException('JUnit file contains no executed test cases.');
    }

    $byCases = $files;
    uasort($byCases, fn (array $a, array $b): int => $b['cases'] <=> $a['cases']);
    $byTime = $files;
    uasort($byTime, fn (array $a, array $b): int => $b['seconds'] <=> $a['seconds']);

    echo 'PHPUnit actual invocations: '.array_sum(array_column($files, 'cases')).' across '.count($files)." test files\n";

    foreach (['Most invocations' => $byCases, 'Most cumulative test runtime' => $byTime] as $label => $list) {
        echo "{$label}:\n";
        foreach (array_slice($list, 0, 12, true) as $file => $info) {
            printf("  %5d cases  %8.1fs  %s\n", $info['cases'], $info['seconds'], $file);
        }
    }
} catch (Throwable $exception) {
    fwrite(STDERR, "JUnit profile unavailable: {$exception->getMessage()}\n");
    exit(1);
}
