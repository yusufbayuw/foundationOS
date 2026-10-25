#!/usr/bin/env bash
# Read-only FoundationOS runtime contract checks. Never runs jobs, migrations or restores.
# Requires explicit app path to prevent accidental inspection of the wrong deployment.
set -euo pipefail
umask 077

if [[ "${1:-}" == "--help" ]]; then
  echo "Usage: FOS_PREFLIGHT_APP_DIR=/verified/release/path [FOS_PREFLIGHT_PHP_BIN=/verified/php] bash scripts/ops/preflight-readonly.sh"
  echo "Inspects Laravel boot, configuration policy, registered /up, scheduler and queue commands."
  echo "Does NOT verify worker processes, cron ticks, database liveness, backup retention or recovery."
  exit 0
fi

if [[ -z "${FOS_PREFLIGHT_APP_DIR:-}" ]]; then
  echo "[FAIL] Explicit FOS_PREFLIGHT_APP_DIR is required; no implicit working directory." >&2
  exit 2
fi
APP_DIR=$(realpath -e -- "$FOS_PREFLIGHT_APP_DIR") || {
  echo "[FAIL] Application path is not accessible." >&2
  exit 2
}
if [[ ! -f "$APP_DIR/artisan" || ! -f "$APP_DIR/bootstrap/app.php" || ! -f "$APP_DIR/vendor/autoload.php" ]]; then
  echo "[FAIL] Laravel application and installed dependencies were not found." >&2
  exit 2
fi

PHP_BIN="${FOS_PREFLIGHT_PHP_BIN:-php}"
if [[ "$PHP_BIN" != */* ]]; then
  PHP_BIN=$(command -v -- "$PHP_BIN") || {
    echo "[FAIL] PHP CLI is unavailable." >&2
    exit 2
  }
fi
if [[ ! -x "$PHP_BIN" ]]; then
  echo "[FAIL] Selected PHP executable is not executable." >&2
  exit 2
fi

TMP=$(mktemp -d)
trap 'rm -rf -- "$TMP"' EXIT
cd -- "$APP_DIR"
FAILED=0

check() {
  local label="$1"
  shift
  if "$@" > "$TMP/last-stdout" 2> "$TMP/last-stderr"; then
    echo "[PASS] $label"
  else
    echo "[FAIL] $label (details retained locally; not printed to avoid secret leakage)" >&2
    FAILED=1
  fi
}

# Do not leak APP_KEY, database DSN, .env, cookie material or exception traces.
check "PHP CLI 8.4" "$PHP_BIN" -r 'exit(PHP_VERSION_ID >= 80400 && PHP_VERSION_ID < 80500 ? 0 : 1);'

check "Laravel boot and non-secret config policies" "$PHP_BIN" -r '
  require $argv[1]."/vendor/autoload.php";
  $app = require $argv[1]."/bootstrap/app.php";
  $app->make(\\Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
  $environment = (string) config("app.env");
  $production = $environment === "production";
  $queue = (string) config("queue.default");
  $supported = ["database", "redis", "sqs", "beanstalkd", "failover", "sync", "deferred", "background", "null"];
  if (!in_array($queue, $supported, true) || $queue === "") exit(1);
  if ($production && (bool) config("app.debug")) exit(2);
  if ($production && in_array($queue, ["sync", "null", "deferred", "background"], true)) exit(3);
  if ($production && !str_starts_with((string) config("app.url"), "https://")) exit(4);
  if ($production && (string) config("app.key") === "") exit(5);
  if (in_array($queue, ["database", "redis", "beanstalkd"], true)
      && (int) config("queue.connections.".$queue.".retry_after") <= 0) exit(6);
  exit(0);
' "$APP_DIR"

check "GET /up route registered" bash -c '
  "$1" artisan route:list --json > "$2/routes.json" 2> "$2/route-error" || exit 1
  "$1" -r '\''
    $routes = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($routes)) exit(1);
    foreach ($routes as $route) {
      if (trim((string) ($route["uri"] ?? ""), "/") === "up"
          && in_array("GET", explode("|", (string) ($route["method"] ?? "")), true)) exit(0);
    }
    exit(1);
  '\'' "$2/routes.json"
' _ "$PHP_BIN" "$TMP"

check "Scheduled task registrations (no tasks executed)" bash -c '
  "$1" artisan schedule:list --no-ansi > "$2/schedule.txt" 2> "$2/schedule-error" || exit 1
  grep -Fq "fos:moodle:drain-outbox" "$2/schedule.txt" &&
    grep -Fq "helpdesk:check-sla" "$2/schedule.txt" &&
    grep -Fq "workflow:escalate-overdue" "$2/schedule.txt"
' _ "$PHP_BIN" "$TMP"

check "Queue worker command registered (not started)" "$PHP_BIN" artisan queue:work --help --no-ansi
check "Failed-queue inspection command registered (not executed)" "$PHP_BIN" artisan queue:failed --help --no-ansi
check "Encrypted snapshot tooling shell syntax" bash -n scripts/ops/snapshot-local-encrypted.sh scripts/ops/verify-encrypted-snapshot.sh

echo "[UNVERIFIED] Real HTTPS/ingress, queue backlog/worker health, scheduler ticks, failed jobs and alerts."
echo "[UNVERIFIED] Live database/file consistency, offsite backup, key escrow, retention, and isolated staging restore."
echo "[UNVERIFIED] RPO/RTO measurements and cross-tenant production-shaped staging UAT."
echo "[UNVERIFIED] APP_KEY historical reuse requires authorized private MATCH/NO MATCH check (issue #122)."
echo "[UNVERIFIED] Security audit CVE gate (issue #121) requires separate CI verification."
if (( FAILED != 0 )); then
  echo "[RESULT] Local runtime contract FAIL; release must remain NO-GO." >&2
  exit 1
fi
echo "[RESULT] Local runtime contract PASS only; NOT an operational production GO."
