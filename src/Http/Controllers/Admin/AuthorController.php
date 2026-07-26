<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use WaqasYousaf\Dullahan\Models\Author;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(
                Auth::guard(config('dullahan.auth.guard', 'dullahan'))->user()?->email === config('dullahan.admin.email'),
                403,
                'Only the system administrator can manage authors.'
            );

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('dullahan::admin.authors.index', [
            'authors' => Author::query()->withCount('posts')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('dullahan::admin.authors.form', [
            'author' => new Author(['show_author_box' => true]),
            'action' => route('dullahan.admin.authors.store'),
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
            $relativePath = trim(config('dullahan.uploads.path', 'uploads/dullahan'), '/');
            $publicPath = public_path($relativePath);

            \Illuminate\Support\Facades\File::ensureDirectoryExists($publicPath, 0755, true);
            $file->move($publicPath, $filename);

            $data['avatar'] = asset($relativePath . '/' . $filename);
        } else {
            $data['avatar'] = null;
        }

        Author::query()->create($data);

        return redirect()->route('dullahan.admin.authors.index')->with('status', 'Author created.');
    }

    public function edit(Author $author): View
    {
        return view('dullahan::admin.authors.form', [
            'author' => $author,
            'action' => route('dullahan.admin.authors.update', $author),
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
            $relativePath = trim(config('dullahan.uploads.path', 'uploads/dullahan'), '/');
            $publicPath = public_path($relativePath);

            \Illuminate\Support\Facades\File::ensureDirectoryExists($publicPath, 0755, true);
            $file->move($publicPath, $filename);

            $data['avatar'] = asset($relativePath . '/' . $filename);
        } else {
            $data['avatar'] = $author->avatar;
        }

        $author->fill($data)->save();

        return redirect()->route('dullahan.admin.authors.index')->with('status', 'Author updated.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ((int) Auth::guard(config('dullahan.auth.guard', 'dullahan'))->id() === (int) $author->getKey()) {
            return redirect()->route('dullahan.admin.authors.index')->with('status', 'You cannot delete your own author account.');
        }

        if ($author->posts()->exists()) {
            return redirect()->route('dullahan.admin.authors.index')->with('status', 'Move this author posts before deleting the author.');
        }

        $author->delete();

        return redirect()->route('dullahan.admin.authors.index')->with('status', 'Author deleted.');
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_unless(
            Auth::guard(config('dullahan.auth.guard', 'dullahan'))->user()?->email === config('dullahan.admin.email'),
            403,
            'Only the system administrator can export authors.'
        );

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role/Title', 'Bio', 'Avatar URL', 'Website URL', 'Show Author Box', 'Created At']);
            
            Author::query()->chunk(100, function ($authors) use ($handle) {
                foreach ($authors as $author) {
                    fputcsv($handle, [
                        $author->id,
                        $author->name,
                        $author->email,
                        $author->job_title ?? '',
                        $author->bio ?? '',
                        $author->avatar ?? '',
                        $author->website_url ?? '',
                        $author->show_author_box ? 'Yes' : 'No',
                        $author->created_at->toIso8601String(),
                    ]);
                }
            });
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dullahan-authors-' . now()->format('YmdHis') . '.csv"',
        ]);

        return $response;
    }

    private function validated(Request $request, ?Author $author = null): array
    {
        $mimes = implode(',', config('dullahan.uploads.mimes', ['jpeg', 'png', 'jpg', 'webp', 'svg']));

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('dullahan_authors', 'email')->ignore($author?->getKey())],
            'password' => [$author ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:' . $mimes, 'max:' . config('dullahan.uploads.max_kb', 4096)],
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
