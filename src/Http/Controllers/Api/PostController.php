<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use WaqasYousaf\Dulluhan\Models\Post;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::query()
            ->with(['author', 'categories'])
            ->published()
            ->when(request('type'), fn ($query, string $type) => $query->where('post_type', $type))
            ->when(request('category'), function ($query, string $category): void {
                $query->whereHas('categories', fn ($query) => $query->where('slug', $category));
            })
            ->latest('published_at')
            ->paginate(config('dulluhan.pagination.posts_per_page', 12));

        $posts->through(fn (Post $post) => $this->postPayload($post));

        return response()->json($posts);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->with(['author', 'categories'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $this->postPayload($post)]);
    }

    private function postPayload(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'post_type' => $post->post_type,
            'excerpt' => \Illuminate\Support\Str::limit(strip_tags($post->content), 150),
            'content' => $post->content,
            'status' => $post->status,
            'featured_image' => $post->featured_image,
            'published_at' => $post->published_at,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'categories' => $post->categories,
            'seo' => $post->seoOptions(),
            'author_box' => $post->author?->authorBox(),
        ];
    }
}
