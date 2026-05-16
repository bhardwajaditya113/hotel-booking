#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

export PLAYWRIGHT_BASE_URL="${PLAYWRIGHT_BASE_URL:-http://127.0.0.1:8765}"
export DB_CONNECTION=sqlite
export DB_DATABASE="${DB_DATABASE:-$(pwd)/database/e2e.sqlite}"

if [[ "${E2E_SKIP_PREP:-}" != "1" ]]; then
  bash scripts/e2e-prep.sh
fi

npx playwright test "$@"
