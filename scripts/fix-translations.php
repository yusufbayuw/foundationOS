<?php

/**
 * Sprint 3 auto-fixer: wraps hardcoded Section/Tab/Fieldset/Step titles
 * and ->label() strings with FilamentUi::text() / FilamentUi::field().
 *
 * Usage: php scripts/fix-translations.php [path ...]
 */
$paths = array_slice($argv, 1);
if (empty($paths)) {
    echo "Usage: php scripts/fix-translations.php <path> [path ...]\n";
    exit(1);
}

$filamentUiImport = 'use Modules\\Core\\Support\\FilamentUi;';

$fixed = 0;
$filesChanged = 0;

function findPhpFiles(string $path): Generator
{
    if (is_file($path) && str_ends_with($path, '.php')) {
        yield $path;

        return;
    }
    if (! is_dir($path)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        if ($f->isFile() && $f->getExtension() === 'php') {
            yield $f->getPathname();
        }
    }
}

foreach ($paths as $scanPath) {
    foreach (findPhpFiles($scanPath) as $file) {
        $original = file_get_contents($file);
        $content = $original;

        // --- 1. Section::make('Title') → Section::make(FilamentUi::text('Title'))
        $content = preg_replace_callback(
            '/\bSection::make\(\s*\'([A-Z][^\']+)\'\s*\)/',
            fn ($m) => "Section::make(FilamentUi::text('".$m[1]."'))",
            $content
        );
        $content = preg_replace_callback(
            '/\bSection::make\(\s*"([A-Z][^"]+)"\s*\)/',
            fn ($m) => 'Section::make(FilamentUi::text("'.$m[1].'"))',
            $content
        );

        // --- 2. Tabs\Tab::make('Title') and Tab::make('Title')
        $content = preg_replace_callback(
            '/\bTabs\\\\Tab::make\(\s*\'([A-Z][^\']+)\'\s*\)/',
            fn ($m) => "Tabs\\Tab::make(FilamentUi::text('".$m[1]."'))",
            $content
        );
        $content = preg_replace_callback(
            '/(?<![:\w])Tab::make\(\s*\'([A-Z][^\']+)\'\s*\)/',
            fn ($m) => "Tab::make(FilamentUi::text('".$m[1]."'))",
            $content
        );

        // --- 3. Fieldset::make('Title')
        $content = preg_replace_callback(
            '/\bFieldset::make\(\s*\'([A-Z][^\']+)\'\s*\)/',
            fn ($m) => "Fieldset::make(FilamentUi::text('".$m[1]."'))",
            $content
        );

        // --- 4. Wizard\Step::make('Title') and Step::make('Title')
        $content = preg_replace_callback(
            '/\bWizard\\\\Step::make\(\s*\'([A-Z][^\']+)\'\s*\)/',
            fn ($m) => "Wizard\\Step::make(FilamentUi::text('".$m[1]."'))",
            $content
        );

        // --- 5. ->label('English Label') NOT already wrapped with FilamentUi
        //     Only match lines that do NOT already contain FilamentUi
        $lines = explode("\n", $content);
        foreach ($lines as &$line) {
            if (str_contains($line, 'fos:lint-ignore-translation')) {
                continue;
            }
            if (str_contains($line, 'FilamentUi::')) {
                continue;
            }
            if (str_contains($line, '$') && str_contains($line, 'label(')) {
                continue;
            }
            if (str_contains($line, 'fn ') || str_contains($line, '__')) {
                continue;
            }

            $line = preg_replace_callback(
                '/->label\(\s*\'([A-Z][a-zA-Z ]{2,})\'\s*\)/',
                fn ($m) => "->label(FilamentUi::text('".$m[1]."'))",
                $line
            );
            $line = preg_replace_callback(
                '/->label\(\s*"([A-Z][a-zA-Z ]{2,})"\s*\)/',
                fn ($m) => '->label(FilamentUi::text("'.$m[1].'"))',
                $line
            );
        }
        unset($line);
        $content = implode("\n", $lines);

        // --- 6. Ensure FilamentUi is imported if we made changes and it's not present
        if ($content !== $original && ! str_contains($content, $filamentUiImport)) {
            // Insert after the last existing 'use' statement
            $content = preg_replace(
                '/(^use [^;]+;\s*$)/m',
                "$1\n".$filamentUiImport,
                $content,
                1  // only first match — wrong, need last. Do it differently:
            );
            // Actually: find the last use block and append after it
            $content = preg_replace(
                '/((?:^use [^;]+;\n)+)(?!use )/m',
                '$1'.$filamentUiImport."\n",
                $content,
                1
            );
        }

        if ($content !== $original) {
            file_put_contents($file, $content);
            $filesChanged++;
            $fixed += substr_count($content, 'FilamentUi::text(') - substr_count($original, 'FilamentUi::text(');
            echo '  fixed: '.str_replace(dirname(__DIR__).'/', '', $file)."\n";
        }
    }
}

echo "\n✅ Done: $fixed replacement(s) in $filesChanged file(s).\n";
