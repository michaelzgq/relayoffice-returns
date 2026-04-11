<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Dossentry</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #2563eb;
            --accent-soft: #dbeafe;
            --max: 920px;
            --shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top center, rgba(219, 234, 254, 0.34) 0%, rgba(255, 255, 255, 0.94) 32%, #f8fafc 72%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(var(--max), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            padding: 24px 0 16px;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.2);
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .page {
            padding: 12px 0 56px;
        }

        .page-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 28px;
            box-shadow: var(--shadow);
            padding: 40px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 20px 0 14px;
            font-size: clamp(34px, 5vw, 54px);
            line-height: 1.02;
            letter-spacing: -0.04em;
        }

        .lede {
            margin: 0;
            max-width: 62ch;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.8;
        }

        .meta {
            margin-top: 18px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }

        .section {
            margin-top: 34px;
            padding-top: 28px;
            border-top: 1px solid var(--line);
        }

        .section h2 {
            margin: 0 0 14px;
            font-size: 22px;
            letter-spacing: -0.03em;
        }

        .section p,
        .section li {
            color: #334155;
            font-size: 16px;
            line-height: 1.8;
        }

        .section ul {
            margin: 12px 0 0 0;
            padding-left: 20px;
        }

        .section strong {
            color: var(--ink);
        }

        .note {
            margin-top: 20px;
            padding: 16px 18px;
            border-radius: 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 600;
        }

        .footer {
            margin-top: 26px;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 720px) {
            .topbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-card {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell topbar-inner">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-mark">D</span>
                <span>Dossentry</span>
            </a>

            <nav class="nav-links">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}">Terms of Service</a>
                <a href="https://demo.dossentry.com/admin/auth/login">Guest Demo</a>
            </nav>
        </div>
    </header>

    <main class="page">
        <div class="shell">
            <article class="page-card">
                @yield('content')
            </article>
            <div class="footer">
                Dossentry legal information for public website and hosted evaluation demo.
            </div>
        </div>
    </main>
</body>
</html>
