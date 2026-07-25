<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dulluhan' }}</title>
    <style>
        :root { color-scheme: light; --bg: #f6f7f9; --panel: #fff; --text: #111827; --muted: #6b7280; --line: #e5e7eb; --accent: #0f766e; --danger: #b91c1c; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 230px 1fr; }
        .sidebar { background: #111827; color: #f9fafb; padding: 24px; }
        .brand { font-size: 20px; font-weight: 700; margin-bottom: 28px; }
        .nav { display: grid; gap: 8px; }
        .nav a, .nav button { border: 0; width: 100%; padding: 10px 12px; border-radius: 6px; background: transparent; color: #d1d5db; text-align: left; font: inherit; cursor: pointer; }
        .nav a:hover, .nav button:hover { background: #1f2937; color: #fff; }
        .main { padding: 28px; display: flex; flex-direction: column; min-width: 0; }
        .content { flex: 1; }
        .footer { color: var(--muted); font-size: 13px; margin-top: 28px; padding-top: 16px; border-top: 1px solid var(--line); }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        h1 { margin: 0; font-size: 28px; letter-spacing: 0; }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 20px; }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
        .metric { background: var(--panel); border: 1px solid var(--line); border-radius: 8px; padding: 18px; }
        .metric strong { display: block; font-size: 30px; }
        .muted { color: var(--muted); }
        .btn { display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 6px; padding: 10px 14px; background: var(--accent); color: white; font: inherit; cursor: pointer; }
        .btn.secondary { background: #374151; }
        .btn.danger { background: var(--danger); }
        table { width: 100%; border-collapse: collapse; background: var(--panel); border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
        th { color: var(--muted); font-size: 13px; font-weight: 600; }
        tr:last-child td { border-bottom: 0; }
        label { display: block; font-weight: 600; margin-bottom: 7px; }
        input, select, textarea { width: 100%; border: 1px solid var(--line); border-radius: 6px; padding: 10px 12px; font: inherit; }
        textarea { min-height: 130px; }
        .field { margin-bottom: 16px; }
        .error { color: var(--danger); font-size: 14px; margin-top: 6px; }
        .alert { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; }
        .actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .checkbox-grid { display: grid; gap: 8px; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); }
        .checkbox-grid label { display: flex; gap: 8px; align-items: center; margin: 0; font-weight: 500; }
        .checkbox-grid input { width: auto; }
        .ql-editor { min-height: 320px; background: #fff; }
        @media (max-width: 760px) { .shell { grid-template-columns: 1fr; } .sidebar { position: static; } .main { padding: 18px; } .topbar { align-items: flex-start; flex-direction: column; } }
    </style>
    @stack('head')
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">{{ config('app.name', 'Laravel') }}</div>
            <nav class="nav">
                <a href="{{ route('dulluhan.admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('dulluhan.admin.posts.index') }}">Posts</a>
                <a href="{{ route('dulluhan.admin.categories.index') }}">Categories</a>
                <a href="{{ route('dulluhan.admin.authors.index') }}">Authors</a>
                <a href="{{ route('dulluhan.admin.api.documentation') }}">API Docs</a>
                <a href="{{ route('dulluhan.admin.posts.create') }}">New Post</a>
                <form method="post" action="{{ route('dulluhan.admin.logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </aside>
        <main class="main">
            <div class="content">
                @if (session('status'))
                    <div class="alert">{{ session('status') }}</div>
                @endif
                @yield('content')
            </div>
            <footer class="footer">
                Dulluhan v{{ \YourVendor\Dulluhan\DulluhanServiceProvider::version() }}
            </footer>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
