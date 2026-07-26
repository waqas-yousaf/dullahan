@extends('dullahan::admin.layout', ['title' => 'Dullahan Authors'])

@section('content')
    <div class="topbar">
        <h1>Authors</h1>
        <div class="actions">
            <a class="btn secondary" href="{{ route('dullahan.admin.authors.export') }}">Export CSV</a>
            <a class="btn" href="{{ route('dullahan.admin.authors.create') }}">New Author</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Posts</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($authors as $author)
                <tr>
                    <td>
                        <strong>{{ $author->name }}</strong>
                        <div class="muted">{{ $author->show_author_box ? 'Author box visible' : 'Author box hidden' }}</div>
                    </td>
                    <td>{{ $author->email }}</td>
                    <td>{{ $author->job_title ?? 'Not set' }}</td>
                    <td>{{ $author->posts_count }}</td>
                    <td>
                        <div class="actions">
                            <a class="btn info" href="{{ route('dullahan.admin.authors.edit', $author) }}">Edit</a>
                            <form method="post" action="{{ route('dullahan.admin.authors.destroy', $author) }}" onsubmit="return confirm('Delete this author?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No authors yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:18px;">
        {{ $authors->links() }}
    </div>
@endsection
