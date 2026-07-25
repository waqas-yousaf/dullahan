@extends('dulluhan::admin.layout', ['title' => $author->exists ? 'Edit Author' : 'New Author'])

@section('content')
    <div class="topbar">
        <h1>{{ $author->exists ? 'Edit Author' : 'New Author' }}</h1>
        <a class="btn secondary" href="{{ route('dulluhan.admin.authors.index') }}">All Authors</a>
    </div>

    <form class="panel" method="post" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="grid">
            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $author->name) }}" required maxlength="255">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $author->email) }}" required maxlength="255">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid">
            <div class="field">
                <label for="password">{{ $author->exists ? 'New password' : 'Password' }}</label>
                <input id="password" name="password" type="password" @required(! $author->exists) minlength="8" autocomplete="new-password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" @required(! $author->exists) minlength="8" autocomplete="new-password">
            </div>
        </div>

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
            @foreach ([
                'website_url' => 'Website URL',
                'facebook_url' => 'Facebook URL',
                'x_url' => 'X URL',
                'linkedin_url' => 'LinkedIn URL',
                'instagram_url' => 'Instagram URL',
                'youtube_url' => 'YouTube URL',
            ] as $field => $label)
                <div class="field">
                    <label for="{{ $field }}">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="url" value="{{ old($field, $author->{$field}) }}">
                    @error($field) <div class="error">{{ $message }}</div> @enderror
                </div>
            @endforeach
        </div>

        <div class="field">
            <label style="display:flex;gap:8px;align-items:center;font-weight:500;">
                <input type="checkbox" name="show_author_box" value="1" style="width:auto;" @checked(old('show_author_box', $author->show_author_box))>
                Show author box in API responses
            </label>
        </div>

        <button class="btn" type="submit">Save Author</button>
    </form>
@endsection
