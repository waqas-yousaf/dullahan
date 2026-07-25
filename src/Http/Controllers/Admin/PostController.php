<?php

namespace YourVendor\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use YourVendor\Dulluhan\Http\Requests\StorePostRequest;
use YourVendor\Dulluhan\Models\Post;

class PostController extends Controller
{
    public function index(): View
    {
        return view('dulluhan::admin.posts.index', [
            'posts' => Post::query()
                ->with('author')
                ->latest()
                ->paginate(config('dulluhan.pagination.admin_posts_per_page', 15)),
        ]);
    }

    public function create(): View
    {
        return view('dulluhan::admin.posts.form', [
            'post' => new Post(['status' => 'draft']),
            'action' => route('dulluhan.admin.posts.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = new Post($request->validated());
        $post->author_id = Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->id();
        $post->save();

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('dulluhan::admin.posts.form', [
            'post' => $post,
            'action' => route('dulluhan.admin.posts.update', $post),
            'method' => 'PUT',
        ]);
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        $post->fill($request->validated())->save();

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('dulluhan.admin.posts.index')->with('status', 'Post deleted.');
    }
}
