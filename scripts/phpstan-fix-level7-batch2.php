#!/usr/bin/env php
<?php

/**
 * Batch fixes for remaining PHPStan level 7 patterns.
 */
$root = realpath(__DIR__.'/..') ?: __DIR__.'/..';

$componentReturn = [
    'Modules/School/app/Filament/Resources/Students/Schemas/StudentForm.php' => [
        'basicInformationFields',
        'enrollmentFields',
        'academicAndHealthFields',
        'fatherInformationFields',
        'motherInformationFields',
        'guardianInformationFields',
        'residenceAndTransportFields',
    ],
    'Modules/Exam/app/Filament/Support/ExamQuestionFormSupport.php' => ['miFieldsSchema'],
    'Modules/Workflow/app/Filament/Resources/Workflows/RelationManagers/AutomatedActionsRelationManager.php' => [
        'formComponents' => 'list<\Filament\Forms\Components\Component>',
        'tableColumns' => 'list<\Filament\Tables\Columns\Column>',
    ],
];

$updated = 0;

foreach ($componentReturn as $relative => $methods) {
    $path = $root.'/'.$relative;
    if (! is_file($path)) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    $original = $content;

    foreach ($methods as $key => $method) {
        if (is_int($key)) {
            $methodName = $method;
            $returnType = 'list<\Filament\Forms\Components\Component>';
        } else {
            $methodName = $key;
            $returnType = $method;
        }

        $pattern = '/(\/\*\*(?:(?!\/)[\s\S])*?\*\/\s*\n\s*(?:public|protected|private) (?:static )?function '.preg_quote($methodName, '/').'\([^)]*\)[^{]*\{)/';

        $content = preg_replace_callback($pattern, function (array $m) use ($returnType): string {
            $block = $m[1];
            if (preg_match('/@return array<string, mixed>/', $block)) {
                $block = preg_replace('/@return array<string, mixed>/', '@return '.$returnType, $block);
            } elseif (! str_contains($block, '@return')) {
                $block = preg_replace('/(\/\*\*)/', '/** @return '.$returnType."\n     ", $block, 1);
            }

            return $block;
        }, $content, 1) ?? $content;
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
        echo "Updated: {$relative}\n";
    }
}

// Fix FinancialReportService mangled Collection namespace in @return
$financialReport = $root.'/Modules/Finance/app/Services/FinancialReportService.php';
if (is_file($financialReport)) {
    $content = file_get_contents($financialReport);
    if ($content !== false) {
        $fixed = str_replace(
            '@return Illuminate\Support\Collection',
            '@return \Illuminate\Support\Collection',
            $content,
        );
        if ($fixed !== $content) {
            file_put_contents($financialReport, $fixed);
            $updated++;
            echo "Updated: FinancialReportService.php\n";
        }
    }
}

// Expand qualifyColumn for common columns on relation/sub queries
$columns = [
    'workflow_step_id',
    'academic_period_id',
    'exam_definition_id',
    'member_id',
    'entity_id',
    'invoice_number',
    'collage_student_id',
    'type',
    'difficulty',
    'topic',
    'subtopic',
    'name',
    'standalone_subject',
    'barcode',
    'copy_number',
    'academic_context_type',
];
$vars = ['query', 'inner', 'builder', 'q', 'q2', 'bankQuery', 'subjectQuery', 'courseQuery', 'evidenceQuery'];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (! $file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    if (str_contains($path, '/vendor/') || str_contains($path, '/node_modules/')) {
        continue;
    }

    if (! str_starts_with($path, $root.'/app/') && ! str_starts_with($path, $root.'/Modules/')) {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }

    $original = $content;

    foreach ($columns as $column) {
        foreach ($vars as $var) {
            $content = preg_replace(
                '/(\$'.preg_quote($var, '/').')->where\(\''.preg_quote($column, '/').'\',/',
                '$1->where($1->qualifyColumn(\''.$column.'\'),',
                $content,
            ) ?? $content;

            $content = preg_replace(
                '/(\$'.preg_quote($var, '/').')->orWhere\(\''.preg_quote($column, '/').'\',/',
                '$1->orWhere($1->qualifyColumn(\''.$column.'\'),',
                $content,
            ) ?? $content;
        }
    }

    if ($content !== $original) {
        file_put_contents($path, $content);
        $updated++;
        echo 'Updated columns: '.str_replace($root.'/', '', $path)."\n";
    }
}

echo "Done. Touched {$updated} file(s).\n";
