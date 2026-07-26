@extends('dullahan::admin.layout', ['title' => $author->exists ? 'Edit Author' : 'New Author'])

@section('content')
<div class="topbar">
    <h1>{{ $author->exists ? 'Edit Author' : 'New Author' }}</h1>
    <a class="btn secondary" href="{{ route('dullahan.admin.authors.index') }}">All Authors</a>
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
            <input id="email" name="email" type="email" value="{{ old('email', $author->email) }}" required
                maxlength="255">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="grid">
        <div class="field">
            <label for="password">{{ $author->exists ? 'New password' : 'Password' }}</label>
            <div style="display: flex; gap: 8px;">
                <input id="password" name="password" type="password" @required(! $author->exists) minlength="8"
                autocomplete="new-password">
                <button type="button" class="btn secondary" id="btn-toggle-password" style="padding: 0 10px;"
                    title="Show/Hide Password">
                    <span class="material-icons" style="font-size: 20px;">visibility</span>
                </button>
                <button type="button" class="btn secondary" id="btn-generate-password" style="padding: 0 10px;"
                    title="Generate Password">
                    <span class="material-icons" style="font-size: 20px;">vpn_key</span>
                </button>
            </div>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <div style="display: flex; gap: 8px;">
                <input id="password_confirmation" name="password_confirmation" type="password" @required(!
                    $author->exists) minlength="8" autocomplete="new-password">
                <button type="button" class="btn secondary" id="btn-toggle-password-conf" style="padding: 0 10px;"
                    title="Show/Hide Confirm Password">
                    <span class="material-icons" style="font-size: 20px;">visibility</span>
                </button>
            </div>
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
            <img src="{{ $author->avatar }}" alt=""
                style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 1px solid var(--line);">
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
            <input type="checkbox" name="show_author_box" value="1" style="width:auto;" @checked(old('show_author_box',
                $author->show_author_box))>
            Show author box in API responses
        </label>
    </div>

    <button class="btn" type="submit">Save Author</button>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const toggleBtn = document.getElementById('btn-toggle-password');
        const toggleConfBtn = document.getElementById('btn-toggle-password-conf');
        const generateBtn = document.getElementById('btn-generate-password');

        function toggleVisibility(input, button) {
            const icon = button.querySelector('.material-icons');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => toggleVisibility(passwordInput, toggleBtn));
        }

        if (toggleConfBtn && confirmInput) {
            toggleConfBtn.addEventListener('click', () => toggleVisibility(confirmInput, toggleConfBtn));
        }

        if (generateBtn && passwordInput && confirmInput) {
            generateBtn.addEventListener('click', function () {
                const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]:;?><,./-=";
                let password = "";
                const array = new Uint32Array(16);
                window.crypto.getRandomValues(array);
                for (let i = 0; i < 16; i++) {
                    password += chars[array[i] % chars.length];
                }

                passwordInput.value = password;
                confirmInput.value = password;

                passwordInput.type = 'text';
                confirmInput.type = 'text';

                const icon = toggleBtn.querySelector('.material-icons');
                const iconConf = toggleConfBtn.querySelector('.material-icons');
                if (icon) icon.textContent = 'visibility_off';
                if (iconConf) iconConf.textContent = 'visibility_off';
            });
        }
    });
</script>
@endpush