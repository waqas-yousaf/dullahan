<div {{ $attributes->merge(['style' => 'display:grid;gap:18px;']) }}>
    @forelse ($posts as $post)
        <x-dulluhan-post-card :post="$post" />
    @empty
        <p style="margin:0;color:#6b7280;">No posts found.</p>
    @endforelse

    @if (method_exists($posts, 'links'))
        <div>
            {{ $posts->links() }}
        </div>
    @endif
</div>
