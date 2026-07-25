<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use WaqasYousaf\Dulluhan\Models\Author;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(
                Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user()?->email === config('dulluhan.admin.email'),
                403,
                'Only the system administrator can manage authors.'
            );

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('dulluhan::admin.authors.index', [
            'authors' => Author::query()->withCount('posts')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('dulluhan::admin.authors.form', [
            'author' => new Author(['show_author_box' => true]),
            'action' => route('dulluhan.admin.authors.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $data['show_author_box'] = $request->boolean('show_author_box');
        $data['social_links'] = [];

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', \Illuminate\Support\Str::ascii($name)), '-');
            $filename = 'avatar-' . now()->format('YmdHis') . '-' . ($safeName ?: 'avatar') . '.' . $extension;
            $relativePath = trim(config('dulluhan.uploads.path', 'uploads/dulluhan'), '/');
            $publicPath = public_path($relativePath);

            \Illuminate\Support\Facades\File::ensureDirectoryExists($publicPath, 0755, true);
            $file->move($publicPath, $filename);

            $data['avatar'] = asset($relativePath . '/' . $filename);
        } else {
            $data['avatar'] = null;
        }

        Author::query()->create($data);

        return redirect()->route('dulluhan.admin.authors.index')->with('status', 'Author created.');
    }

    public function edit(Author $author): View
    {
        return view('dulluhan::admin.authors.form', [
            'author' => $author,
            'action' => route('dulluhan.admin.authors.update', $author),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $data = $this->validated($request, $author);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['show_author_box'] = $request->boolean('show_author_box');

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', \Illuminate\Support\Str::ascii($name)), '-');
            $filename = 'avatar-' . now()->format('YmdHis') . '-' . ($safeName ?: 'avatar') . '.' . $extension;
            $relativePath = trim(config('dulluhan.uploads.path', 'uploads/dulluhan'), '/');
            $publicPath = public_path($relativePath);

            \Illuminate\Support\Facades\File::ensureDirectoryExists($publicPath, 0755, true);
            $file->move($publicPath, $filename);

            $data['avatar'] = asset($relativePath . '/' . $filename);
        } else {
            $data['avatar'] = $author->avatar;
        }

        $author->fill($data)->save();

        return redirect()->route('dulluhan.admin.authors.index')->with('status', 'Author updated.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ((int) Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->id() === (int) $author->getKey()) {
            return redirect()->route('dulluhan.admin.authors.index')->with('status', 'You cannot delete your own author account.');
        }

        if ($author->posts()->exists()) {
            return redirect()->route('dulluhan.admin.authors.index')->with('status', 'Move this author posts before deleting the author.');
        }

        $author->delete();

        return redirect()->route('dulluhan.admin.authors.index')->with('status', 'Author deleted.');
    }

    private function validated(Request $request, ?Author $author = null): array
    {
        $mimes = implode(',', config('dulluhan.uploads.mimes', ['jpeg', 'png', 'jpg', 'webp', 'svg']));

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('dulluhan_authors', 'email')->ignore($author?->getKey())],
            'password' => [$author ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:' . $mimes, 'max:' . config('dulluhan.uploads.max_kb', 4096)],
            'website_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
            'x_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'show_author_box' => ['nullable', 'boolean'],
        ]);
    }
}
