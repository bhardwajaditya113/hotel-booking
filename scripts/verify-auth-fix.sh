#!/bin/bash

# Production Auth Fix - Verification and Deployment Script
# This script verifies and applies the permanent auth fix for production

set -e

echo "================================"
echo "Production Auth Fix - Verification"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Check 1: Verify Laravel is installed
if [ ! -f "artisan" ]; then
    print_error "Laravel artisan not found. Please run from Laravel root directory."
    exit 1
fi
print_status "Laravel installation found"

# Check 2: Verify environment file
if [ ! -f ".env" ]; then
    print_error ".env file not found"
    exit 1
fi
print_status ".env file found"

# Check 3: Display current session configuration
echo ""
echo "Current Session Configuration:"
echo "=============================="

if grep -q "SESSION_DRIVER=" .env; then
    SESSION_DRIVER=$(grep "SESSION_DRIVER=" .env | cut -d '=' -f2)
    echo "SESSION_DRIVER: $SESSION_DRIVER"
else
    echo "SESSION_DRIVER: (using default - file)"
fi

if grep -q "CACHE_DRIVER=" .env; then
    CACHE_DRIVER=$(grep "CACHE_DRIVER=" .env | cut -d '=' -f2)
    echo "CACHE_DRIVER: $CACHE_DRIVER"
else
    echo "CACHE_DRIVER: (using default - file)"
fi

if grep -q "SESSION_ENCRYPT=" .env; then
    SESSION_ENCRYPT=$(grep "SESSION_ENCRYPT=" .env | cut -d '=' -f2)
    echo "SESSION_ENCRYPT: $SESSION_ENCRYPT"
else
    echo "SESSION_ENCRYPT: (using default - false)"
fi

echo ""

# Check 4: Verify environment is production or staging
APP_ENV=$(grep "APP_ENV=" .env | cut -d '=' -f2 || echo "local")
if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    print_warning "Environment: $APP_ENV (fixes needed)"
else
    print_status "Environment: $APP_ENV (local)"
fi

echo ""
echo "Database Verification:"
echo "====================="

# Check 5: Test database connection
if php artisan migrate:status > /dev/null 2>&1; then
    print_status "Database connection: OK"
else
    print_error "Database connection: FAILED"
    echo "Please verify DATABASE_* environment variables in .env"
    exit 1
fi

# Check 6: Check if migrations are current
if php artisan migrate:status 2>/dev/null | grep -q "Pending"; then
    print_warning "Pending migrations detected"
else
    print_status "All migrations applied"
fi

# Check 7: Verify cache table exists (required for database cache driver)
echo ""
echo "Cache Configuration:"
echo "===================="

if php artisan tinker --execute="exit(\DB::table('INFORMATION_SCHEMA.TABLES')->where('TABLE_NAME', 'cache')->exists() ? 0 : 1);" 2>/dev/null; then
    print_status "Cache table exists"
else
    print_warning "Cache table does not exist (needed for CACHE_DRIVER=database)"
    echo "You can create it with: php artisan cache:table && php artisan migrate"
fi

echo ""
echo "===== RECOMMENDATIONS ====="
echo ""

if [ "$CACHE_DRIVER" != "database" ]; then
    echo "1. Update CACHE_DRIVER to 'database' for production:"
    echo "   CACHE_DRIVER=database"
fi

if [ "$SESSION_DRIVER" != "cookie" ]; then
    echo "2. Update SESSION_DRIVER to 'cookie' for containerized production:"
    echo "   SESSION_DRIVER=cookie"
fi

if [ "$SESSION_ENCRYPT" != "true" ]; then
    echo "3. Enable session encryption for security:"
    echo "   SESSION_ENCRYPT=true"
fi

echo ""
echo "Once you've updated .env with recommended values, run:"
echo "  php artisan cache:table (if cache table doesn't exist)"
echo "  php artisan migrate"
echo "  php artisan cache:clear"
echo "  php artisan config:clear"
echo ""
echo "Then test login: /login"
echo ""

