#!/bin/bash

# Production Auth Fix - Verification Script
# Run this after Railway deployment completes to verify auth is working

set -e

PROD_URL="https://www.infinityloop.online"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="password"

echo "╔════════════════════════════════════════════════════════════════════════════╗"
echo "║           PRODUCTION AUTH FIX - VERIFICATION SCRIPT                        ║"
echo "║                                                                            ║"
echo "║  Run this script after Railway deployment completes to verify auth works.  ║"
echo "╚════════════════════════════════════════════════════════════════════════════╝"
echo ""

# Test 1: Login page loads
echo "TEST 1: Checking login page..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$PROD_URL/login")
if [ "$RESPONSE" = "200" ]; then
    echo "  ✅ Login page loads (HTTP $RESPONSE)"
else
    echo "  ❌ Login page failed (HTTP $RESPONSE)"
    exit 1
fi
echo ""

# Test 2: Register page loads
echo "TEST 2: Checking registration page..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$PROD_URL/register")
if [ "$RESPONSE" = "200" ]; then
    echo "  ✅ Registration page loads (HTTP $RESPONSE)"
else
    echo "  ❌ Registration page failed (HTTP $RESPONSE)"
    exit 1
fi
echo ""

# Test 3: Admin login page loads
echo "TEST 3: Checking admin login page..."
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "$PROD_URL/admin/login")
if [ "$RESPONSE" = "200" ]; then
    echo "  ✅ Admin login page loads (HTTP $RESPONSE)"
else
    echo "  ❌ Admin login page failed (HTTP $RESPONSE)"
    exit 1
fi
echo ""

# Test 4: Check if CSRF tokens are being generated
echo "TEST 4: Checking CSRF token generation..."
LOGIN_PAGE=$(curl -s "$PROD_URL/login")
CSRF_TOKEN=$(echo "$LOGIN_PAGE" | grep -o 'csrf-token" content="[^"]*' | cut -d'"' -f3)
if [ ! -z "$CSRF_TOKEN" ]; then
    echo "  ✅ CSRF token generated: ${CSRF_TOKEN:0:20}..."
else
    echo "  ❌ CSRF token not found"
    exit 1
fi
echo ""

# Test 5: Check if sessions are being created
echo "TEST 5: Checking session creation..."
SESSION_HEADER=$(curl -s -i "$PROD_URL/login" | grep -i "set-cookie.*laravel_session" | head -1)
if [ ! -z "$SESSION_HEADER" ]; then
    echo "  ✅ Session cookie being set"
    echo "     $SESSION_HEADER" | head -c 60
    echo "..."
else
    echo "  ❌ Session cookie not found"
    exit 1
fi
echo ""

# Test 6: Database check (if you have access)
echo "TEST 6: Checking if sessions table exists..."
echo "  ⓘ To verify sessions are being stored:"
echo "    Query: SELECT COUNT(*) FROM sessions;"
echo "    In Railway dashboard → PostgreSQL → Database console"
echo ""

echo "╔════════════════════════════════════════════════════════════════════════════╗"
echo "║                          VERIFICATION COMPLETE                             ║"
echo "║                                                                            ║"
echo "║  ✅ All basic checks passed!                                              ║"
echo "║                                                                            ║"
echo "║  Next Steps:                                                               ║"
echo "║  1. Open browser and go to: $PROD_URL/login                  ║"
echo "║  2. Login with: $ADMIN_EMAIL                                 ║"
echo "║  3. Should redirect to /dashboard (not show 419 error)                     ║"
echo "║  4. If successful: Auth is FIXED! ✅                                       ║"
echo "║  5. If failed: Check Railway logs for errors                               ║"
echo "║                                                                            ║"
echo "╚════════════════════════════════════════════════════════════════════════════╝"
