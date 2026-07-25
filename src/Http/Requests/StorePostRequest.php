<?php

namespace YourVendor\Dulluhan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'post_type' => ['required', Rule::in(array_keys(config('dulluhan.post_types', ['post' => 'Post'])))],
            'featured_image' => ['nullable', 'url'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:dulluhan_categories,id'],
        ];
    }
}
