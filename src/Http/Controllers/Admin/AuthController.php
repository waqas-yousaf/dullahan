<?php

namespace YourVendor\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->check()) {
            return redirect()->route('dulluhan.admin.dashboard');
        }

        return view('dulluhan::admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dulluhan.admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->logout();

        $request->session()->regenerateToken();

        return redirect()->route('dulluhan.admin.login');
    }
}
