<?php

$files = array_merge(
    glob(__DIR__.'/../Modules/*/app/Filament/Resources/*/Schemas/*.php'),
    glob(__DIR__.'/../Modules/*/app/Filament/Resources/*/Tables/*.php'),
);

foreach ($files as $file) {
    $code = file_get_contents($file);

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

    $lines = preg_split('/\\R/', $code) ?: [];
    $output = [];
    $pendingLabel = null;

    foreach ($lines as $line) {
        if (preg_match('/^\s*->label\(/', $line) === 1) {
            $pendingLabel = $line;

            continue;
        }

        if ($pendingLabel !== null) {
            $trimmedLine = ltrim($line);

            if (
                preg_match("/^[A-Za-z\\\\]+::make\\('/", $trimmedLine) === 1
                || preg_match('/^\\]\\s*,?$/', trim($line)) === 1
            ) {
                if (! str_ends_with(trim($pendingLabel), ',')) {
                    $pendingLabel .= ',';
                }
            }

            $output[] = $pendingLabel;
            $pendingLabel = null;
        }

        $output[] = $line;
    }

    if ($pendingLabel !== null) {
        $output[] = $pendingLabel;
    }

    $normalized = implode("\n", $output);
    $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);

    if ($normalized !== $code) {
        file_put_contents($file, $normalized);
    }
}
