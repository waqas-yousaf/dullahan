<?php

namespace WaqasYousaf\Dulluhan\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DulluhanAdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->check()) {
            return redirect()->route('dulluhan.admin.login');
        }

        return $next($request);
    }
}
