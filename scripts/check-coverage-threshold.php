#!/usr/bin/env php
<?php

declare(strict_types=1);

$cloverPath = $argv[1] ?? 'coverage/clover.xml';
$minimumPercent = (float) ($argv[2] ?? 80);

if (! is_file($cloverPath)) {
    fwrite(STDERR, "Coverage file not found: {$cloverPath}\n");
    exit(1);
}

$xml = simplexml_load_file($cloverPath);

if ($xml === false) {
    fwrite(STDERR, "Unable to parse coverage file: {$cloverPath}\n");
    exit(1);
}

$metrics = $xml->project->metrics ?? null;

if ($metrics === null) {
    fwrite(STDERR, "Coverage metrics missing in: {$cloverPath}\n");
    exit(1);
}

$statements = (int) ($metrics['statements'] ?? 0);
$covered = (int) ($metrics['coveredstatements'] ?? 0);

if ($statements === 0) {
    fwrite(STDERR, "No statements found in coverage report.\n");
    exit(1);
}

$percent = ($covered / $statements) * 100;

printf(
    "Line coverage: %.2f%% (%d/%d statements)\n",
    $percent,
    $covered,
    $statements,
);

if ($percent < $minimumPercent) {
    fwrite(
        STDERR,
        sprintf("Coverage %.2f%% is below the required minimum of %.2f%%.\n", $percent, $minimumPercent),
    );
    exit(1);
}

fwrite(STDOUT, "Coverage threshold met.\n");
exit(0);
