<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthorBoxController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $mimes = implode(',', config('dulluhan.uploads.mimes', ['jpeg', 'png', 'jpg', 'webp', 'svg']));
        $data = $request->validate([
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

        $author = Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user();

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

        $author->fill([
            'job_title' => $data['job_title'] ?? null,
            'bio' => $data['bio'] ?? null,
            'avatar' => $data['avatar'],
            'website_url' => $data['website_url'] ?? null,
            'facebook_url' => $data['facebook_url'] ?? null,
            'x_url' => $data['x_url'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? null,
            'youtube_url' => $data['youtube_url'] ?? null,
            'social_links' => $this->parseSocialLinks($data['social_links'] ?? ''),
            'show_author_box' => $request->boolean('show_author_box'),
        ])->save();

        return redirect()->route('dulluhan.admin.dashboard')->with('status', 'Author box updated.');
    }

    private function parseSocialLinks(string $links): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $links))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->map(function (string $line): array {
                [$label, $url] = array_pad(array_map('trim', explode('|', $line, 2)), 2, null);

                return [
                    'label' => $url ? $label : parse_url($label, PHP_URL_HOST) ?? $label,
                    'url' => $url ?: $label,
                ];
            })
            ->values()
            ->all();
    }
}
