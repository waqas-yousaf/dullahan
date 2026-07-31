<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use WaqasYousaf\Dullahan\Models\Post;

class AutosaveController extends Controller
{
    public function __invoke(Request $request, ?Post $post = null): JsonResponse
    {
        abort_unless(config('dullahan.autosave.enabled', true), 404);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('dullahan_posts', 'slug')->ignore($post?->getKey())],
            'author_id' => ['nullable', 'integer', 'exists:dullahan_authors,id'],
            'content' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'post_type' => ['nullable', Rule::in(array_keys(config('dullahan.post_types', ['post' => 'Post'])))],
            'excerpt' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'url'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url'],
            'og_title' => ['nullable', 'string', 'max:95'],
            'og_description' => ['nullable', 'string', 'max:200'],
            'og_image' => ['nullable', 'url'],
            'robots' => ['nullable', Rule::in(['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'])],
            'schema_markup' => ['nullable', 'json'],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:dullahan_categories,id'],
        ]);

        unset($data['status'], $data['published_at']);

        $post ??= new Post();
        $post->fill(array_merge([
            'title' => $post->title ?: 'Untitled Draft',
            'content' => $post->content ?: '<p><br></p>',
            'status' => 'draft',
            'post_type' => config('dullahan.default_post_type', 'post'),
        ], array_filter($data, fn ($value) => $value !== null)));

        if (! $post->exists) {
            $post->author_id = Auth::guard(config('dullahan.auth.guard', 'dullahan'))->id();
        }

        $post->autosaved_at = now();
        $post->save();

        return response()->json([
            'id' => $post->getKey(),
            'autosaved_at' => $post->autosaved_at?->toIso8601String(),
            'edit_url' => route('dullahan.admin.posts.edit', $post),
            'update_url' => route('dullahan.admin.posts.update', $post),
            'autosave_url' => route('dullahan.admin.posts.autosave.existing', $post),
        ]);
    }
}
