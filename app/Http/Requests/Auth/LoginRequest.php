<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $rawLogin = trim((string) $this->input('login'));
        $plainPassword = trim((string) $this->input('password'));

        // Prefer email, then phone, then display name so `.first()` never picks the wrong row
        // when multiple identifiers could match (same message as wrong password otherwise).
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower($rawLogin)])
            ->first()
            ?? User::query()->where('phone', $rawLogin)->first()
            ?? User::query()->where('name', $rawLogin)->first();

        $hash = $user ? $user->getRawOriginal('password') : null;

        if (! $user || ! is_string($hash) || ! Hash::check($plainPassword, $hash)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        // Must match POST /admin/login even when routeIs() is unreliable (same as AuthenticatedSessionController).
        $postedToAdminLogin = $this->isMethod('POST')
            && ($this->routeIs('admin.login.store')
                || $this->is('admin/login')
                || str_ends_with(trim($this->path(), '/'), 'admin/login'));

        if ($postedToAdminLogin && strtolower(trim((string) $user->role)) !== 'admin') {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower(trim((string) $this->input('login'))).'|'.$this->ip());
    }
}
