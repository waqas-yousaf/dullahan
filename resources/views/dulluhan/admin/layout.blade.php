<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dulluhan' }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f8fafc;
            --panel: #ffffff;
            --text: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #0f766e;
            --danger: #e11d48;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                color-scheme: dark;
                --bg: #0b0f19;
                --panel: #111827;
                --text: #f8fafc;
                --muted: #94a3b8;
                --line: #1e293b;
                --accent: #14b8a6;
                --danger: #f43f5e;
            }
        }

        * {
            box-sizing: border-box;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.15s ease, box-shadow 0.2s ease;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 230px 1fr;
        }

        .sidebar {
            background: #111827;
            color: #f9fafb;
            padding: 24px;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 28px;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav a,
        .nav button {
            border: 0;
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            background: transparent;
            color: #d1d5db;
            text-align: left;
            font: inherit;
            cursor: pointer;
        }

        .nav a:hover,
        .nav button:hover {
            background: #1f2937;
            color: #fff;
        }

        .main {
            padding: 28px;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content {
            flex: 1;
        }

        .footer {
            color: var(--muted);
            font-size: 13px;
            margin-top: 28px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 0;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
        }

        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .metric {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
        }

        .metric strong {
            display: block;
            font-size: 30px;
        }

        .muted {
            color: var(--muted);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 6px;
            padding: 10px 14px;
            background: var(--accent);
            color: white;
            font: inherit;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn:hover {
            opacity: 0.95;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn.secondary {
            background: var(--panel);
            color: var(--text);
            border: 1px solid var(--line);
        }

        .btn.secondary:hover {
            background: var(--bg);
        }

        .btn.danger {
            background: var(--danger);
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: middle;
        }

        th {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 7px;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 10px 12px;
            font: inherit;
            background: var(--panel);
            color: var(--text);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.15);
        }

        textarea {
            min-height: 130px;
        }

        .field {
            margin-bottom: 16px;
        }

        .error {
            color: var(--danger);
            font-size: 14px;
            margin-top: 6px;
        }

        .alert {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.15);
            color: #10b981;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-weight: 500;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .checkbox-grid {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        }

        .checkbox-grid label {
            display: flex;
            gap: 8px;
            align-items: center;
            margin: 0;
            font-weight: 500;
        }

        .checkbox-grid input {
            width: auto;
        }

        .ql-editor {
            min-height: 320px;
            background: var(--panel);
            color: var(--text);
        }

        .ql-toolbar.ql-snow {
            background: var(--panel) !important;
            border-color: var(--line) !important;
        }
        .ql-container.ql-snow {
            border-color: var(--line) !important;
        }
        .ql-snow .ql-stroke {
            stroke: var(--text) !important;
        }
        .ql-snow .ql-fill {
            fill: var(--text) !important;
        }
        .ql-snow .ql-picker {
            color: var(--text) !important;
        }
        .ql-snow .ql-picker-options {
            background-color: var(--panel) !important;
            border-color: var(--line) !important;
        }

        @media (max-width: 760px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .main {
                padding: 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* Pagination */
        .w-5,
        .h-5,
        nav svg,
        [role="navigation"] svg,
        svg.w-5,
        svg.h-5 {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle;
        }

        nav p,
        [role="navigation"] p {
            margin: 0;
            font-size: 14px;
            color: var(--muted);
        }

        nav>div:first-child,
        [role="navigation"]>div:first-child {
            display: none !important;
        }

        nav>div:last-child,
        [role="navigation"]>div:last-child {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            flex-wrap: wrap;
            gap: 12px;
        }

        nav .relative.z-0,
        [role="navigation"] .relative.z-0 {
            display: inline-flex;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--line);
        }

        nav .relative.z-0 a,
        nav .relative.z-0 span,
        [role="navigation"] .relative.z-0 a,
        [role="navigation"] .relative.z-0 span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            background: var(--panel);
            border: 0;
            border-right: 1px solid var(--line);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            color: var(--text);
        }

        nav .relative.z-0 a:last-child,
        nav .relative.z-0 span:last-child,
        [role="navigation"] .relative.z-0 a:last-child,
        [role="navigation"] .relative.z-0 span:last-child {
            border-right: 0;
        }

        nav .relative.z-0 a:hover,
        [role="navigation"] .relative.z-0 a:hover {
            background: var(--bg);
        }

        nav .relative.z-0 span[aria-current="page"],
        [role="navigation"] .relative.z-0 span[aria-current="page"] {
            background: var(--accent);
            color: white;
            font-weight: 600;
        }

        nav .relative.z-0 span[aria-disabled="true"],
        [role="navigation"] .relative.z-0 span[aria-disabled="true"] {
            color: var(--muted);
            cursor: not-allowed;
        }
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @stack('head')
</head>

<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">{{ config('app.name', 'Laravel') }}</div>
            <nav class="nav">
                <a href="{{ route('dulluhan.admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('dulluhan.admin.posts.index') }}">Posts</a>
                <a href="{{ route('dulluhan.admin.posts.create') }}">New Post</a>
                <a href="{{ route('dulluhan.admin.categories.index') }}">Categories</a>
                @if (Auth::guard(config('dulluhan.auth.guard', 'dulluhan'))->user()?->email === config('dulluhan.admin.email'))
                    <a href="{{ route('dulluhan.admin.authors.index') }}">Authors</a>
                @endif
                <a href="{{ route('dulluhan.admin.profile.edit') }}">Profile Settings</a>
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
                Dulluhan v{{ \WaqasYousaf\Dulluhan\DulluhanServiceProvider::version() }}
            </footer>
        </main>
    </div>
    @stack('scripts')
</body>

</html>