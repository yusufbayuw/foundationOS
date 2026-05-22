<?php

/**
 * Sprint 4 auto-fixer: routes hardcoded helperText() strings through FilamentUi::text().
 *
 * Usage: php scripts/fix-helper-texts.php [path ...]
 */
$paths = array_slice($argv, 1);
if (empty($paths)) {
    echo "Usage: php scripts/fix-helper-texts.php <path> [path ...]\n";
    exit(1);
}

$filamentUiImport = 'use Modules\\Core\\Support\\FilamentUi;';

$replacements = [
    "->helperText('Opsional. Kosongkan untuk data tenant-wide.')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide data.'))",
    "->helperText('Opsional. Kosongkan untuk stock take tenant-wide.')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide stock takes.'))",
    "->helperText('Opsional. Kosongkan untuk denda tenant-wide.')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide fines.'))",
    "->helperText('Opsional. Kosongkan untuk member tenant-wide (lintas organisasi).')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide members.'))",
    "->helperText('Opsional. Kosongkan untuk transaksi tenant-wide.')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide transactions.'))",
    "->helperText('Opsional. Kosongkan untuk copy tenant-wide.')" => "->helperText(FilamentUi::text('Optional. Leave blank for tenant-wide copies.'))",
    "->helperText('Kosongkan agar policy berlaku tenant-wide.')" => "->helperText(FilamentUi::text('Leave blank for tenant-wide policy.'))",
    "->helperText('Kosongkan untuk kebijakan perpustakaan tenant-wide (terpusat).')" => "->helperText(FilamentUi::text('Leave blank for the tenant-wide library policy.'))",
    "->helperText('Kosongkan untuk kategori tenant-wide (terpusat).')" => "->helperText(FilamentUi::text('Leave blank for tenant-wide categories.'))",
    "->helperText('Opsional. Jika diisi, nama penerbit akan mengikuti master data.')" => "->helperText(FilamentUi::text('Optional. If filled, publisher name follows master data.'))",
    "->helperText('Opsional. Pilih penulis dari master data untuk konsistensi laporan.')" => "->helperText(FilamentUi::text('Optional. Select authors from master data for report consistency.'))",
    "->helperText('Opsional. Pilih topik/subject untuk klasifikasi laporan.')" => "->helperText(FilamentUi::text('Optional. Select topics for report classification.'))",
    "->helperText('Opsional. Jika diisi, kebijakan peminjaman mengikuti tipe member.')" => "->helperText(FilamentUi::text('Optional. If filled, loan policy follows member type.'))",
    "->helperText('Opsional. Isi jika lokasi belum terdaftar di master data.')" => "->helperText(FilamentUi::text('Optional. Fill if location is not registered in master data.'))",
    "->helperText('Pisahkan penulis dengan koma.')" => "->helperText(FilamentUi::text('Separate authors with commas.'))",
    "->helperText('Pisahkan kata kunci dengan koma.')" => "->helperText(FilamentUi::text('Separate keywords with commas.'))",
    "->helperText('Isi jika judul ini berjenis serial/jurnal.')" => "->helperText(FilamentUi::text('Fill if this title is a serial or journal.'))",
    "->helperText('General Material Designation (GMD).')" => "->helperText(FilamentUi::text('General Material Designation (GMD).'))",
];

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

        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        if ($content !== $original && ! str_contains($content, $filamentUiImport)) {
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
            $count = substr_count($content, 'FilamentUi::text(') - substr_count($original, 'FilamentUi::text(');
            $fixed += $count;
            echo '  fixed: '.str_replace(dirname(__DIR__).'/', '', $file)."\n";
        }
    }
}

echo "\n✅ Done: $fixed replacement(s) in $filesChanged file(s).\n";
