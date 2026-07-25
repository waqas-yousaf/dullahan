@extends('dulluhan::admin.layout', ['title' => 'Dulluhan Dashboard'])

@section('content')
    <div class="topbar">
        <h1>Dashboard</h1>
        <a class="btn" href="{{ route('dulluhan.admin.posts.create') }}">New Post</a>
    </div>

    <section class="grid" style="margin-bottom: 22px;">
        <div class="metric"><span class="muted">Total posts</span><strong>{{ $totalPosts }}</strong></div>
        <div class="metric"><span class="muted">Published</span><strong>{{ $publishedPosts }}</strong></div>
        <div class="metric"><span class="muted">Drafts</span><strong>{{ $draftPosts }}</strong></div>
    </section>

    <section class="panel">
        <h2 style="margin-top: 0;">Recent posts</h2>
        @forelse ($recentPosts as $post)
            <p>
                <a href="{{ route('dulluhan.admin.posts.edit', $post) }}"><strong>{{ $post->title }}</strong></a>
                <span class="muted">by {{ $post->author?->name ?? 'Unknown' }} · {{ ucfirst($post->status) }}</span>
            </p>
        @empty
            <p class="muted">No posts yet.</p>
        @endforelse
    </section>
@endsection
