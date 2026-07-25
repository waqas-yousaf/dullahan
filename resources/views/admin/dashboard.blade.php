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
                <span class="muted">by {{ $post->author?->name ?? 'Unknown' }} - {{ ucfirst($post->status) }} - {{ $post->autosaved_at ? 'autosaved ' . $post->autosaved_at->diffForHumans() : 'saved ' . $post->updated_at->diffForHumans() }}</span>
            </p>
        @empty
            <p class="muted">No posts yet.</p>
        @endforelse
    </section>

    <section class="panel" style="margin-top: 22px;">
        <h2 style="margin-top: 0;">Author Box</h2>
        <form method="post" action="{{ route('dulluhan.admin.author-box.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="job_title">Role or title</label>
                <input id="job_title" name="job_title" value="{{ old('job_title', $author->job_title) }}" maxlength="255">
                @error('job_title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio">{{ old('bio', $author->bio) }}</textarea>
                @error('bio') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="avatar">Avatar Image</label>
                @if ($author->avatar)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ $author->avatar }}" alt="" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 1px solid var(--line);">
                    </div>
                @endif
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml">
                @error('avatar') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="grid">
                <div class="field">
                    <label for="website_url">Website URL</label>
                    <input id="website_url" name="website_url" type="url" value="{{ old('website_url', $author->website_url) }}">
                    @error('website_url') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid">
                <div class="field">
                    <label for="facebook_url">Facebook URL</label>
                    <input id="facebook_url" name="facebook_url" type="url" value="{{ old('facebook_url', $author->facebook_url) }}">
                    @error('facebook_url') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="x_url">X URL</label>
                    <input id="x_url" name="x_url" type="url" value="{{ old('x_url', $author->x_url) }}">
                    @error('x_url') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="linkedin_url">LinkedIn URL</label>
                    <input id="linkedin_url" name="linkedin_url" type="url" value="{{ old('linkedin_url', $author->linkedin_url) }}">
                    @error('linkedin_url') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="instagram_url">Instagram URL</label>
                    <input id="instagram_url" name="instagram_url" type="url" value="{{ old('instagram_url', $author->instagram_url) }}">
                    @error('instagram_url') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="youtube_url">YouTube URL</label>
                    <input id="youtube_url" name="youtube_url" type="url" value="{{ old('youtube_url', $author->youtube_url) }}">
                    @error('youtube_url') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="social_links">Additional social links</label>
                <textarea id="social_links" name="social_links" placeholder="LinkedIn | https://linkedin.com/in/name&#10;X | https://x.com/name">{{ old('social_links', collect($author->social_links ?? [])->map(fn ($link) => ($link['label'] ?? '') . ' | ' . ($link['url'] ?? ''))->join("\n")) }}</textarea>
                @error('social_links') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label style="display:flex;gap:8px;align-items:center;font-weight:500;">
                    <input type="checkbox" name="show_author_box" value="1" style="width:auto;" @checked(old('show_author_box', $author->show_author_box))>
                    Show author box in API responses
                </label>
            </div>

            <button class="btn" type="submit">Save Author Box</button>
        </form>
    </section>
@endsection
