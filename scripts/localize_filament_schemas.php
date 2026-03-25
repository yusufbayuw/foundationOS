<?php

require __DIR__ . '/../vendor/autoload.php';

$basePath = dirname(__DIR__);

$componentClasses = [
    'BadgeColumn',
    'Checkbox',
    'ColorColumn',
    'DatePicker',
    'FileUpload',
    'IconColumn',
    'ImageColumn',
    'KeyValue',
    'MarkdownEditor',
    'Radio',
    'RichEditor',
    'Select',
    'TextColumn',
    'TextEntry',
    'TextInput',
    'Textarea',
    'TimePicker',
    'Toggle',
    'ToggleButtons',
    'ToggleColumn',
];

$files = array_merge(
    glob($basePath . '/Modules/*/app/Filament/Resources/*/Schemas/*.php'),
    glob($basePath . '/Modules/*/app/Filament/Resources/*/Tables/*.php'),
);

foreach ($files as $file) {
    $relativePath = ltrim(str_replace($basePath . '/', '', $file), '/');
    $code = shell_exec(sprintf('git show HEAD:%s 2>/dev/null', escapeshellarg($relativePath)));

    if (! is_string($code) || trim($code) === '') {
        $code = file_get_contents($file);
    }

    $code = str_replace(
        [
            "use Filament\\Tables\\Filters\\TrashedFilter;\n",
            "use Filament\\Actions\\ForceDeleteBulkAction;\n",
            "use Filament\\Actions\\RestoreBulkAction;\n",
        ],
        '',
        $code,
    );

    $code = preg_replace(
        '/->filters\\(\\[\\s*TrashedFilter::make\\(\\),\\s*\\]\\)/s',
        '->filters([])',
        $code,
    );

    $code = str_replace(
        [
            "ForceDeleteBulkAction::make(),\n",
            "RestoreBulkAction::make(),\n",
        ],
        '',
        $code,
    );

    $code = preg_replace_callback(
        "/->label\\('([^']*)'\\)/",
        static function (array $matches): string {
            $value = str_replace("'", "\\'", $matches[1]);

            return "->label(\\Modules\\Core\\Support\\FilamentUi::text('{$value}'))";
        },
        $code,
    );

    $lines = preg_split("/\\R/", $code) ?: [];
    $output = [];

    for ($i = 0, $count = count($lines); $i < $count; $i++) {
        $line = $lines[$i];

        if (! preg_match("/^([ \t]*)([A-Za-z\\\\]+::make\\('([^']+)'\\))(.*)$/", $line, $matches)) {
            $output[] = $line;
            continue;
        }

        [, $indent, $componentCall, $field, $rest] = $matches;
        $componentBase = preg_replace('/::.*$/', '', $matches[2]);

        if (! in_array($componentBase, $componentClasses, true)) {
            $output[] = $line;
            continue;
        }

        if (str_contains($line, '->label(')) {
            $output[] = $line;
            continue;
        }

        $trimmedRest = trim($rest);
        $nextNonEmpty = null;

        for ($j = $i + 1; $j < $count; $j++) {
            if (trim($lines[$j]) === '') {
                continue;
            }

            $nextNonEmpty = trim($lines[$j]);
            break;
        }

        $hasFollowingChain = is_string($nextNonEmpty) && str_starts_with($nextNonEmpty, '->');
        $singleLineItem = $trimmedRest === '' || $trimmedRest === ',';

        $output[] = $indent . $componentCall;

        $labelLine = $indent . '    ->label(\\Modules\\Core\\Support\\FilamentUi::field(\'' . $field . '\'))';

        if ($trimmedRest === ',') {
            $labelLine .= ',';
        } elseif ($trimmedRest !== '' && str_starts_with($trimmedRest, '->')) {
            $labelLine .= $trimmedRest;
        } elseif (! $hasFollowingChain && $singleLineItem) {
            $labelLine .= ',';
        }

        $output[] = $labelLine;
    }

    $normalized = implode("\n", $output);
    $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);

    if ($normalized !== $code) {
        file_put_contents($file, $normalized);
    }
}
