@extends('dullahan::admin.layout', ['title' => 'Profile & Author Settings'])

@section('content')
    <div class="topbar">
        <h1>Profile & Author Settings</h1>
    </div>

    <form class="panel" method="post" action="{{ route('dullahan.admin.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h3 style="margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Account Details</h3>
        <div class="grid">
            <div class="field">
                <label for="name">Name</label>
                <input id="name" name="name" value="{{ old('name', $author->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $author->email) }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <h3 style="margin-top: 24px; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Change Password</h3>
        <div class="field">
            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            <div class="muted">Required only if you are changing your password.</div>
            @error('current_password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="grid">
            <div class="field">
                <label for="password">New password</label>
                <div style="display: flex; gap: 8px;">
                    <input id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                    <button type="button" class="btn secondary" id="btn-toggle-password" style="padding: 0 10px;" title="Show/Hide Password">
                        <span class="material-icons" style="font-size: 20px;">visibility</span>
                    </button>
                    <button type="button" class="btn secondary" id="btn-generate-password" style="padding: 0 10px;" title="Generate Password">
                        <span class="material-icons" style="font-size: 20px;">vpn_key</span>
                    </button>
                </div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <div style="display: flex; gap: 8px;">
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password">
                    <button type="button" class="btn secondary" id="btn-toggle-password-conf" style="padding: 0 10px;" title="Show/Hide Confirm Password">
                        <span class="material-icons" style="font-size: 20px;">visibility</span>
                    </button>
                </div>
            </div>
        </div>

        <h3 style="margin-top: 24px; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Author Box Details</h3>
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

        <button class="btn" type="submit" style="margin-top: 16px;">Save Settings</button>
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
