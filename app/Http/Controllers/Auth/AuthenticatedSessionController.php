<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $id = Auth::user()->id;
        $profileData = User::find($id);
        $username = $profileData->name;

        $notification = [
            'message' => 'User '.$username.' Login Successfully',
            'alert-type' => 'info',
        ];

        $role = strtolower(trim((string) $request->user()->role));

        $url = match ($role) {
            'admin' => '/admin/dashboard',
            'user' => '/dashboard',
            default => '/dashboard',
        };

        /**
         * POST /admin/login must land in /admin/* even if session url.intended is /dashboard
         * (guest middleware stores that when browsing the site before signing in).
         * Do not use redirect()->intended() here — it prefers url.intended over our default.
         */
        $postedToAdminLogin = $request->isMethod('POST')
            && ($request->routeIs('admin.login.store')
                || $request->is('admin/login')
                || str_ends_with(trim($request->path(), '/'), 'admin/login'));

        if ($postedToAdminLogin) {
            $intended = $request->session()->pull('url.intended');

            if (is_string($intended) && str_starts_with(parse_url($intended, PHP_URL_PATH) ?: '', '/admin')) {
                return redirect()->to($intended)->with($notification);
            }

            return redirect()->to('/admin/dashboard')->with($notification);
        }

        return redirect()->intended($url)->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
