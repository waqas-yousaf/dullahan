<?php

namespace WaqasYousaf\Dullahan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('dullahan_posts', 'slug')->ignore($post?->getKey())],
            'author_id' => ['required', 'integer', 'exists:dullahan_authors,id'],
            'content' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'post_type' => ['required', Rule::in(array_keys(config('dullahan.post_types', ['post' => 'Post'])))],
            'excerpt' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'url'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'min:50', 'max:70'],
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
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('published_at') && $this->filled('timezone')) {
            try {
                $localTime = $this->input('published_at');
                $timezone = $this->input('timezone');

                if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $localTime)) {
                    $utcDate = Carbon::createFromFormat('Y-m-d\TH:i', $localTime, $timezone)
                        ->setTimezone(config('app.timezone', 'UTC'));

                    $this->merge([
                        'published_at' => $utcDate->toDateTimeString(),
                    ]);
                }
            } catch (\Exception $e) {
                // Let validation handle parsing errors
            }
        }
    }
}
