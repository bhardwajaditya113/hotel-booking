<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Production auth diagnostics for troubleshooting login/registration failures.
 * Accessible only in non-production or for admins.
 */
class AuthDiagnosticsController
{
    public function check()
    {
        if (!app()->environment('production') && !Auth::check()) {
            // Allow in dev or if not authenticated
        } elseif (Auth::check() && Auth::user()->role === 'admin') {
            // Allow admins in prod
        } else {
            abort(403);
        }

        $diagnostics = [];
        
        // 1. Database connection
        try {
            DB::connection()->getPdo();
            $diagnostics['database'] = ['status' => 'OK', 'connection' => DB::connection()->getName()];
        } catch (\Exception $e) {
            $diagnostics['database'] = ['status' => 'FAILED', 'error' => $e->getMessage()];
        }
        
        // 2. Users table schema
        try {
            $schema = DB::getSchemaBuilder();
            if ($schema->hasTable('users')) {
                $columns = $schema->getColumnListing('users');
                $required = ['id', 'name', 'email', 'password', 'role', 'status'];
                $missing = array_diff($required, $columns);
                $diagnostics['users_table'] = [
                    'status' => empty($missing) ? 'OK' : 'MISSING_COLUMNS',
                    'columns' => $columns,
                    'missing' => $missing,
                ];
            } else {
                $diagnostics['users_table'] = ['status' => 'MISSING'];
            }
        } catch (\Exception $e) {
            $diagnostics['users_table'] = ['status' => 'ERROR', 'error' => $e->getMessage()];
        }
        
        // 3. User count
        try {
            $count = User::count();
            $diagnostics['users_count'] = $count;
        } catch (\Exception $e) {
            $diagnostics['users_count'] = ['error' => $e->getMessage()];
        }
        
        // 4. Sessions config
        $diagnostics['session'] = [
            'driver' => config('session.driver'),
            'lifetime' => config('session.lifetime'),
            'secure' => config('session.secure'),
            'http_only' => config('session.http_only'),
        ];
        
        // 5. Auth config
        $diagnostics['auth'] = [
            'default_guard' => config('auth.defaults.guard'),
            'default_provider' => config('auth.defaults.provider'),
        ];
        
        return response()->json($diagnostics);
    }

    /**
     * Test registration flow without actually creating a user.
     */
    public function testRegistration(Request $request)
    {
        if (!app()->environment('production') && !Auth::check()) {
            // Allow in dev
        } elseif (Auth::check() && Auth::user()->role === 'admin') {
            // Allow admins
        } else {
            abort(403);
        }

        $test = $request->input('test', 'validation');
        $results = [];
        
        if ($test === 'validation' || $test === 'all') {
            // Validate test data
            $data = [
                'name' => 'Test User ' . time(),
                'email' => 'test-' . time() . '@example.com',
                'password' => 'TestPassword123!',
            ];
            
            $validator = validator($data, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
            ]);
            
            $results['validation'] = [
                'passes' => $validator->passes(),
                'errors' => $validator->errors()->all(),
            ];
        }
        
        if ($test === 'db_write' || $test === 'all') {
            // Test database write
            try {
                $testEmail = 'db-test-' . time() . '@example.com';
                $user = User::create([
                    'name' => 'DB Test User',
                    'email' => $testEmail,
                    'password' => bcrypt('TestPassword123!'),
                    'role' => 'user',
                    'status' => 'active',
                ]);
                
                $results['db_write'] = [
                    'status' => 'OK',
                    'user_id' => $user->id,
                ];
                
                // Cleanup
                $user->delete();
            } catch (\Exception $e) {
                $results['db_write'] = [
                    'status' => 'FAILED',
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return response()->json($results);
    }
}
