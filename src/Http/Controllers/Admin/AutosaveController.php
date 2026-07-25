<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use WaqasYousaf\Dulluhan\Models\Post;

class AutosaveController extends Controller
{
    public function __invoke(Request $request, ?Post $post = null): JsonResponse
    {
        abort_unless(config('dulluhan.autosave.enabled', true), 404);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('dulluhan_posts', 'slug')->ignore($post?->getKey())],
            'author_id' => ['nullable', 'integer', 'exists:dulluhan_authors,id'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'post_type' => ['nullable', Rule::in(array_keys(config('dulluhan.post_types', ['post' => 'Post'])))],
            'featured_image' => ['nullable', 'url'],
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
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:dulluhan_categories,id'],
        ]);

        $categoryIds = $data['categories'] ?? null;
        unset($data['categories'], $data['status'], $data['published_at']);

        $post ??= new Post();
        $post->fill(array_merge([
            'title' => $post->title ?: 'Untitled Draft',
            'content' => $post->content ?: '<p><br></p>',
            'status' => 'draft',
            'post_type' => config('dulluhan.default_post_type', 'post'),
        ], array_filter($data, fn ($value) => $value !== null)));

        if (! $post->exists) {
            $post->author_id = Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->id();
        }

        $post->autosaved_at = now();
        $post->save();

        if (is_array($categoryIds)) {
            $post->categories()->sync($categoryIds);
        }

        return response()->json([
            'id' => $post->getKey(),
            'autosaved_at' => $post->autosaved_at?->toIso8601String(),
            'edit_url' => route('dulluhan.admin.posts.edit', $post),
            'update_url' => route('dulluhan.admin.posts.update', $post),
            'autosave_url' => route('dulluhan.admin.posts.autosave.existing', $post),
        ]);
    }
}
