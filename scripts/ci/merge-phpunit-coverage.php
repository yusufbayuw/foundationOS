<?php
declare(strict_types=1);

// Experimental PHPUnit 12 native CodeCoverage::merge, never used as a
// release gate without strict A/B comparison against monolithic coverage.
if ($argc < 5) {
    fwrite(STDERR, "Usage: php scripts/ci/merge-phpunit-coverage.php <output.clover.xml> <base.clover.xml> <part0.cov> ...\n");
    exit(2);
}
$root = dirname(__DIR__, 2);
require $root . '/vendor/autoload.php';
$output = $argv[1];
$baselineFile = $argv[2];
$inputs = array_slice($argv, 3);
$coverage = null;
foreach ($inputs as $input) {
    if (!is_file($input) || filesize($input) === 0) {
        throw new RuntimeException("Missing shard coverage: $input");
    }
    $part = require $input;
    if (!$part instanceof SebastianBergmann\CodeCoverage\CodeCoverage) {
        throw new RuntimeException("Unexpected PHPUnit coverage format: $input");
    }
    if ($coverage === null) {
        $coverage = $part;
    } else {
        $coverage->merge($part);
    }
    unset($part);
}
(new SebastianBergmann\CodeCoverage\Report\Clover())->process($coverage, $output);
$baseline = simplexml_load_file($baselineFile, 'SimpleXMLElement', LIBXML_NONET);
$merged = simplexml_load_file($output, 'SimpleXMLElement', LIBXML_NONET);
if ($baseline === false || $merged === false || !isset($baseline->project->metrics, $merged->project->metrics)) {
    throw new RuntimeException('Invalid Clover baseline or merged report');
}
foreach (['statements', 'coveredstatements'] as $metric) {
    $before = (int) $baseline->project->metrics[$metric];
    $after = (int) $merged->project->metrics[$metric];
    if ($before !== $after || ($metric === 'statements' && $before < 1)) {
        throw new RuntimeException("Coverage parity FAILED for $metric: monolithic=$before, sharded=$after");
    }
}
echo "PASS: monolithic and sharded PHPUnit covered statements match exactly\n";