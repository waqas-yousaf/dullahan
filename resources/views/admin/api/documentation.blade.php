@extends('dulluhan::admin.layout', ['title' => 'Dulluhan API Documentation'])

@section('content')
    <div class="topbar">
        <h1>API Documentation</h1>
        <a class="btn secondary" href="{{ route('dulluhan.admin.dashboard') }}">Dashboard</a>
    </div>

    <section class="panel">
        <h2 style="margin-top:0;">Authentication</h2>
        <p class="muted">API protection is {{ $apiSecurity['enabled'] ?? false ? 'enabled' : 'disabled' }}.</p>
        <p>Send the API key with the <code>{{ $apiSecurity['header'] ?? 'X-Dulluhan-Api-Key' }}</code> header.</p>
        <pre style="overflow:auto;background:#111827;color:#f9fafb;border-radius:8px;padding:14px;">curl -H "{{ $apiSecurity['header'] ?? 'X-Dulluhan-Api-Key' }}: YOUR_API_KEY" {{ url($apiPrefix . '/posts') }}</pre>

        <h2>Domain Restrictions</h2>
        @if (! empty($apiSecurity['allowed_domains']))
            <ul>
                @foreach ($apiSecurity['allowed_domains'] as $domain)
                    <li>{{ $domain }}</li>
                @endforeach
            </ul>
        @else
            <p class="muted">No domain restrictions are configured.</p>
        @endif
    </section>

    <section class="panel" style="margin-top:22px;">
        <h2 style="margin-top:0;">Endpoints</h2>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Endpoint</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>GET</td>
                    <td><code>/{{ $apiPrefix }}/posts</code></td>
                    <td>Paginated published posts with categories, SEO values, and author box.</td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td><code>/{{ $apiPrefix }}/posts/{slug}</code></td>
                    <td>Single published post by slug.</td>
                </tr>
            </tbody>
        </table>

        <h2>Filters</h2>
        <table>
            <thead>
                <tr>
                    <th>Query</th>
                    <th>Example</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>type</code></td>
                    <td><code>/{{ $apiPrefix }}/posts?type=news</code></td>
                </tr>
                <tr>
                    <td><code>category</code></td>
                    <td><code>/{{ $apiPrefix }}/posts?category=announcements</code></td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="panel" style="margin-top:22px;">
        <h2 style="margin-top:0;">Response Shape</h2>
        <pre style="overflow:auto;background:#111827;color:#f9fafb;border-radius:8px;padding:14px;">{
  "data": [
    {
      "id": 1,
      "title": "Post title",
      "slug": "post-title",
      "post_type": "post",
      "categories": [],
      "seo": {
        "meta_title": "Post title",
        "meta_description": "Summary",
        "canonical_url": "https://example.com/post-title",
        "robots": "index,follow"
      },
      "author_box": {
        "name": "Author Name",
        "bio": "Author bio",
        "social_links": []
      }
    }
  ]
}</pre>
    </section>
@endsection
