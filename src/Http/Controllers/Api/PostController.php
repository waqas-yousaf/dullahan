<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use WaqasYousaf\Dullahan\Models\Post;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::query()
            ->with(['author', 'category'])
            ->published()
            ->when(request('type'), fn ($query, string $type) => $query->where('post_type', $type))
            ->when(request('category'), function ($query, string $category): void {
                $query->whereHas('category', fn ($query) => $query->where('slug', $category));
            })
            ->latest('published_at')
            ->paginate(config('dullahan.pagination.posts_per_page', 12));

        $posts->through(fn (Post $post) => $this->postListPayload($post));

        return response()->json($posts);
    }

    public function show(string $slug): JsonResponse
    {
        $post = Post::query()
            ->with(['author', 'category'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(['data' => $this->postDetailPayload($post)]);
    }

    private function postListPayload(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'post_type' => $post->post_type,
            'featured_image' => $post->featured_image,
            'published_at' => $post->published_at,
            'updated_at' => $post->updated_at,
            'category' => $post->category ? [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
            'author' => $post->author ? [
                'name' => $post->author->name,
                'avatar' => $post->author->avatar,
            ] : null,
        ];
    }

    private function postDetailPayload(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'post_type' => $post->post_type,
            'content' => $post->content,
            'featured_image' => $post->featured_image,
            'published_at' => $post->published_at,
            'updated_at' => $post->updated_at,
            'category' => $post->category ? [
                'id' => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
            'seo' => $post->seoOptions(),
            'author_box' => $post->author?->authorBox(),
        ];
    }
}
