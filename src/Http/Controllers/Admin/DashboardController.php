<?php

namespace YourVendor\Dulluhan\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use YourVendor\Dulluhan\Models\Post;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dulluhan::admin.dashboard', [
            'totalPosts' => Post::query()->count(),
            'publishedPosts' => Post::query()->where('status', 'published')->count(),
            'draftPosts' => Post::query()->where('status', 'draft')->count(),
            'recentPosts' => Post::query()->with('author')->latest()->limit(5)->get(),
        ]);
    }
}
