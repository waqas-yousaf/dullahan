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
                <th>Status</th>
                <th>Author</th>
                <th>Published</th>
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
                    <td>{{ ucfirst($post->status) }}</td>
                    <td>{{ $post->author?->name ?? 'Unknown' }}</td>
                    <td>{{ $post->published_at?->format('M j, Y H:i') ?? 'Not scheduled' }}</td>
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
                <tr><td colspan="5" class="muted">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 18px;">
        {{ $posts->links() }}
    </div>
@endsection
