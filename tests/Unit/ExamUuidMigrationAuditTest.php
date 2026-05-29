<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExamUuidMigrationAuditTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function examMigrationFilesProvider(): array
    {
        $directory = dirname(__DIR__, 2).'/Modules/Exam/database/migrations';
        $files = [];

        foreach (glob($directory.'/*.php') ?: [] as $path) {
            $files[basename($path)] = [$path];
        }

        return $files;
    }

    #[DataProvider('examMigrationFilesProvider')]
    public function test_exam_migrations_do_not_use_auto_increment_primary_keys(string $path): void
    {
        $contents = file_get_contents($path);
        $this->assertNotFalse($contents);

        $this->assertDoesNotMatchRegularExpression(
            '/\$table->id\s*\(/',
            $contents,
            "Migration {$path} must not use \$table->id() for Exam tables.",
        );

        $this->assertDoesNotMatchRegularExpression(
            '/bigIncrements\s*\(/',
            $contents,
            "Migration {$path} must not use bigIncrements().",
        );

        $this->assertDoesNotMatchRegularExpression(
            '/increments\s*\(\s*[\'"]id[\'"]\s*\)/',
            $contents,
            "Migration {$path} must not use increments('id').",
        );
    }
}
