<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use WaqasYousaf\Dullahan\Models\Post;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dullahan::admin.dashboard', [
            'totalPosts' => Post::query()->count(),
            'publishedPosts' => Post::query()->where('status', 'published')->count(),
            'draftPosts' => Post::query()->where('status', 'draft')->count(),
            'recentPosts' => Post::query()->with('author')->orderByDesc('id')->limit(5)->get(),
            'author' => Auth::guard(config('dullahan.auth.guard', 'dullahan'))->user(),
        ]);
    }
}
