#!/bin/bash

# Test script for Production Auth Fix
# This script tests the cookie-based session implementation locally

set -e

BLUE='\033[0;34m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}Auth Fix Testing Script${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Step 1: Verify Laravel is running or start it
echo "Step 1: Checking Laravel application..."
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} Laravel is running on port 8000"
else
    echo -e "${RED}✗${NC} Laravel is NOT running on port 8000"
    echo "To start: php artisan serve --host=127.0.0.1"
    exit 1
fi

echo ""
echo "Step 2: Verifying environment configuration..."

cd "$(dirname "$0")/.."

# Check SESSION_DRIVER
if grep -q "SESSION_DRIVER=cookie" .env; then
    echo -e "${GREEN}✓${NC} SESSION_DRIVER=cookie"
else
    echo -e "${RED}✗${NC} SESSION_DRIVER not set to cookie (current: $(grep SESSION_DRIVER .env))"
    echo "  Update .env: SESSION_DRIVER=cookie"
fi

# Check SESSION_ENCRYPT
if grep -q "SESSION_ENCRYPT=true" .env; then
    echo -e "${GREEN}✓${NC} SESSION_ENCRYPT=true"
else
    echo -e "${RED}✗${NC} SESSION_ENCRYPT not set to true"
    echo "  Update .env: SESSION_ENCRYPT=true"
fi

# Check CACHE_DRIVER
if grep -q "CACHE_DRIVER=database" .env; then
    echo -e "${GREEN}✓${NC} CACHE_DRIVER=database"
else
    echo -e "${RED}✗${NC} CACHE_DRIVER not set to database (current: $(grep CACHE_DRIVER .env))"
    echo "  Update .env: CACHE_DRIVER=database"
fi

echo ""
echo "Step 3: Verifying database..."

# Check if cache tables exist
php artisan tinker --execute="
\$hasCache = Schema::hasTable('cache');
\$hasCacheLocks = Schema::hasTable('cache_locks');
echo \$hasCache ? \"✓ cache table exists\n\" : \"✗ cache table missing\n\";
echo \$hasCacheLocks ? \"✓ cache_locks table exists\n\" : \"✗ cache_locks table missing\n\";
exit;
" 2>&1 | grep "✓\|✗"

echo ""
echo "Step 4: Testing auth flows..."
echo ""

# Test 1: Verify database has users
echo "Test 1: Database users..."
php artisan tinker --execute="
\$count = App\\Models\\User::count();
echo \"Found \$count users in database\n\";
if (\$count > 0) {
    \$user = App\\Models\\User::first();
    echo \"  - Example: {$user->email} (role: {$user->role})\n\";
}
exit;
" 2>&1

echo ""
echo "Test 2: Testing user authentication..."
php artisan tinker --execute="
\$user = App\\Models\\User::where('email', 'demo.guest@elapse.test')->first();
if (\$user) {
    echo \"✓ Demo user found: {$user->email}\n\";
    echo \"  - ID: {$user->id}\n\";
    echo \"  - Name: {$user->name}\n\";
    echo \"  - Role: {$user->role}\n\";
} else {
    echo \"✗ Demo user not found\n\";
}
exit;
" 2>&1

echo ""
echo "Test 3: Cache driver test..."
php artisan tinker --execute="
\$driver = config('cache.default');
echo \"Cache driver: \$driver\n\";
cache()->put('test_key', 'test_value', 60);
\$value = cache()->get('test_key');
if (\$value === 'test_value') {
    echo \"✓ Cache write/read successful\n\";
} else {
    echo \"✗ Cache write/read failed\n\";
}
cache()->forget('test_key');
exit;
" 2>&1

echo ""
echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}Testing Complete${NC}"
echo -e "${BLUE}================================${NC}"
echo ""
echo "Manual Testing:"
echo "1. Open browser: http://127.0.0.1:8000/login"
echo "2. Login with: demo.guest@elapse.test / password"
echo "3. Should redirect to /dashboard"
echo "4. Check DevTools → Network → Cookies tab"
echo "5. Should see encrypted session cookie with Secure, HttpOnly flags"
echo ""

