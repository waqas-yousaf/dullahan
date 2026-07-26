<?php

namespace WaqasYousaf\Dullahan\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DullahanAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard(config('dullahan.auth.guard', 'dullahan'))->check()) {
            return redirect()->route('dullahan.admin.login');
        }

        return $next($request);
    }
}
