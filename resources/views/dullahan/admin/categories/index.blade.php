@extends('dullahan::admin.layout', ['title' => 'Dullahan Categories'])

@section('content')
    <div class="topbar">
        <h1>Categories</h1>
        <div class="actions">
            <a class="btn secondary" href="{{ route('dullahan.admin.categories.export') }}">Export CSV</a>
            <a class="btn secondary" href="{{ route('dullahan.admin.posts.index') }}">Posts</a>
        </div>
    </div>

    <div class="grid" style="align-items:start;">
        <form class="panel" method="post" action="{{ $action }}">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <h2 style="margin-top:0;">{{ $category->exists ? 'Edit Category' : 'New Category' }}</h2>

            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="255">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="255">
                @error('slug') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="actions">
                <button class="btn" type="submit">{{ $category->exists ? 'Update Category' : 'Create Category' }}</button>
                @if ($category->exists)
                    <a class="btn secondary" href="{{ route('dullahan.admin.categories.index') }}">Cancel</a>
                @endif
            </div>
        </form>

        <section class="panel">
            <h2 style="margin-top:0;">All Categories</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Posts</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <div class="muted">{{ $item->slug }}</div>
                            </td>
                            <td>{{ $item->posts_count }}</td>
                            <td>
                                <div class="actions">
                                    <a class="btn success" href="{{ route('dullahan.admin.posts.index', ['category' => $item->id]) }}" title="View posts in category" style="padding: 6px 8px; display: inline-flex; align-items: center;">
                                        <span class="material-icons" style="font-size: 18px;">chevron_right</span>
                                    </a>
                                    <a class="btn info" href="{{ route('dullahan.admin.categories.edit', $item) }}">Edit</a>
                                    <form method="post" action="{{ route('dullahan.admin.categories.destroy', $item) }}" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="muted">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:18px;">
                {{ $categories->links() }}
            </div>
        </section>
    </div>
@endsection
