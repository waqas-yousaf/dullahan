<article {{ $attributes->merge(['style' => 'border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;background:#fff;']) }}>
    @if ($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="" style="display:block;width:100%;aspect-ratio:16/9;object-fit:cover;">
    @endif
    <div style="padding:16px;">
        <h2 style="margin:0 0 8px;font-size:20px;line-height:1.25;">{{ $post->title }}</h2>
        <p style="margin:0 0 12px;color:#6b7280;font-size:14px;">
            {{ config('dullahan.post_types.' . $post->post_type, ucfirst($post->post_type ?? 'post')) }}
            @if ($post->category)
                - {{ $post->category->name }}
            @endif
            <br>
            {{ $post->author?->name ?? 'Dullahan' }}
            @if ($post->published_at)
                - {{ $post->published_at->format('M j, Y') }}
            @endif
        </p>
    </div>
</article>
