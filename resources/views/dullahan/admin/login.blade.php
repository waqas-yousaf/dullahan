<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} Login</title>
    <!-- Google Fonts & Material Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('dullahan-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <style>
        :root, html[data-theme="light"] {
            --bg-gradient: radial-gradient(circle at 0% 0%, #f1f5f9 0%, #cbd5e1 100%);
            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(255, 255, 255, 0.5);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --accent: #4f46e5;
            --accent-hover: #4338ca;
            --card-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.02);
            --danger: #ef4444;
        }
        html[data-theme="dark"] {
            --bg-gradient: radial-gradient(circle at 0% 0%, #0f172a 0%, #020617 100%);
            --card-bg: rgba(15, 23, 42, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(2, 6, 17, 0.6);
            --input-border: #1e293b;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --card-shadow: 0 20px 25px -5px rgba(0,0,0,0.4), 0 10px 10px -5px rgba(0,0,0,0.3);
            --danger: #f87171;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--bg-gradient);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            padding: 20px;
        }

        .login-card {
            width: min(440px, 100%);
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--card-shadow);
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-wrap h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, var(--accent) 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-wrap p {
            margin: 8px 0 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .field {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .material-icons {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 20px;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 8px;
            padding: 12px 14px 12px 42px;
            font: inherit;
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        input:focus + .material-icons {
            color: var(--accent);
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 24px;
            background: var(--accent);
            color: white;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
        }

        button:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: var(--danger);
            font-size: 13px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .error .material-icons {
            font-size: 16px;
        }

        .recaptcha {
            margin-top: 16px;
        }

        .footer {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 32px;
            text-align: center;
        }
        
        .footer span {
            background: var(--input-border);
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
            opacity: 0.85;
        }
    </style>
    @if (config('dullahan.recaptcha.enabled') && config('dullahan.recaptcha.site_key') && config('dullahan.recaptcha.version') === 'v3')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('dullahan.recaptcha.site_key') }}"></script>
    @elseif (config('dullahan.recaptcha.enabled') && config('dullahan.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
</head>
<body>
    <div class="login-card">
        <form id="dullahan-login-form" method="post" action="{{ route('dullahan.admin.login.store') }}">
            @csrf
            
            <div class="logo-wrap">
                <h1>{{ config('app.name', 'Laravel') }}</h1>
                <p>Sign in to manage your content</p>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <div class="input-icon-wrapper">
                    <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus>
                    <span class="material-icons">mail</span>
                </div>
                @error('email') 
                    <div class="error">
                        <span class="material-icons">error_outline</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="field" style="margin-bottom: 8px;">
                <label for="password">Password</label>
                <div class="input-icon-wrapper">
                    <input id="password" name="password" type="password" placeholder="••••••••" required>
                    <span class="material-icons">lock</span>
                </div>
                @error('password')
                    <div class="error">
                        <span class="material-icons">error_outline</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            @if (config('dullahan.recaptcha.enabled') && config('dullahan.recaptcha.site_key'))
                @if (config('dullahan.recaptcha.version') === 'v3')
                    <input id="g-recaptcha-response" type="hidden" name="g-recaptcha-response">
                @else
                    <div class="recaptcha">
                        <div class="g-recaptcha" data-sitekey="{{ config('dullahan.recaptcha.site_key') }}"></div>
                    </div>
                @endif
                @error('g-recaptcha-response')
                    <div class="error">
                        <span class="material-icons">error_outline</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            @endif

            <button type="submit">Sign in</button>
            
            <div class="footer">
                <span>Dullahan v{{ \WaqasYousaf\Dullahan\DullahanServiceProvider::version() }}</span>
            </div>
        </form>
    </div>

    @if (config('dullahan.recaptcha.enabled') && config('dullahan.recaptcha.site_key') && config('dullahan.recaptcha.version') === 'v3')
        <script>
            const loginForm = document.getElementById('dullahan-login-form');
            loginForm.addEventListener('submit', event => {
                event.preventDefault();
                grecaptcha.ready(() => {
                    grecaptcha.execute(@json(config('dullahan.recaptcha.site_key')), { action: 'dullahan_login' })
                        .then(token => {
                            document.getElementById('g-recaptcha-response').value = token;
                            loginForm.submit();
                        });
                });
            });
        </script>
    @endif
</body>
</html>
