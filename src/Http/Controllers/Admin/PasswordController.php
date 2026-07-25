<?php

namespace YourVendor\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $author = Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user();

        if (! Hash::check($data['current_password'], $author->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('The current password is incorrect.'),
            ]);
        }

        $author->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $request->session()->regenerate();

        return redirect()->route('dulluhan.admin.dashboard')->with('status', 'Password changed.');
    }
}
