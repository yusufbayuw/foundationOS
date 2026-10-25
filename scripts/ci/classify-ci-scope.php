<?php

declare(strict_types=1);

/**
 * Narrow allowlist for normal ready PR feedback; unknown = full.
 * CI checks this on actual PR base..head, not a user-controlled label.
 * Inputs are NUL-delimited paths from "git diff --name-only -z".
 */
if ($argc !== 2) {
    fwrite(STDERR, "Usage: php scripts/ci/classify-ci-scope.php changed-files.nul\n");
    exit(2);
}

$input = file_get_contents($argv[1]);
if ($input === false || $input === '') {
    fwrite(STDERR, "Cannot classify missing/empty git diff: require full CI.\n");
    echo "full=true\ntargets=\n";
    exit(0);
}

$paths = array_values(array_filter(explode("\0", $input), static fn (string $path): bool => $path !== ''));
$mapped = [
    'Ai' => [
        'tests/Feature/AiAdvisorServiceTest.php',
        'tests/Feature/AiPromptTemplateFilamentTest.php',
        'tests/Feature/AiPromptTemplateRegistrationServiceTest.php',
    ],
    'Capacity' => ['tests/Feature/CapacityResourceRegistrationServiceTest.php'],
    'Cms' => ['tests/Feature/CmsPublishingServiceTest.php'],
    'Consulting' => ['tests/Feature/ConsultingClientRegistrationServiceTest.php'],
    'KpiEnterprise' => ['tests/Feature/KpiAreaRegistrationServiceTest.php'],
    'Printing' => [
        'tests/Feature/PrintTemplateRegistrationServiceTest.php',
        'tests/Feature/PrintTemplateResolverTest.php',
    ],
    'Property' => ['tests/Feature/PropertyRegistrationServiceTest.php'],
];

$full = false;
$modules = [];
foreach ($paths as $path) {
    if (str_contains($path, "\n") || str_contains($path, "\r") ||
        str_contains($path, '..') || str_starts_with($path, '/') ||
        str_starts_with($path, '-') || str_contains($path, '\\')) {
        $full = true;
        break;
    }

    // Documentation and static presentation changes do not need full PHP suites.
    if (preg_match('~^(docs/.*|[A-Za-z0-9_.-]+\.md|resources/(css|js)/.*|public/(css|js|build)/.*)$~D', $path)) {
        continue;
    }

    if (!preg_match('~^Modules/([A-Za-z0-9]+)/(.+)$~D', $path, $match)) {
        $full = true;
        break;
    }

    $module = $match[1];
    $relative = $match[2];
    if (!array_key_exists($module, $mapped) ||
        preg_match('~(^|/)(database|routes|config|Policies|Middleware|Jobs|Listeners|Observers|Models|Http|Contracts|Providers)(/|$)~', $relative)) {
        $full = true;
        break;
    }

    $modules[$module] = true;
}

if (count($paths) > 15 || count($modules) > 2) {
    $full = true;
}

$tests = [];
if (!$full) {
    foreach (array_keys($modules) as $module) {
        foreach ($mapped[$module] as $test) {
            if (!is_file(dirname(__DIR__, 2) . '/' . $test)) {
                $full = true;
                break 2;
            }
            $tests[$test] = true;
        }
    }
}

if ($full) {
    fwrite(STDERR, "Unrecognized, cross-cutting or high-risk change: require full PHPUnit, PostgreSQL, MariaDB, coverage.\n");
    echo "full=true\ntargets=\n";
} else {
    // Only fixed allowlisted files can enter shell-expanded targets.
    fwrite(STDERR, "Narrow change: mandatory security/tenant smoke plus mapped module tests.\n");
    echo "full=false\ntargets=" . implode(' ', array_keys($tests)) . "\n";
}
