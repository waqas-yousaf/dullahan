<?php

namespace WaqasYousaf\Dulluhan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use WaqasYousaf\Dulluhan\Models\Category;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('dulluhan::admin.categories.index', [
            'categories' => Category::query()->withCount('posts')->orderBy('name')->paginate(20),
            'category' => new Category(),
            'action' => route('dulluhan.admin.categories.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::query()->create($this->validated($request));

        return redirect()->route('dulluhan.admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('dulluhan::admin.categories.index', [
            'categories' => Category::query()->withCount('posts')->orderBy('name')->paginate(20),
            'category' => $category,
            'action' => route('dulluhan.admin.categories.update', $category),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->fill($this->validated($request, $category))->save();

        return redirect()->route('dulluhan.admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('dulluhan.admin.categories.index')->with('status', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('dulluhan_categories', 'slug')->ignore($category?->getKey()),
            ],
            'description' => ['nullable', 'string'],
        ]);
    }
}
