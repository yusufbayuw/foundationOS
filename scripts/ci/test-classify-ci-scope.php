<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$script = $root . '/scripts/ci/classify-ci-scope.php';
$cases = [
    [['README.md', 'docs/operations/new.md'], false, ''],
    [['Modules/Cms/app/Services/CmsPublishingService.php'], false, 'tests/Feature/CmsPublishingServiceTest.php'],
    [['Modules/Printing/resources/views/example.blade.php'], false, 'tests/Feature/PrintTemplateRegistrationServiceTest.php'],
    [['Modules/Finance/app/Services/PaymentService.php'], true, ''],
    [['Modules/Cms/app/Models/CmsPage.php'], true, ''],
    [['tests/Feature/SomeRegressionTest.php'], true, ''],
    [['config/app.php'], true, ''],
    [['.github/workflows/tests.yml'], true, ''],
    [['Modules/Cms/app/A.php', 'Modules/Capacity/app/B.php', 'Modules/Printing/app/C.php'], true, ''],
    [[], true, ''],
];
foreach ($cases as $number => [$paths, $expectedFull, $expectedTarget]) {
    $file = tempnam(sys_get_temp_dir(), 'ci-diff-');
    if ($file === false) {
        throw new RuntimeException('Unable to make CI test fixture');
    }
    file_put_contents($file, $paths === [] ? '' : implode("\0", $paths) . "\0");
    $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($file);
    $output = [];
    $exit = 1;
    exec($command . ' 2>/dev/null', $output, $exit);
    unlink($file);
    if ($exit !== 0) {
        throw new RuntimeException("Classifier fixture $number crashed");
    }
    $body = implode("\n", $output);
    if (!str_contains($body, 'full=' . ($expectedFull ? 'true' : 'false')) ||
        !str_contains($body, 'targets=' . $expectedTarget)) {
        throw new RuntimeException("Classifier fixture $number failed: $body");
    }
}
echo "PASS: conservative CI scope classification fixtures\n";
