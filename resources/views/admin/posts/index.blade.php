@extends('dulluhan::admin.layout', ['title' => 'Dulluhan Posts'])

@section('content')
    <div class="topbar">
        <h1>Posts</h1>
        <a class="btn" href="{{ route('dulluhan.admin.posts.create') }}">New Post</a>
    </div>

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
                        @forelse ($post->categories as $category)
                            <span class="muted">{{ $category->name }}{{ ! $loop->last ? ',' : '' }}</span>
                        @empty
                            <span class="muted">Uncategorized</span>
                        @endforelse
                    </td>
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>{{ $post->author?->name ?? 'Unknown' }}</td>
                    <td>{{ $post->autosaved_at?->diffForHumans() ?? $post->updated_at?->diffForHumans() }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn secondary" href="{{ route('dulluhan.admin.posts.edit', $post) }}">Edit</a>
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
