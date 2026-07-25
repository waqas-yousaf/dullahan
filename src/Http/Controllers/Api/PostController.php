<?php

namespace YourVendor\Dulluhan\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use YourVendor\Dulluhan\Models\Post;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::query()
            ->with('author')
            ->published()
            ->latest('published_at')
            ->paginate(config('dulluhan.pagination.posts_per_page', 12));

        return response()->json($posts);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $post]);
    }
}
