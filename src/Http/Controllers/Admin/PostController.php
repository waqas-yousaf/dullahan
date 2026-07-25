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
            ->with(['author', 'category']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
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

        $posts = $query->orderByDesc('id')
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
        $post = new Post($data);
        $post->save();

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('dulluhan::admin.posts.form', [
            'post' => $post->load('category'),
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
        $post->fill($data)->save();

        return redirect()->route('dulluhan.admin.posts.edit', $post)->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('dulluhan.admin.posts.index')->with('status', 'Post deleted.');
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['ID', 'Title', 'Slug', 'Post Type', 'Status', 'Author', 'Category', 'Published At', 'Created At']);
            
            Post::query()->with(['author', 'category'])->chunk(100, function ($posts) use ($handle) {
                foreach ($posts as $post) {
                    fputcsv($handle, [
                        $post->id,
                        $post->title,
                        $post->slug,
                        $post->post_type,
                        $post->status,
                        $post->author?->name ?? 'N/A',
                        $post->category?->name ?? 'N/A',
                        $post->published_at?->toIso8601String() ?? 'N/A',
                        $post->created_at->toIso8601String(),
                    ]);
                }
            });
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dulluhan-posts-' . now()->format('YmdHis') . '.csv"',
        ]);

        return $response;
    }
}
