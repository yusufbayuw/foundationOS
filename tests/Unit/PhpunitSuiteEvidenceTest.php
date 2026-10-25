<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PhpunitSuiteEvidenceTest extends TestCase
{
    public function test_suite_with_executed_successful_case_is_accepted(): void
    {
        $this->assertEvidenceResult(
            '<testsuites><testsuite name="Unit" file="%FILE%"><testcase name="runs" file="%FILE%"/></testsuite></testsuites>',
            true,
        );
    }

    public function test_suite_with_no_executed_case_fails_closed(): void
    {
        $this->assertEvidenceResult('<testsuites/>', false);
    }

    public function test_other_suite_cannot_impersonate_required_suite(): void
    {
        $this->assertEvidenceResult(
            '<testsuites><testsuite name="Other" file="%FILE%"><testcase name="fake" file="%OTHER%"/></testsuite></testsuites>',
            false,
        );
    }

    public function test_skipped_only_suite_does_not_count_as_safety_evidence(): void
    {
        $this->assertEvidenceResult(
            '<testsuites><testsuite file="%FILE%"><testcase name="skipped" file="%FILE%"><skipped/></testcase></testsuite></testsuites>',
            false,
        );
    }

    public function test_failing_suite_cannot_be_attested_as_green(): void
    {
        $this->assertEvidenceResult(
            '<testsuites><testsuite file="%FILE%"><testcase name="bad" file="%FILE%"><failure>deliberate</failure></testcase></testsuite></testsuites>',
            false,
        );
    }

    private function assertEvidenceResult(string $junit, bool $shouldPass): void
    {
        $configuration = tempnam(sys_get_temp_dir(), 'fos-phpunit-config-');
        $report = tempnam(sys_get_temp_dir(), 'fos-phpunit-report-');

        self::assertNotFalse($configuration);
        self::assertNotFalse($report);

        $testFile = htmlspecialchars(__FILE__, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $otherFile = htmlspecialchars(__DIR__.'/not-the-requested-test.php', ENT_QUOTES | ENT_XML1, 'UTF-8');

        $configXml = '<phpunit><testsuites><testsuite name="Unit"><file>'.$testFile.'</file></testsuite></testsuites></phpunit>';

        try {
            file_put_contents($configuration, $configXml);
            file_put_contents($report, str_replace(['%FILE%', '%OTHER%'], [$testFile, $otherFile], $junit));

            $process = new Process([
                PHP_BINARY,
                dirname(__DIR__, 2).'/scripts/verify-phpunit-suite-evidence.php',
                $configuration,
                $report,
                'Unit',
            ]);
            $process->run();

            self::assertSame(
                $shouldPass,
                $process->isSuccessful(),
                $process->getOutput().$process->getErrorOutput(),
            );
        } finally {
            @unlink($configuration);
            @unlink($report);
        }
    }
}
