@extends('dulluhan::admin.layout', ['title' => 'Dulluhan Posts'])

@section('content')
    <div class="topbar">
        <h1>Posts</h1>
        <div class="actions">
            <a class="btn secondary" href="{{ route('dulluhan.admin.posts.export') }}">Export CSV</a>
            <a class="btn" href="{{ route('dulluhan.admin.posts.create') }}">New Post</a>
        </div>
    </div>

    <form method="get" action="{{ route('dulluhan.admin.posts.index') }}" class="panel" style="margin-bottom: 22px; padding: 16px;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label for="search" style="font-size: 13px; margin-bottom: 4px;">Search</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="Search title or content..." style="padding: 8px 12px; height: 38px;">
            </div>

            <div style="min-width: 150px;">
                <label for="category" style="font-size: 13px; margin-bottom: 4px;">Category</label>
                <select id="category" name="category" style="padding: 8px 12px; height: 38px;">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 150px;">
                <label for="author" style="font-size: 13px; margin-bottom: 4px;">Author</label>
                <select id="author" name="author" style="padding: 8px 12px; height: 38px;">
                    <option value="">All Authors</option>
                    @foreach ($authors as $auth)
                        <option value="{{ $auth->id }}" @selected(request('author') == $auth->id)>{{ $auth->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 130px;">
                <label for="type" style="font-size: 13px; margin-bottom: 4px;">Type</label>
                <select id="type" name="type" style="padding: 8px 12px; height: 38px;">
                    <option value="">All Types</option>
                    @foreach ($postTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') == $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 120px;">
                <label for="status" style="font-size: 13px; margin-bottom: 4px;">Status</label>
                <select id="status" name="status" style="padding: 8px 12px; height: 38px;">
                    <option value="">All Statuses</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn" style="height: 38px; padding: 0 16px;">Filter</button>
                @if (request()->anyFilled(['search', 'category', 'author', 'type', 'status']))
                    <a href="{{ route('dulluhan.admin.posts.index') }}" class="btn secondary" style="height: 38px; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center;">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Categories</th>
                <th>Status</th>
                <th>Author</th>
                <th>Saved</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>
                        <strong>{{ $post->title }}</strong>
                        <div class="muted">{{ $post->slug }}</div>
                    </td>
                    <td>{{ $postTypes[$post->post_type] ?? ucfirst($post->post_type) }}</td>
                    <td>
                        @if ($post->category)
                            <span class="muted">{{ $post->category->name }}</span>
                        @else
                            <span class="muted">Uncategorized</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>{{ $post->author?->name ?? 'Unknown' }}</td>
                    <td>{{ $post->autosaved_at?->diffForHumans() ?? $post->updated_at?->diffForHumans() }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn success" href="{{ $post->publicUrl() }}" target="_blank">View</a>
                            <a class="btn info" href="{{ route('dulluhan.admin.posts.edit', $post) }}">Edit</a>
                            <form method="post" action="{{ route('dulluhan.admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 18px;">
        {{ $posts->links() }}
    </div>
@endsection
