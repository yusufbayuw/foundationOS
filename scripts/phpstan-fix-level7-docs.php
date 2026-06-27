#!/usr/bin/env php
<?php

/**
 * Apply PHPDoc array-shape hints for common PHPStan level 7 iterable patterns.
 */
$dryRun = in_array('--dry-run', $argv, true);
$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';

/** @var array<string, array<string, string>> */
$methodDocs = [
    'Modules/Workflow/app/Contracts/WorkflowEngine.php' => [
        'advance' => '@param array<string, mixed> $formData',
        'returnToStep' => '@param array<string, mixed> $formData',
    ],
    'Modules/Workflow/app/Contracts/ProvidesWorkflowContext.php' => [
        'workflowContext' => '@return array<string, mixed>',
    ],
    'Modules/Workflow/app/Contracts/RuleEngine.php' => [
        'evaluate' => '@param array<string, mixed> $context',
    ],
    'Modules/Workflow/app/Contracts/WorkflowAuditLogger.php' => [
        'log' => '@param array<string, mixed> $context',
    ],
    'Modules/Workflow/app/Contracts/WorkflowFormSchemaValidator.php' => [
        'validate' => '@param array<string, mixed> $formData',
    ],
    'Modules/Workflow/app/Contracts/WorkflowInstanceStarter.php' => [
        'start' => '@param array<string, mixed> $context',
    ],
    'Modules/Workflow/app/Contracts/WorkflowTransitionResolver.php' => [
        'resolve' => '@param array<string, mixed> $context',
    ],
    'Modules/Campus/app/Services/GradebookConfigResolver.php' => [
        'componentsFor' => '@return array<string, array{weight: float, matchers: array<int, string>}>',
        'scaleFor' => '@return array<int, array{min: float, letter: string, point: float}>',
        'gradeFor' => '@return array{letter: string, point: float}',
    ],
    'app/Support/TypedValue.php' => [
        'intList' => "@param list<int> \$default\n     * @return list<int>",
    ],
    'app/Http/Controllers/Api/v1/ApiController.php' => [
        'success' => '@param array<string, mixed> $meta',
        'error' => '@param array<string, mixed> $details',
    ],
];

$updated = 0;

foreach ($methodDocs as $relativePath => $methods) {
    $file = $root.'/'.$relativePath;
    if (! is_file($file)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $original = $content;

    foreach ($methods as $method => $docLine) {
        $content = injectMethodDoc($content, $method, $docLine);
    }

    if ($content !== $original) {
        if (! $dryRun) {
            file_put_contents($file, $content);
        }
        $updated++;
        echo ($dryRun ? '[dry-run] ' : '')."Updated: {$file}\n";
    }
}

echo ($dryRun ? '[dry-run] ' : '')."Updated {$updated} file(s).\n";

function injectMethodDoc(string $content, string $method, string $docLine): string
{
    $pattern = '/(?:(    \/\*\*(?:(?!    \*\/)[\s\S])*?    \*\/\s*\n)+)?(    (?:public|protected|private) (?:static )?function '.$method.'\()/';

    return preg_replace_callback($pattern, function (array $matches) use ($docLine): string {
        $existing = $matches[1] ?? '';

        if ($existing !== '' && str_contains($existing, $docLine)) {
            return $matches[0];
        }

        if ($existing !== '' && str_contains($existing, '@return') && str_starts_with($docLine, '@return')) {
            return $matches[0];
        }

        if ($existing !== '' && str_contains($existing, '@param') && str_starts_with($docLine, '@param')) {
            foreach (explode("\n", $docLine) as $line) {
                $param = trim(str_replace('@param', '', $line));
                if ($param !== '' && ! str_contains($existing, explode(' ', $param)[1] ?? '')) {
                    $existing = rtrim($existing, " \n")."     * {$line}\n";
                }
            }

            return $existing.$matches[2];
        }

        if ($existing !== '') {
            $lines = array_filter(array_map('trim', explode("\n", $docLine)));
            $insert = '';
            foreach ($lines as $line) {
                $insert .= "     * {$line}\n";
            }

            $existing = preg_replace('/(\s+\*\/\s*\n)$/', $insert.'$1', $existing) ?? $existing;

            return $existing.$matches[2];
        }

        $lines = array_filter(array_map('trim', explode("\n", $docLine)));
        $doc = "    /**\n";
        foreach ($lines as $line) {
            $doc .= "     * {$line}\n";
        }
        $doc .= "     */\n";

        return $doc.$matches[2];
    }, $content, 1) ?? $content;
}
