<?php

namespace WaqasYousaf\Dullahan\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use WaqasYousaf\Dullahan\Models\Category;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('dullahan::admin.categories.index', [
            'categories' => Category::query()->withCount('posts')->orderBy('name')->paginate(20),
            'category' => new Category(),
            'action' => route('dullahan.admin.categories.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::query()->create($this->validated($request));

        return redirect()->route('dullahan.admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category): View
    {
        return view('dullahan::admin.categories.index', [
            'categories' => Category::query()->withCount('posts')->orderBy('name')->paginate(20),
            'category' => $category,
            'action' => route('dullahan.admin.categories.update', $category),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->fill($this->validated($request, $category))->save();

        return redirect()->route('dullahan.admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('dullahan.admin.categories.index')->with('status', 'Category deleted.');
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Description', 'Posts Count', 'Created At']);
            
            Category::query()->withCount('posts')->chunk(100, function ($categories) use ($handle) {
                foreach ($categories as $cat) {
                    fputcsv($handle, [
                        $cat->id,
                        $cat->name,
                        $cat->slug,
                        $cat->description ?? '',
                        $cat->posts_count,
                        $cat->created_at->toIso8601String(),
                    ]);
                }
            });
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="dullahan-categories-' . now()->format('YmdHis') . '.csv"',
        ]);

        return $response;
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('dullahan_categories', 'slug')->ignore($category?->getKey()),
            ],
            'description' => ['nullable', 'string'],
        ]);
    }
}
