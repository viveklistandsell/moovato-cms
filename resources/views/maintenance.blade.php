<!DOCTYPE html>
<html lang="{{ $locale ?? str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    @php($s = App\Models\SiteSetting::current())
    @php($favicon = $s->favicon_path ? '/storage/'.ltrim($s->favicon_path, '/') : '/favicon.ico')
    <link rel="icon" href="{{ $favicon }}">
    <meta name="theme-color" content="{{ $themeColor }}">
    <title>{{ $heading }} — {{ $siteName }}</title>

    <style>
        :root {
            --accent: {{ $themeColor }};
            color-scheme: light dark;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #0a0a0a;
            color: #fafafa;
            overflow-x: hidden;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 2rem 1.5rem;
        }

        /* Animated gradient backdrop */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.18), transparent 45%),
                radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.18), transparent 45%),
                radial-gradient(circle at 50% 100%, rgba(236, 72, 153, 0.12), transparent 60%);
            pointer-events: none;
            z-index: 0;
            animation: drift 18s ease-in-out infinite alternate;
        }

        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-2%, 2%) scale(1.05); }
        }

        /* Subtle grid */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, rgba(0,0,0,1) 30%, transparent 80%);
            pointer-events: none;
            z-index: 0;
        }

        header.brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: -0.01em;
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.625rem;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            overflow: hidden;
        }
        .brand-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        main {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 0;
        }

        .card {
            max-width: 38rem;
            text-align: center;
            opacity: 0;
            transform: translateY(20px);
            animation: rise 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes rise {
            to { opacity: 1; transform: translateY(0); }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.875rem;
            border-radius: 9999px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #c7d2fe;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.75rem;
        }
        .pulse {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background: #818cf8;
            box-shadow: 0 0 0 0 rgba(129, 140, 248, 0.7);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(129, 140, 248, 0.7); }
            70%  { box-shadow: 0 0 0 10px rgba(129, 140, 248, 0); }
            100% { box-shadow: 0 0 0 0 rgba(129, 140, 248, 0); }
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            letter-spacing: -0.035em;
            margin: 0 0 1.25rem;
            line-height: 1.1;
            background: linear-gradient(180deg, #ffffff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        p.message {
            font-size: 1.0625rem;
            line-height: 1.7;
            color: #d4d4d8;
            margin: 0 auto 2.5rem;
            max-width: 32rem;
        }

        /* Animated dot loader */
        .dots {
            display: inline-flex;
            gap: 0.5rem;
            margin-bottom: 2.5rem;
        }
        .dots span {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background: var(--accent);
            opacity: 0.4;
            animation: bounce 1.4s ease-in-out infinite;
        }
        .dots span:nth-child(2) { animation-delay: 0.16s; }
        .dots span:nth-child(3) { animation-delay: 0.32s; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40%           { transform: scale(1);   opacity: 1; }
        }

        footer {
            position: relative;
            z-index: 2;
            text-align: center;
            font-size: 0.75rem;
            color: #71717a;
            letter-spacing: 0.04em;
        }

        @media (prefers-color-scheme: light) {
            html, body { background: #fafafa; color: #18181b; }
            body::before { opacity: 0.5; }
            body::after { mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.7) 30%, transparent 80%); }
            .badge { background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.2); color: #4338ca; }
            h1 { background: linear-gradient(180deg, #18181b 0%, #71717a 100%); -webkit-background-clip: text; background-clip: text; color: transparent; }
            p.message { color: #3f3f46; }
            footer { color: #a1a1aa; }
        }
    </style>
</head>
<body>
    <header class="brand">
        <span class="brand-logo">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}">
            @else
                {{ mb_substr($siteName, 0, 1) }}
            @endif
        </span>
        <span>{{ $siteName }}</span>
    </header>

    <main>
        <div class="card">
            <span class="badge">
                <span class="pulse"></span>
                <span>{{ $locale === 'de' ? 'Wartungsmodus' : 'Maintenance mode' }}</span>
            </span>
            <h1>{{ $heading }}</h1>
            <p class="message">{{ $message }}</p>
            <div class="dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </div>
    </main>

    <footer>
        © {{ now()->year }} {{ $siteName }} · HTTP 503
    </footer>
</body>
</html>
