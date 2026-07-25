@extends('dulluhan::admin.layout', ['title' => 'Change Password'])

@section('content')
    <div class="topbar">
        <h1>Change Password</h1>
    </div>

    <form class="panel" method="post" action="{{ route('dulluhan.admin.password.update') }}">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
            @error('current_password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="grid">
            <div class="field">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
            </div>
        </div>

        <button class="btn" type="submit">Change Password</button>
    </form>
@endsection
