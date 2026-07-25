<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('dulluhan::admin.profile', [
            'author' => Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $author = Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user();
        $mimes = implode(',', config('dulluhan.uploads.mimes', ['jpeg', 'png', 'jpg', 'webp', 'svg']));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('dulluhan_authors', 'email')->ignore($author->getKey())],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:' . $mimes, 'max:' . config('dulluhan.uploads.max_kb', 4096)],
            'website_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
            'x_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'youtube_url' => ['nullable', 'url'],
            'social_links' => ['nullable', 'string'],
            'show_author_box' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'], $author->password)) {
                throw ValidationException::withMessages([
                    'current_password' => __('The current password is incorrect.'),
                ]);
            }
            $author->password = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', \Illuminate\Support\Str::ascii($name)), '-');
            $filename = 'avatar-' . now()->format('YmdHis') . '-' . ($safeName ?: 'avatar') . '.' . $extension;
            $relativePath = trim(config('dulluhan.uploads.path', 'uploads/dulluhan'), '/');
            $publicPath = public_path($relativePath);

            File::ensureDirectoryExists($publicPath, 0755, true);
            $file->move($publicPath, $filename);

            $author->avatar = asset($relativePath . '/' . $filename);
        }

        $author->name = $data['name'];
        $author->email = $data['email'];
        $author->job_title = $data['job_title'] ?? null;
        $author->bio = $data['bio'] ?? null;
        $author->website_url = $data['website_url'] ?? null;
        $author->facebook_url = $data['facebook_url'] ?? null;
        $author->x_url = $data['x_url'] ?? null;
        $author->linkedin_url = $data['linkedin_url'] ?? null;
        $author->instagram_url = $data['instagram_url'] ?? null;
        $author->youtube_url = $data['youtube_url'] ?? null;
        $author->social_links = $this->parseSocialLinks($data['social_links'] ?? '');
        $author->show_author_box = $request->boolean('show_author_box');

        $author->save();

        if (! empty($data['password'])) {
            $request->session()->regenerate();
        }

        return redirect()->route('dulluhan.admin.profile.edit')->with('status', 'Profile updated successfully.');
    }

    private function parseSocialLinks(string $value): array
    {
        return collect(explode("\n", $value))
            ->map(function ($line): ?array {
                $parts = explode('|', $line, 2);
                if (count($parts) < 2) {
                    return null;
                }

                $label = trim($parts[0]);
                $url = trim($parts[1]);

                if (empty($label) || empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                    return null;
                }

                return [
                    'label' => $label,
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
