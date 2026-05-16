#!/usr/bin/env bash
# Prepare an isolated SQLite DB with full demo seed data for Playwright E2E.
set -euo pipefail
cd "$(dirname "$0")/.."

export APP_ENV="${APP_ENV:-local}"
export DB_CONNECTION=sqlite
export DB_DATABASE="${DB_DATABASE:-$(pwd)/database/e2e.sqlite}"

rm -f "${DB_DATABASE}"
touch "${DB_DATABASE}"

php artisan migrate:fresh --seed --force

echo "E2E database ready at ${DB_DATABASE}"
