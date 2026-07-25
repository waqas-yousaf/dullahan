<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dulluhan Login</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f6f7f9; color: #111827; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        form { width: min(420px, calc(100vw - 32px)); background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 28px; }
        h1 { margin: 0 0 20px; font-size: 26px; letter-spacing: 0; }
        label { display: block; font-weight: 600; margin: 14px 0 7px; }
        input { width: 100%; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 6px; padding: 11px 12px; font: inherit; }
        button { width: 100%; border: 0; border-radius: 6px; padding: 11px 12px; margin-top: 18px; background: #0f766e; color: white; font: inherit; cursor: pointer; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 6px; }
    </style>
</head>
<body>
    <form method="post" action="{{ route('dulluhan.admin.login.store') }}">
        @csrf
        <h1>Dulluhan</h1>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Sign in</button>
    </form>
</body>
</html>
