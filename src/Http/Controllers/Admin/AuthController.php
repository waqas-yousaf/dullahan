<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard(config('dullahan.auth.guard', 'dullahan'))->check()) {
            return redirect()->route('dullahan.admin.dashboard');
        }

        return view('dullahan::admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $this->validateRecaptcha($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard(config('dullahan.auth.guard', 'dullahan'))->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dullahan.admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard(config('dullahan.auth.guard', 'dullahan'))->logout();

        $request->session()->regenerateToken();

        return redirect()->route('dullahan.admin.login');
    }

    private function validateRecaptcha(Request $request): void
    {
        if (! config('dullahan.recaptcha.enabled', false)) {
            return;
        }

        $token = $request->input('g-recaptcha-response');
        $secret = config('dullahan.recaptcha.secret_key');

        if (! $token || ! $secret) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('Please complete the reCAPTCHA challenge.'),
            ]);
        }

        $response = Http::asForm()->post(config('dullahan.recaptcha.verify_url'), [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        $payload = $response->json();
        $version = config('dullahan.recaptcha.version', 'v2');
        $minimumScore = (float) config('dullahan.recaptcha.minimum_score', 0.5);

        if (! $response->ok() || ! ($payload['success'] ?? false)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('reCAPTCHA verification failed.'),
            ]);
        }

        if ($version === 'v3' && (float) ($payload['score'] ?? 0) < $minimumScore) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('reCAPTCHA score was too low.'),
            ]);
        }
    }
}
