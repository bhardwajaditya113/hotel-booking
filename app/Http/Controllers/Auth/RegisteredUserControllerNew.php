<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Production-hardened user registration controller with resilient fallbacks.
 */
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Create user with all required fields for production
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user',  // Explicit default role
                'status' => 'active',  // Explicit default status
                'email_verified_at' => null,  // Explicit null
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Dispatch registered event
            event(new Registered($user));

            // Authenticate user
            Auth::login($user);

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            return redirect('/dashboard')->with('message', 'Registration successful! Welcome to Elapse.');
        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            return redirect()->back()
                ->withInput($request->except('password'))
                ->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}
