<?php
declare(strict_types=1);

// Proof-of-concept only: derive all shard selections from existing PHPUnit XML.
// No tests are excluded; all emitted files stay in repository root so paths resolve.
if ($argc !== 3 || !ctype_digit($argv[1]) || !ctype_digit($argv[2])) {
    fwrite(STDERR, "Usage: php scripts/ci/phpunit-shard.php <shards> <index>\n");
    exit(2);
}
$count = (int) $argv[1];
$index = (int) $argv[2];
if ($count < 2 || $count > 8 || $index >= $count) {
    throw new RuntimeException('Invalid shard count/index');
}
$root = dirname(__DIR__, 2);
$xml = new DOMDocument();
if (!$xml->load($root . '/phpunit.coverage.xml', LIBXML_NONET)) {
    throw new RuntimeException('Unable to parse source PHPUnit config');
}
$xp = new DOMXPath($xml);
$suites = $xp->query('/phpunit/testsuites/testsuite');
$names = [];
$files = [];
foreach ($suites as $suite) {
    $names[] = $suite->getAttribute('name');
    foreach ($suite->childNodes as $selector) {
        if (!$selector instanceof DOMElement) continue;
        $relative = trim($selector->textContent);
        $path = realpath($root . '/' . $relative);
        if ($path === false || !str_starts_with($path, $root . '/')) {
            throw new RuntimeException("Missing/unsafe PHPUnit test selector: $relative");
        }
        $selected = [];
        if ($selector->tagName === 'file') {
            $selected[] = $path;
        } elseif ($selector->tagName === 'directory') {
            $suffix = $selector->getAttribute('suffix') ?: 'Test.php';
            if ($suffix !== 'Test.php') {
                throw new RuntimeException('Unverified PHPUnit selector suffix');
            }
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($it as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), $suffix)) {
                    $selected[] = $file->getRealPath();
                }
            }
        } else {
            throw new RuntimeException('Unsupported PHPUnit suite selector');
        }
        foreach ($selected as $absolute) {
            $file = substr($absolute, strlen($root) + 1);
            if (isset($files[$file])) {
                throw new RuntimeException("Duplicate PHPUnit test path: $file");
            }
            $files[$file] = filesize($absolute);
        }
    }
}
$required = ['Unit','Feature','Workflow','Tenant','Policy','Permission','Regression','Queue','ParallelWorkflow','ImportExport'];
if ($names !== $required || count($files) < $count) {
    throw new RuntimeException('Mandatory test suites are missing or too few files');
}
uksort($files, static fn($a, $b) => ($files[$b] <=> $files[$a]) ?: strcmp($a, $b));
$bins = array_fill(0, $count, []);
$weights = array_fill(0, $count, 0);
foreach ($files as $file => $size) {
    $target = array_keys($weights, min($weights), true)[0];
    $bins[$target][] = $file;
    $weights[$target] += $size;
}
if (min(array_map('count', $bins)) < 1) {
    throw new RuntimeException('Empty shard');
}
$container = $xp->query('/phpunit/testsuites')->item(0);
while ($container->hasChildNodes()) {
    $container->removeChild($container->firstChild);
}
$suite = $xml->createElement('testsuite');
$suite->setAttribute('name', 'Shard' . $index);
foreach ($bins[$index] as $file) {
    $entry = $xml->createElement('file');
    $entry->appendChild($xml->createTextNode($file));
    $suite->appendChild($entry);
}
$container->appendChild($suite);
$config = $root . '/phpunit-shard-' . $index . '.xml';
if ($xml->save($config) === false) {
    throw new RuntimeException('Could not save PHPUnit shard config');
}
$manifest = json_encode(['shards' => $bins, 'source_sha256' => hash_file('sha256', $root . '/phpunit.coverage.xml')], JSON_THROW_ON_ERROR);
file_put_contents($root . '/phpunit-shard-manifest.json', $manifest);
printf("Shard %d/%d selects %d distinct test files of %d total\n", $index + 1, $count, count($bins[$index]), count($files));