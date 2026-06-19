<?php

declare(strict_types=1);

define('CLI_SCRIPT', true);

$moodleConfigPath = '/Users/yusuf/Herd/moodle/config.php';
$envPath = __DIR__.'/../.env';

if (! file_exists($moodleConfigPath)) {
    fwrite(STDERR, "Moodle config not found at {$moodleConfigPath}\n");
    exit(1);
}

require $moodleConfigPath;

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

$requiredFunctions = [
    'core_webservice_get_site_info',
    'core_user_create_users',
    'core_user_update_users',
    'core_user_get_users_by_field',
    'core_course_create_categories',
    'core_course_update_categories',
    'core_course_get_categories',
    'core_course_create_courses',
    'core_course_update_courses',
    'core_course_get_courses_by_field',
    'enrol_manual_enrol_users',
    'enrol_manual_unenrol_users',
    'core_cohort_create_cohorts',
    'core_cohort_add_cohort_members',
    'core_cohort_get_cohorts',
    'core_cohort_search_cohorts',
    'core_calendar_create_calendar_events',
    'gradereport_overview_get_course_grades',
    'core_completion_get_course_completion_status',
    'core_completion_get_activities_completion_status',
    'mod_attendance_get_courses_with_today_sessions',
    'mod_attendance_get_user_absences',
];

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

$prepare = static function (mysqli $db, string $sql): mysqli_stmt {
    $statement = $db->prepare($sql);
    if (! $statement) {
        fwrite(STDERR, "Prepare failed: {$db->error}\nSQL: {$sql}\n");
        exit(1);
    }

    return $statement;
};

$prefix = $CFG->prefix;

$tokenSql = "SELECT id, userid, externalserviceid
    FROM {$prefix}external_tokens
    WHERE token = ?
    LIMIT 1";
$tokenStatement = $prepare($mysqli, $tokenSql);

$tokenStatement->bind_param('s', $token);
$tokenStatement->execute();
$tokenResult = $tokenStatement->get_result();
$tokenRow = $tokenResult->fetch_assoc();

if (! $tokenRow) {
    fwrite(STDERR, "Token not found in Moodle external_tokens\n");
    exit(1);
}

$serviceId = (int) $tokenRow['externalserviceid'];
$userId = (int) $tokenRow['userid'];

if ($serviceId <= 0) {
    fwrite(STDERR, "Token is not linked to an external service\n");
    exit(1);
}

$insertedFunctions = 0;
$serviceFunctionColumns = [];
$serviceFunctionColumnResult = $mysqli->query("SHOW COLUMNS FROM {$prefix}external_services_functions");
while ($serviceFunctionColumnResult && ($column = $serviceFunctionColumnResult->fetch_assoc())) {
    $serviceFunctionColumns[] = (string) ($column['Field'] ?? '');
}

$useFunctionIdColumn = in_array('functionnameid', $serviceFunctionColumns, true);
$useFunctionNameColumn = in_array('functionname', $serviceFunctionColumns, true);

if (! $useFunctionIdColumn && ! $useFunctionNameColumn) {
    fwrite(STDERR, "Unsupported schema for {$prefix}external_services_functions (missing functionnameid/functionname)\n");
    exit(1);
}

foreach ($requiredFunctions as $functionName) {
    $functionSql = "SELECT id FROM {$prefix}external_functions WHERE name = ? LIMIT 1";
    $functionStatement = $prepare($mysqli, $functionSql);
    $functionStatement->bind_param('s', $functionName);
    $functionStatement->execute();
    $functionResult = $functionStatement->get_result();
    $functionRow = $functionResult->fetch_assoc();

    if (! $functionRow) {
        fwrite(STDERR, "Function not found in Moodle core: {$functionName}\n");

        continue;
    }

    $functionId = (int) $functionRow['id'];

    if ($useFunctionIdColumn) {
        $existsSql = "SELECT id
            FROM {$prefix}external_services_functions
            WHERE externalserviceid = ? AND functionnameid = ?
            LIMIT 1";
        $existsStatement = $prepare($mysqli, $existsSql);
        $existsStatement->bind_param('ii', $serviceId, $functionId);
    } else {
        $existsSql = "SELECT id
            FROM {$prefix}external_services_functions
            WHERE externalserviceid = ? AND functionname = ?
            LIMIT 1";
        $existsStatement = $prepare($mysqli, $existsSql);
        $existsStatement->bind_param('is', $serviceId, $functionName);
    }
    $existsStatement->execute();
    $existsResult = $existsStatement->get_result();

    if ($existsResult->fetch_assoc()) {
        continue;
    }

    if ($useFunctionIdColumn) {
        $insertSql = "INSERT INTO {$prefix}external_services_functions (externalserviceid, functionnameid)
            VALUES (?, ?)";
        $insertStatement = $prepare($mysqli, $insertSql);
        $insertStatement->bind_param('ii', $serviceId, $functionId);
    } else {
        $insertSql = "INSERT INTO {$prefix}external_services_functions (externalserviceid, functionname)
            VALUES (?, ?)";
        $insertStatement = $prepare($mysqli, $insertSql);
        $insertStatement->bind_param('is', $serviceId, $functionName);
    }
    $insertStatement->execute();
    $insertedFunctions++;
}

