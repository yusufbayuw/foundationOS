<?php

declare(strict_types=1);

$moodleConfigPath = '/Users/yusuf/Herd/moodle/config.php';

if (! file_exists($moodleConfigPath)) {
    fwrite(STDERR, "Moodle config not found at {$moodleConfigPath}\n");
    exit(1);
}

define('CLI_SCRIPT', true);
require $moodleConfigPath;

$envPath = __DIR__ . '/../.env';
$token = getenv('MOODLE_WS_TOKEN') ?: '';

if ($token === '' && file_exists($envPath)) {
    $env = file_get_contents($envPath) ?: '';

    if (preg_match('/^MOODLE_WS_TOKEN=(.*)$/m', $env, $matches)) {
        $token = trim($matches[1], "\"' \t\n\r\0\x0B");
    }
}

if ($token === '') {
    fwrite(STDERR, "MOODLE_WS_TOKEN not found in environment or .env\n");
    exit(1);
}

$mysqli = new mysqli(
    $CFG->dbhost,
    $CFG->dbuser,
    $CFG->dbpass,
    $CFG->dbname,
    (int) ($CFG->dboptions['dbport'] ?? 3306),
);

if ($mysqli->connect_error) {
    fwrite(STDERR, "DB connect error: {$mysqli->connect_error}\n");
    exit(1);
}

$prefix = $CFG->prefix;
$sql = "SELECT t.id, t.userid, t.externalserviceid, t.validuntil, t.iprestriction, t.timecreated,
            u.username,
            s.name AS service_name, s.enabled, s.restrictedusers, s.requiredcapability
        FROM {$prefix}external_tokens t
        JOIN {$prefix}user u ON u.id = t.userid
        LEFT JOIN {$prefix}external_services s ON s.id = t.externalserviceid
        WHERE t.token = ?
        LIMIT 1";

$statement = $mysqli->prepare($sql);

if (! $statement) {
    fwrite(STDERR, "Prepare failed: {$mysqli->error}\n");
    exit(1);
}

$statement->bind_param('s', $token);
$statement->execute();
$result = $statement->get_result();
$tokenRow = $result->fetch_assoc();

if (! $tokenRow) {
    echo json_encode(['token_found' => false], JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
}

$serviceId = (int) $tokenRow['externalserviceid'];
$functions = [];

if ($serviceId > 0) {
    $functionSql = "SELECT f.name
        FROM {$prefix}external_services_functions esf
        JOIN {$prefix}external_functions f ON f.id = esf.functionnameid
        WHERE esf.externalserviceid = {$serviceId}
        ORDER BY f.name";

    $functionResult = $mysqli->query($functionSql);

    if ($functionResult) {
        while ($row = $functionResult->fetch_assoc()) {
            $functions[] = $row['name'];
        }
    }
}

echo json_encode([
    'token_found' => true,
    'token' => [
        'id' => (int) $tokenRow['id'],
        'userid' => (int) $tokenRow['userid'],
        'username' => $tokenRow['username'],
        'externalserviceid' => (int) $tokenRow['externalserviceid'],
        'service_name' => $tokenRow['service_name'],
        'enabled' => (int) ($tokenRow['enabled'] ?? 0),
        'restrictedusers' => (int) ($tokenRow['restrictedusers'] ?? 0),
        'requiredcapability' => $tokenRow['requiredcapability'],
        'iprestriction' => $tokenRow['iprestriction'],
        'validuntil' => (int) $tokenRow['validuntil'],
        'timecreated' => (int) $tokenRow['timecreated'],
    ],
    'service_functions_count' => count($functions),
    'service_functions' => $functions,
], JSON_PRETTY_PRINT) . PHP_EOL;
