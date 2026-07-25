<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ApiDocumentationController extends Controller
{
    public function __invoke(): View
    {
        return view('dulluhan::admin.api.documentation', [
            'apiPrefix' => config('dulluhan.api_prefix', 'api/dulluhan'),
            'apiSecurity' => config('dulluhan.api_security', []),
        ]);
    }
}
