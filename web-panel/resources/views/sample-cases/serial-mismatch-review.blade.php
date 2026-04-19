<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.marketing-meta', [
        'metaTitle' => $appName . ' | Sample Case: Serial Mismatch Review',
        'metaDescription' => 'See a sample warehouse-side return exception record showing serial mismatch evidence, hold posture, and brand-ready review context.',
        'metaImage' => $sampleCaseAssets['compareBoard'],
        'metaImageAlt' => 'Dossentry sample serial mismatch review board.',
    ])
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/dossentry/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #f4f8fc;
            --ink: #10233b;
            --muted: #617284;
            --line: #d8e1eb;
            --accent: #2563eb;
            --accent-deep: #1d4ed8;
            --danger: #dc2626;
            --danger-soft: #fff3f2;
            --success: #147a52;
            --shadow-soft: 0 22px 52px rgba(15, 23, 42, 0.09);
            --shadow-card: 0 10px 24px rgba(15, 23, 42, 0.05);
            --max: 1180px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top center, rgba(219, 234, 254, 0.5) 0%, rgba(255, 255, 255, 0.96) 34%, #f8fafc 70%),
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
            grid-template-columns: minmax(0, 0.98fr) minmax(320px, 1.02fr);
            gap: 32px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1, h2, h3 { margin: 0; letter-spacing: -0.04em; }

        h1 {
            margin-top: 18px;
            font-size: clamp(40px, 5vw, 62px);
            line-height: 1.02;
            font-weight: 900;
            max-width: 12ch;
        }

        .hero-copy p,
        .section-copy p,
        .timeline-item p,
        .note-card p {
            color: var(--muted);
            line-height: 1.75;
        }

        .hero-copy p {
            margin: 18px 0 0;
            max-width: 38rem;
            font-size: 18px;
        }

        .hero-actions {
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

        .hero-panel img {
            display: block;
            width: 100%;
            height: auto;
        }

        .hero-panel-note {
            padding: 14px 16px 18px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
            gap: 24px;
            align-items: start;
        }

        .video-shell {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 24px;
            background: #0f172a;
            box-shadow: var(--shadow-card);
        }

        .video-shell video {
            display: block;
            width: 100%;
            height: auto;
            background: #020617;
        }

        .demo-copy {
            display: grid;
            gap: 14px;
        }

        .demo-point {
            padding: 18px 20px;
            border: 1px solid var(--line);
            border-radius: 20px;
            background: var(--surface-soft);
        }

        .demo-point h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .demo-point p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .main {
            padding: 28px 0 40px;
        }

        .facts-grid,
        .summary-grid,
        .gallery-grid,
        .timeline-grid {
            display: grid;
            gap: 18px;
        }

        .facts-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .fact-card,
        .summary-card,
        .note-card,
        .timeline-item,
        .photo-card {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
        }

        .fact-card {
            padding: 18px;
            min-height: 126px;
        }

        .fact-label {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .fact-value {
            font-size: 18px;
            line-height: 1.4;
            font-weight: 800;
            color: var(--ink);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(220, 38, 38, 0.15);
            background: var(--danger-soft);
            color: var(--danger);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .section-card {
            margin-top: 22px;
            padding: 28px;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-size: 28px;
            line-height: 1.08;
        }

        .section-head p {
            margin: 10px 0 0;
            max-width: 56ch;
            color: var(--muted);
            line-height: 1.7;
        }

        .summary-grid {
            grid-template-columns: minmax(0, 1.18fr) minmax(280px, 0.82fr);
        }

        .summary-card,
        .note-card {
            padding: 22px;
        }

        .summary-card h3,
        .note-card h3 {
            font-size: 18px;
            margin-bottom: 12px;
        }

        .summary-list {
            display: grid;
            gap: 10px;
        }

        .summary-item {
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--surface-soft);
            border: 1px solid rgba(216, 225, 235, 0.9);
        }

        .summary-item strong {
            display: block;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .summary-item span {
            display: block;
            color: var(--ink);
            line-height: 1.65;
            font-weight: 600;
        }

        .note-card {
            background: linear-gradient(180deg, #fffefe 0%, #fff6f5 100%);
            border-color: rgba(220, 38, 38, 0.14);
        }

        .gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .photo-card {
            overflow: hidden;
        }

        .photo-card img {
            display: block;
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: #eef2f7;
        }

        .photo-meta {
            padding: 14px 16px 18px;
        }

        .photo-meta h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .photo-meta p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .timeline-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .timeline-item {
            padding: 18px;
        }

        .timeline-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--accent);
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .timeline-item h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .timeline-item p {
            margin: 0;
            font-size: 14px;
        }

        .cta-panel {
            margin-top: 22px;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .cta-panel h2 {
            font-size: 28px;
            line-height: 1.08;
        }

        .cta-panel p {
            margin: 10px 0 0;
            color: var(--muted);
            line-height: 1.7;
            max-width: 44rem;
        }

        .cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .footer {
            padding: 0 0 34px;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 1040px) {
            .facts-grid,
            .timeline-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            .demo-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .shell {
                width: min(var(--max), calc(100% - 24px));
            }

            .hero,
            .section-card,
            .cta-panel {
                padding: 22px;
                border-radius: 22px;
            }

            .facts-grid,
            .gallery-grid,
            .timeline-grid {
                grid-template-columns: 1fr;
            }

            .photo-card img {
                height: 240px;
            }

            h1 {
                max-width: none;
                font-size: clamp(34px, 10vw, 48px);
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

            <nav class="topnav">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ route('compare.generic-inspection-apps') }}">Compare</a>
                <a href="#sample-video" data-track-page="sample_case" data-track-placement="topnav" data-track-cta="watch_video">Sample Video</a>
                <a href="{{ route('sample-cases.serial-mismatch.pdf') }}" data-track-page="sample_case" data-track-placement="topnav" data-track-cta="download_pdf">Sample PDF</a>
                <a href="{{ route('landing') }}#review-request" data-track-page="sample_case" data-track-placement="topnav" data-track-cta="workflow_review">Workflow Review</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="shell">
            <section class="hero">
                <div class="hero-grid">
                    <div class="hero-copy">
                        <span class="eyebrow">Sample Case Only</span>
                        <h1>Serial mismatch caught before the case moved forward.</h1>
                        <p>
                            This illustrative workflow example shows how a warehouse team can document an identity mismatch,
                            hold the case, and send one review-ready record instead of rebuilding the story from folders,
                            screenshots, and chat threads.
                        </p>
                        <div class="hero-actions">
                            <a class="button button-primary" href="#sample-video" data-track-page="sample_case" data-track-placement="hero" data-track-cta="watch_video">Watch 77-second Demo</a>
                            <a class="button button-primary" href="{{ $sampleCasePdfUrl }}" data-track-page="sample_case" data-track-placement="hero" data-track-cta="download_pdf">Download Sample PDF</a>
                            <a class="button button-secondary" href="{{ route('landing') }}#review-request" data-track-page="sample_case" data-track-placement="hero" data-track-cta="workflow_review">Request Workflow Review</a>
                        </div>
                        <div class="hero-proof">
                            <span>Illustrative workflow example</span>
                            <span>77-second walkthrough</span>
                            <span>Real handling photos</span>
                            <span>Warehouse-side evidence</span>
                            <span>Hold before release</span>
                        </div>
                    </div>

                    <div class="hero-panel">
                        <img src="{{ $sampleCaseAssets['compareBoard'] }}" alt="Sample serial mismatch review board">
                        <div class="hero-panel-note">
                            The expected return record and the observed carton label no longer match. That gap is what triggers hold posture, review context, and evidence capture.
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-card" id="sample-video">
                <div class="section-head">
                    <div>
                        <h2>77-second sample walkthrough</h2>
                        <p>This short demo shows the public sample case the same way a prospect would see it: one review-ready record, one hold reason, and one shareable workflow asset instead of scattered screenshots.</p>
                    </div>
                </div>

                <div class="demo-grid">
                    <div class="video-shell">
                        <video controls playsinline preload="metadata" poster="{{ $sampleCaseAssets['compareBoard'] }}">
                            <source src="{{ $sampleCaseVideoUrl }}" type="video/mp4">
                        </video>
                    </div>

                    <div class="demo-copy">
                        <article class="demo-point">
                            <h3>What the video covers</h3>
                            <p>Home page entry, sample-case summary, evidence capture, hold posture, and the reason this exception should not move forward from memory.</p>
                        </article>
                        <article class="demo-point">
                            <h3>How to use it</h3>
                            <p>Use the page link when you want a prospect to click around, and use this embedded walkthrough when you need a fast first impression without asking them to read the full case.</p>
                        </article>
                        <article class="demo-point">
                            <h3>Outbound-safe framing</h3>
                            <p>Keep calling it a sample case and an illustrative workflow example. The value is clarity, not pretending this is a named customer win.</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2>Case snapshot</h2>
                        <p>This sample is designed to show exactly what a buyer needs to understand in under one minute: what the warehouse expected, what the team observed, why the case stayed on hold, and what the brand reviewer should see next.</p>
                    </div>
                </div>

                <div class="facts-grid">
                    <div class="fact-card">
                        <div class="fact-label">Return ID</div>
                        <div class="fact-value">{{ $sampleCaseFacts['returnId'] }}</div>
                    </div>
                    <div class="fact-card">
                        <div class="fact-label">Expected SKU</div>
                        <div class="fact-value">{{ $sampleCaseFacts['expectedSku'] }}</div>
                    </div>
                    <div class="fact-card">
                        <div class="fact-label">Expected Serial</div>
                        <div class="fact-value">{{ $sampleCaseFacts['expectedSerial'] }}</div>
                    </div>
                    <div class="fact-card">
                        <div class="fact-label">Observed Label</div>
                        <div class="fact-value">{{ $sampleCaseFacts['observedLabel'] }}</div>
                    </div>
                    <div class="fact-card">
                        <div class="fact-label">Current Status</div>
                        <div class="status-pill">{{ $sampleCaseFacts['status'] }}</div>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2>Why the case was held</h2>
                        <p>The warehouse expected one unit identity but observed a different carton label during inspection. Because the client playbook requires serial verification, the case could not move to release-ready without review.</p>
                    </div>
                </div>

                <div class="summary-grid">
                    <div class="summary-card">
                        <h3>External-facing summary</h3>
                        <div class="summary-list">
                            <div class="summary-item">
                                <strong>Expected record</strong>
                                <span>The return record expected SKU {{ $sampleCaseFacts['expectedSku'] }} with serial {{ $sampleCaseFacts['expectedSerial'] }}.</span>
                            </div>
                            <div class="summary-item">
                                <strong>Observed during inspection</strong>
                                <span>The received carton label showed {{ $sampleCaseFacts['observedLabel'] }}, which does not match the expected record.</span>
                            </div>
                            <div class="summary-item">
                                <strong>Why it matters</strong>
                                <span>An identity mismatch is a high-risk exception. The warehouse documented the discrepancy, opened the unit for verification, and maintained hold posture pending review.</span>
                            </div>
                            <div class="summary-item">
                                <strong>Next reviewer action</strong>
                                <span>Review the comparison board, carton labels, and opened-unit photos before any refund release or final disposition.</span>
                            </div>
                        </div>
                    </div>

                    <div class="note-card">
                        <h3>Inspector note</h3>
                        <p>
                            Expected unit record does not match the carton label observed during inspection. Received carton shows model and serial information inconsistent with the expected return record. Unit was opened for verification and the case should remain on hold pending brand review.
                        </p>
                    </div>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2>Evidence captured before release</h2>
                        <p>This gallery keeps the workflow grounded in warehouse reality. The point is not that the images are dramatic. The point is that the warehouse can show what was seen, what was checked, and why the case did not move forward automatically.</p>
                    </div>
                </div>

                <div class="gallery-grid">
                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['receivedOverview'] }}" alt="Received cartons overview">
                        <div class="photo-meta">
                            <h3>Received-carton overview</h3>
                            <p>High-level context of the inbound cartons and handling environment before the case is opened.</p>
                        </div>
                    </article>

                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['observedLabels'] }}" alt="Observed carton labels">
                        <div class="photo-meta">
                            <h3>Observed carton labels</h3>
                            <p>Close enough to read the carton identity that triggered the exception path.</p>
                        </div>
                    </article>

                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['cartonStack'] }}" alt="Carton stack view">
                        <div class="photo-meta">
                            <h3>Secondary label angle</h3>
                            <p>Additional carton view to reinforce that the mismatch was not a one-angle reading error.</p>
                        </div>
                    </article>

                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['palletAngle'] }}" alt="Pallet angle">
                        <div class="photo-meta">
                            <h3>Label context on pallet</h3>
                            <p>Shows how the warehouse can capture proof in the flow of normal handling, not only at a dedicated station.</p>
                        </div>
                    </article>

                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['openedCase'] }}" alt="Opened case interior">
                        <div class="photo-meta">
                            <h3>Opened unit verification</h3>
                            <p>The unit was opened for verification after the identity mismatch was found, instead of being pushed forward from assumption.</p>
                        </div>
                    </article>

                    <article class="photo-card">
                        <img src="{{ $sampleCaseAssets['openedLid'] }}" alt="Opened lid verification">
                        <div class="photo-meta">
                            <h3>Interior follow-up check</h3>
                            <p>Secondary interior view used to show the item state and support the hold decision.</p>
                        </div>
                    </article>
                </div>
            </section>

            <section class="section-card">
                <div class="section-head">
                    <div>
                        <h2>Timeline the buyer can understand</h2>
                        <p>The workflow is intentionally simple: receive, compare, verify, hold, and share one review-ready record.</p>
                    </div>
                </div>

                <div class="timeline-grid">
                    <article class="timeline-item">
                        <div class="timeline-step">1</div>
                        <h3>Received</h3>
                        <p>Inbound carton enters the warehouse and is attached to the return record.</p>
                    </article>
                    <article class="timeline-item">
                        <div class="timeline-step">2</div>
                        <h3>Mismatch observed</h3>
                        <p>The carton label does not match the expected SKU and serial in the return record.</p>
                    </article>
                    <article class="timeline-item">
                        <div class="timeline-step">3</div>
                        <h3>Opened for verification</h3>
                        <p>The unit is opened and documented so the warehouse has defensible evidence, not just a suspicion.</p>
                    </article>
                    <article class="timeline-item">
                        <div class="timeline-step">4</div>
                        <h3>Held for review</h3>
                        <p>The case stays on hold until the next reviewer confirms what should happen next.</p>
                    </article>
                </div>
            </section>

            <section class="cta-panel">
                <div class="cta-copy">
                    <h2>This is the kind of return case that should not be handled from memory.</h2>
                    <p>Use this sample when you need to show a 3PL how Dossentry handles high-risk return exceptions across brands without pretending it is a real customer success story.</p>
                </div>
                <div class="cta-actions">
                    <a class="button button-primary" href="{{ $sampleCasePdfUrl }}" data-track-page="sample_case" data-track-placement="cta" data-track-cta="download_pdf">Download Sample PDF</a>
                    <a class="button button-secondary" href="{{ route('landing') }}#review-request" data-track-page="sample_case" data-track-placement="cta" data-track-cta="workflow_review">Request Workflow Review</a>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <div>Sample case only. Illustrative workflow example using real handling photos.</div>
            <nav class="footer-links">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ route('compare.generic-inspection-apps') }}">Compare</a>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}">Terms of Service</a>
            </nav>
        </div>
    </footer>

    @include('partials.marketing-click-tracking', ['pageKey' => 'sample_case'])
</body>
</html>