$serviceSql = "SELECT restrictedusers FROM {$prefix}external_services WHERE id = ? LIMIT 1";
$serviceStatement = $prepare($mysqli, $serviceSql);
$serviceStatement->bind_param('i', $serviceId);
$serviceStatement->execute();
$serviceResult = $serviceStatement->get_result();
$serviceRow = $serviceResult->fetch_assoc();
$restrictedUsers = (int) ($serviceRow['restrictedusers'] ?? 0);

$serviceUserLinked = false;

if ($restrictedUsers === 1) {
    $serviceUserSql = "SELECT id
        FROM {$prefix}external_services_users
        WHERE externalserviceid = ? AND userid = ?
        LIMIT 1";
    $serviceUserStatement = $prepare($mysqli, $serviceUserSql);
    $serviceUserStatement->bind_param('ii', $serviceId, $userId);
    $serviceUserStatement->execute();
    $serviceUserResult = $serviceUserStatement->get_result();

    if (! $serviceUserResult->fetch_assoc()) {
        $columns = [];
        $columnQuery = "SHOW COLUMNS FROM {$prefix}external_services_users";
        $columnResult = $mysqli->query($columnQuery);
        while ($columnResult && ($column = $columnResult->fetch_assoc())) {
            $columns[] = (string) ($column['Field'] ?? '');
        }

        $insertColumns = ['externalserviceid', 'userid'];
        $insertValues = [$serviceId, $userId];
        $types = 'ii';

        if (in_array('iprestriction', $columns, true)) {
            $insertColumns[] = 'iprestriction';
            $insertValues[] = '';
            $types .= 's';
        }

        if (in_array('validuntil', $columns, true)) {
            $insertColumns[] = 'validuntil';
            $insertValues[] = 0;
            $types .= 'i';
        }

        $now = time();
        if (in_array('timecreated', $columns, true)) {
            $insertColumns[] = 'timecreated';
            $insertValues[] = $now;
            $types .= 'i';
        }

        if (in_array('timemodified', $columns, true)) {
            $insertColumns[] = 'timemodified';
            $insertValues[] = $now;
            $types .= 'i';
        }

        if (in_array('creatorid', $columns, true)) {
            $insertColumns[] = 'creatorid';
            $insertValues[] = 0;
            $types .= 'i';
        }

        $placeholders = implode(', ', array_fill(0, count($insertColumns), '?'));
        $insertServiceUserSql = "INSERT INTO {$prefix}external_services_users
            (".implode(', ', $insertColumns).")
            VALUES ({$placeholders})";
        $insertServiceUserStatement = $prepare($mysqli, $insertServiceUserSql);

        $params = [];
        $params[] = &$types;
        foreach ($insertValues as $index => $value) {
            $params[] = &$insertValues[$index];
        }

        call_user_func_array([$insertServiceUserStatement, 'bind_param'], $params);
        $insertServiceUserStatement->execute();
    }

    $serviceUserLinked = true;
}

echo json_encode([
    'service_id' => $serviceId,
    'user_id' => $userId,
    'inserted_functions' => $insertedFunctions,
    'restrictedusers' => $restrictedUsers,
    'service_user_linked' => $serviceUserLinked,
], JSON_PRETTY_PRINT).PHP_EOL;
