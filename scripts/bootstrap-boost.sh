#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

ensure_env_value() {
    local key="$1"
    local value="$2"

    if grep -q "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        echo "${key}=${value}" >> .env
    fi
}

if [[ ! -f .env ]]; then
    cp .env.example .env
fi

ensure_env_value "APP_ENV" "local"
ensure_env_value "APP_DEBUG" "true"
ensure_env_value "DB_CONNECTION" "sqlite"

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --no-interaction --force
fi

mkdir -p database
touch database/database.sqlite

php artisan config:clear --no-interaction
php artisan migrate --force --no-interaction

if ! php artisan list boost --no-interaction > /dev/null 2>&1; then
    echo "Laravel Boost commands are not available. Check APP_ENV=local and APP_DEBUG=true." >&2
    exit 1
fi

echo "Laravel Boost bootstrap complete (APP_ENV=local, APP_DEBUG=true)."
