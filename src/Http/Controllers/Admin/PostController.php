<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use WaqasYousaf\Dulluhan\Http\Requests\StorePostRequest;
use WaqasYousaf\Dulluhan\Models\Author;
use WaqasYousaf\Dulluhan\Models\Category;
use WaqasYousaf\Dulluhan\Models\Post;

class PostController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Post::query()
            ->with(['author', 'categories']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('dulluhan_categories.id', $categoryId));
        }

        if ($authorId = $request->input('author')) {
            $query->where('author_id', $authorId);
        }

        if ($postType = $request->input('type')) {
            $query->where('post_type', $postType);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $posts = $query->latest()
            ->paginate(config('dulluhan.pagination.admin_posts_per_page', 15))
            ->withQueryString();

        return view('dulluhan::admin.posts.index', [
            'posts' => $posts,
            'categories' => Category::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
            'postTypes' => config('dulluhan.post_types', ['post' => 'Post']),
        ]);
    }

    public function create(): View
    {
        return view('dulluhan::admin.posts.form', [
            'post' => new Post([
                'author_id' => Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->id(),
                'status' => 'draft',
                'post_type' => config('dulluhan.default_post_type', 'post'),
            ]),
            'action' => route('dulluhan.admin.posts.store'),
            'method' => 'POST',
            'categories' => Category::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
            'postTypes' => config('dulluhan.post_types', ['post' => 'Post']),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $post = new Post($data);
        $post->save();
        $post->categories()->sync($categoryIds);

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('dulluhan::admin.posts.form', [
            'post' => $post->load('categories'),
            'action' => route('dulluhan.admin.posts.update', $post),
            'method' => 'PUT',
            'categories' => Category::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
            'postTypes' => config('dulluhan.post_types', ['post' => 'Post']),
        ]);
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();
        $categoryIds = $data['categories'] ?? [];
        unset($data['categories']);

        $post->fill($data)->save();
        $post->categories()->sync($categoryIds);

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('dulluhan.admin.posts.index')->with('status', 'Post deleted.');
    }
}
