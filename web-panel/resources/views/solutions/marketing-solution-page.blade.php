<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.marketing-meta', [
        'metaTitle' => $metaTitle,
        'metaDescription' => $metaDescription,
        'metaImage' => $metaImage,
        'metaImageAlt' => $metaImageAlt,
    ])
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/dossentry/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #f3f7fc;
            --ink: #0f172a;
            --muted: #617284;
            --line: #d8e1eb;
            --accent: #2563eb;
            --accent-deep: #1d4ed8;
            --accent-soft: #eff6ff;
            --shadow-soft: 0 20px 48px rgba(15, 23, 42, 0.08);
            --shadow-card: 0 10px 26px rgba(15, 23, 42, 0.05);
            --max: 1180px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top center, rgba(219, 234, 254, 0.44) 0%, rgba(255, 255, 255, 0.96) 36%, #f8fafc 68%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(var(--max), calc(100% - 40px));
            margin: 0 auto;
        }

        .topbar {
            padding: 22px 0 14px;
        }

        .topbar-inner,
        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
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
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
        }

        .topnav,
        .footer-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 22px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .topnav a:hover,
        .footer-links a:hover {
            color: var(--accent);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 14px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 800;
            transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease, border-color 160ms ease, color 160ms ease;
        }

        .button:hover { transform: translateY(-1px); }

        .button-primary {
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(37, 99, 235, 0.22);
        }

        .button-secondary {
            background: #ffffff;
            color: var(--ink);
            border-color: var(--line);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .hero,
        .section-card,
        .faq-panel,
        .cta-panel {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(216, 225, 235, 0.95);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
        }

        .hero {
            padding: 40px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.92fr);
            gap: 32px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1, h2, h3, h4 { margin: 0; letter-spacing: -0.04em; }

        h1 {
            margin-top: 18px;
            font-size: clamp(42px, 5vw, 64px);
            line-height: 1.02;
            font-weight: 900;
            max-width: 12ch;
        }

        .hero-copy p,
        .proof-copy p,
        .faq-intro p,
        .cta-copy p,
        .section-card p {
            color: var(--muted);
            line-height: 1.75;
        }

        .hero-copy p {
            margin: 18px 0 0;
            max-width: 40rem;
            font-size: 18px;
        }

        .hero-actions,
        .proof-actions,
        .cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 24px;
        }

        .hero-proof {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .hero-proof span {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #ffffff;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .hero-panel {
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: var(--shadow-card);
        }

        .hero-panel img,
        .hero-panel video {
            display: block;
            width: 100%;
            height: auto;
            background: #0f172a;
        }

        .hero-panel-note {
            padding: 14px 16px 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .main {
            padding: 28px 0 40px;
        }

        .section-stack {
            display: grid;
            gap: 22px;
        }

        .section-card {
            padding: 30px;
            display: grid;
            gap: 12px;
        }

        .section-kicker {
            color: var(--accent);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .section-card h2 {
            font-size: 30px;
            line-height: 1.08;
            max-width: 22ch;
        }

        .proof-grid,
        .faq-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 0.9fr);
            gap: 22px;
            align-items: start;
        }

        .proof-copy,
        .faq-intro {
            padding: 28px;
        }

        .proof-copy h3,
        .faq-intro h3 {
            font-size: 28px;
            line-height: 1.1;
        }

        .proof-copy p,
        .faq-intro p {
            margin: 14px 0 0;
        }

        .bullet-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .bullet-item {
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: var(--surface-soft);
            color: #334155;
            font-size: 15px;
            line-height: 1.7;
        }

        .faq-panel {
            padding: 18px;
            display: grid;
            gap: 12px;
        }

        .faq-item {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #ffffff;
            overflow: hidden;
        }

        .faq-item summary {
            list-style: none;
            cursor: pointer;
            padding: 18px 20px;
            font-size: 15px;
            font-weight: 800;
            color: var(--ink);
        }

        .faq-item summary::-webkit-details-marker { display: none; }

        .faq-answer {
            padding: 0 20px 18px;
            color: var(--muted);
            line-height: 1.75;
        }

        .cta-panel {
            margin-top: 22px;
            padding: 30px;
        }

        .cta-copy h2 {
            font-size: 30px;
            line-height: 1.08;
            max-width: 18ch;
        }

        .cta-copy p {
            margin: 12px 0 0;
            max-width: 42rem;
        }

        .related-links {
            display: flex;
            flex-wrap: wrap;
            gap: 14px 18px;
            margin-top: 18px;
            color: #475569;
            font-size: 14px;
            font-weight: 700;
        }

        .related-links a {
            color: var(--accent);
        }

        .footer {
            padding: 0 0 34px;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 1040px) {
            .hero-grid,
            .proof-grid,
            .faq-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .shell {
                width: min(var(--max), calc(100% - 24px));
            }

            .hero,
            .section-card,
            .proof-copy,
            .faq-intro,
            .faq-panel,
            .cta-panel {
                padding: 22px;
                border-radius: 22px;
            }

            h1 {
                max-width: none;
                font-size: clamp(36px, 10vw, 50px);
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell topbar-inner">
            <a class="brand" href="{{ route('landing') }}">
                <span class="brand-mark">D</span>
                <span>{{ $appName }}</span>
            </a>

            <nav class="topnav">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ route('compare.generic-inspection-apps') }}">Compare</a>
                <a href="{{ route('sample-cases.serial-mismatch') }}">Sample Case</a>
                @foreach($navLinks as $navLink)
                    <a href="{{ $navLink['url'] }}">{{ $navLink['label'] }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="shell">
            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <span class="eyebrow">{{ $eyebrow }}</span>
                        <h1>{{ $title }}</h1>
                        <p>{{ $intro }}</p>
                        <div class="hero-actions">
                            <a class="button button-primary" href="{{ $primaryCtaUrl }}" data-track-page="{{ $pageKey }}" data-track-placement="hero" data-track-cta="{{ $primaryCtaTrack }}">{{ $primaryCtaLabel }}</a>
                            <a class="button button-secondary" href="{{ $secondaryCtaUrl }}" data-track-page="{{ $pageKey }}" data-track-placement="hero" data-track-cta="{{ $secondaryCtaTrack }}">{{ $secondaryCtaLabel }}</a>
                        </div>
                        <div class="hero-proof">
                            @foreach($proofPills as $pill)
                                <span>{{ $pill }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="hero-panel">
                        @if($visualType === 'video')
                            <video controls playsinline preload="metadata" poster="{{ $visualPoster }}">
                                <source src="{{ $visualSrc }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ $visualSrc }}" alt="{{ $visualAlt }}">
                        @endif
                        <div class="hero-panel-note">
                            {{ $visualNote }}
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-stack">
                @foreach($sections as $section)
                    <article class="section-card">
                        <span class="section-kicker">{{ $section['kicker'] }}</span>
                        <h2>{{ $section['title'] }}</h2>
                        <p>{{ $section['copy'] }}</p>
                    </article>
                @endforeach
            </section>

            <section class="section-card" style="margin-top: 22px;">
                <div class="proof-grid">
                    <div class="proof-copy">
                        <span class="section-kicker">Proof Asset</span>
                        <h3>{{ $proofTitle }}</h3>
                        <p>{{ $proofBody }}</p>
                        <div class="proof-actions">
                            <a class="button button-primary" href="{{ route('sample-cases.serial-mismatch') }}" data-track-page="{{ $pageKey }}" data-track-placement="proof" data-track-cta="sample_case">View Sample Case</a>
                            <a class="button button-secondary" href="{{ route('compare.generic-inspection-apps') }}" data-track-page="{{ $pageKey }}" data-track-placement="proof" data-track-cta="compare">Compare View</a>
                        </div>
                    </div>

                    <div class="bullet-list">
                        @foreach($proofBullets as $bullet)
                            <div class="bullet-item">{{ $bullet }}</div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="section-card" style="margin-top: 22px;">
                <div class="faq-grid">
                    <div class="faq-intro">
                        <span class="section-kicker">FAQ</span>
                        <h3>What buyers usually ask next</h3>
                        <p>These pages are meant to make the workflow easier to understand, not to pretend Dossentry replaces every system in the stack.</p>
                    </div>

                    <div class="faq-panel">
                        @foreach($faq as $item)
                            <details class="faq-item">
                                <summary>{{ $item['question'] }}</summary>
                                <div class="faq-answer">{{ $item['answer'] }}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="cta-panel">
                <div class="cta-copy">
                    <span class="section-kicker">Next Step</span>
                    <h2>{{ $title }}</h2>
                    <p>If this workflow maps to the way your team already handles messy return cases, start with the sample case, then request a workflow review against your current process.</p>
                </div>
                <div class="cta-actions">
                    <a class="button button-primary" href="{{ $primaryCtaUrl }}" data-track-page="{{ $pageKey }}" data-track-placement="cta" data-track-cta="{{ $primaryCtaTrack }}">{{ $primaryCtaLabel }}</a>
                    <a class="button button-secondary" href="{{ $secondaryCtaUrl }}" data-track-page="{{ $pageKey }}" data-track-placement="cta" data-track-cta="{{ $secondaryCtaTrack }}">{{ $secondaryCtaLabel }}</a>
                </div>
                <div class="related-links">
                    @foreach($relatedLinks as $relatedLink)
                        <a href="{{ $relatedLink['url'] }}" data-track-page="{{ $pageKey }}" data-track-placement="related" data-track-cta="related_link">{{ $relatedLink['label'] }}</a>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <div>{{ $appName }}. Warehouse-side return exception workflow and review-ready evidence.</div>
            <nav class="footer-links">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ route('sample-cases.serial-mismatch') }}">Sample Case</a>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}">Terms of Service</a>
            </nav>
        </div>
    </footer>

    @include('partials.marketing-click-tracking', ['pageKey' => $pageKey])
</body>
</html>
