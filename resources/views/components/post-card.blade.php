<article {{ $attributes->merge(['style' => 'border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:#fff;']) }}>
    @if ($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="" style="display:block;width:100%;aspect-ratio:16/9;object-fit:cover;">
    @endif
    <div style="padding:16px;">
        <h2 style="margin:0 0 8px;font-size:20px;line-height:1.25;">{{ $post->title }}</h2>
        <p style="margin:0 0 12px;color:#6b7280;font-size:14px;">
            {{ config('dulluhan.post_types.' . $post->post_type, ucfirst($post->post_type ?? 'post')) }}
            @if ($post->categories->isNotEmpty())
                - {{ $post->categories->pluck('name')->join(', ') }}
            @endif
            <br>
            {{ $post->author?->name ?? 'Dulluhan' }}
            @if ($post->published_at)
                - {{ $post->published_at->format('M j, Y') }}
            @endif
        </p>
        @if ($post->excerpt)
            <p style="margin:0;color:#374151;line-height:1.6;">{{ $post->excerpt }}</p>
        @endif
    </div>
</article>
