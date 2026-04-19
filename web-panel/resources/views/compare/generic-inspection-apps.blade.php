<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.marketing-meta', [
        'metaTitle' => $appName . ' | Compare Dossentry vs Generic Inspection Apps',
        'metaDescription' => 'See why generic inspection apps stop at checklists while Dossentry creates a brand-ready return record for disputed warehouse-side returns.',
        'metaImage' => asset('assets/dossentry/og-compare.png'),
        'metaImageAlt' => 'Dossentry compared with generic inspection apps for warehouse-side returns.',
    ])
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/dossentry/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-soft: #eff6ff;
            --surface-warm: #fffaf2;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #dbe4ef;
            --accent: #2563eb;
            --accent-deep: #1d4ed8;
            --shadow-soft: 0 18px 48px rgba(15, 23, 42, 0.08);
            --shadow-card: 0 10px 26px rgba(15, 23, 42, 0.05);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --max: 1180px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            overflow-x: hidden;
            background:
                radial-gradient(circle at top center, rgba(219, 234, 254, 0.56) 0%, rgba(255, 255, 255, 0.94) 36%, #f8fafc 72%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

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
            gap: 20px;
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
            gap: 24px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .topnav a:hover,
        .footer-links a:hover {
            color: var(--accent);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 0;
            min-height: 46px;
            padding: 0 20px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 800;
            border: 1px solid transparent;
            line-height: 1.25;
            text-align: center;
            white-space: normal;
            overflow-wrap: anywhere;
            transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease, border-color 180ms ease, color 180ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(37, 99, 235, 0.22);
        }

        .button-secondary {
            background: #ffffff;
            color: var(--ink);
            border-color: var(--line);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }

        .button-quiet {
            color: var(--accent);
            border-color: rgba(37, 99, 235, 0.14);
            background: transparent;
        }

        .hero {
            padding: 26px 0 38px;
        }

        .hero-card,
        .comparison-card,
        .section-card,
        .cta-panel {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(219, 228, 239, 0.94);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-soft);
        }

        .hero-card {
            padding: 42px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(340px, 0.96fr);
            gap: 34px;
            align-items: center;
        }

        .eyebrow,
        .section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--surface-soft);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        h4 {
            margin: 0;
            letter-spacing: -0.04em;
        }

        h1 {
            margin-top: 20px;
            font-size: clamp(42px, 5vw, 66px);
            line-height: 1.02;
            font-weight: 900;
            max-width: 11ch;
        }

        .hero-copy p,
        .section-head p,
        .section-copy p,
        .cta-copy p {
            color: var(--muted);
            line-height: 1.75;
        }

        .hero-copy p {
            margin: 20px 0 0;
            max-width: 34rem;
            font-size: 18px;
        }

        .hero-copy,
        .hero-panel,
        .hero-actions > *,
        .cta-actions > *,
        .topbar-actions > * {
            min-width: 0;
        }

        .hero-actions,
        .cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .hero-proof {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .hero-proof-pill {
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
            padding: 24px;
            border-radius: var(--radius-lg);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(219, 228, 239, 0.96);
            box-shadow: var(--shadow-card);
        }

        .hero-panel h3 {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.1;
        }

        .hero-list,
        .bullet-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .hero-item,
        .bullet-item {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: #ffffff;
        }

        .hero-item strong,
        .bullet-item strong {
            display: block;
            font-size: 14px;
            font-weight: 800;
        }

        .hero-item span,
        .bullet-item span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .section {
            padding: 0 0 30px;
        }

        .section-head {
            margin-bottom: 18px;
        }

        .section-head h2 {
            margin-top: 18px;
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.05;
            font-weight: 900;
            max-width: 12ch;
        }

        .comparison-card {
            overflow: hidden;
        }

        .comparison-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 8px;
        }

        .comparison-hint {
            display: none;
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.5;
        }

        table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 18px 20px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid var(--line);
        }

        th {
            background: #f8fbff;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
        }

        td strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
            color: var(--ink);
        }

        td span {
            display: block;
            margin-top: 6px;
            color: var(--muted);
            line-height: 1.65;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .section-card {
            padding: 24px;
        }

        .section-card h3 {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.1;
        }

        .section-copy {
            margin-top: 14px;
        }

        .section-copy p {
            margin: 0;
            font-size: 15px;
        }

        .faq-grid {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 24px;
        }

        .faq-panel {
            padding: 10px 22px;
            border-radius: var(--radius-xl);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(219, 228, 239, 0.94);
            box-shadow: var(--shadow-soft);
        }

        .faq-intro h3 {
            font-size: 32px;
            line-height: 1.05;
            font-weight: 900;
        }

        .faq-intro p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.72;
        }

        .faq-item {
            border-top: 1px solid var(--line);
        }

        .faq-item:first-child {
            border-top: 0;
        }

        .faq-item summary {
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 0;
            cursor: pointer;
            font-size: 15px;
            font-weight: 800;
        }

        .faq-item summary::-webkit-details-marker {
            display: none;
        }

        .faq-item summary::after {
            content: "+";
            color: var(--muted);
            font-size: 22px;
            font-weight: 400;
            line-height: 1;
        }

        .faq-item[open] summary::after {
            content: "-";
        }

        .faq-answer {
            padding: 0 0 18px;
            color: var(--muted);
            line-height: 1.72;
            font-size: 15px;
        }

        .cta {
            padding: 0 0 90px;
        }

        .cta-panel {
            padding: 30px;
        }

        .cta-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
            gap: 24px;
            align-items: center;
        }

        .cta-copy h2 {
            font-size: clamp(34px, 4vw, 48px);
            line-height: 1.04;
            font-weight: 900;
            max-width: 12ch;
        }

        .cta-notes {
            display: grid;
            gap: 14px;
        }

        .cta-note {
            padding: 18px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .cta-note strong {
            display: block;
            font-size: 15px;
            font-weight: 800;
        }

        .cta-note p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.65;
            font-size: 14px;
        }

        .footer {
            padding: 0 0 28px;
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 980px) {
            .hero-grid,
            .section-grid,
            .faq-grid,
            .cta-grid {
                grid-template-columns: 1fr;
            }

            .topbar-inner,
            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .topnav,
            .footer-links {
                flex-wrap: wrap;
                gap: 14px;
            }
        }

        @media (max-width: 760px) {
            .shell {
                width: min(var(--max), calc(100% - 24px));
            }

            .topbar-inner {
                align-items: flex-start;
            }

            .topbar-actions {
                width: 100%;
                display: grid;
                grid-template-columns: minmax(0, 0.82fr) minmax(0, 1.18fr);
                gap: 10px;
            }

            .topbar-actions .button {
                width: 100%;
            }

            .hero-actions,
            .cta-actions {
                flex-direction: column;
            }

            .hero-card,
            .section-card,
            .cta-panel,
            .faq-panel {
                padding: 22px;
            }

            h1 {
                font-size: clamp(32px, 10vw, 44px);
                max-width: 10ch;
            }

            .topnav {
                gap: 12px;
                font-size: 13px;
            }

            .topbar-actions {
                grid-template-columns: 1fr;
            }

            .comparison-hint {
                display: block;
            }

            .button {
                width: 100%;
                padding: 12px 18px;
            }
        }

        @media (max-width: 420px) {
            .hero-card,
            .section-card,
            .cta-panel,
            .faq-panel {
                padding: 20px;
            }

            .hero-copy p,
            .section-head p,
            .section-copy p,
            .cta-copy p,
            .faq-intro p,
            .faq-answer {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell">
            <div class="topbar-inner">
                <a class="brand" href="{{ route('landing') }}">
                    <span class="brand-mark">D</span>
                    <span>{{ $appName }}</span>
                </a>
                <nav class="topnav">
                    <a href="#comparison">Comparison</a>
                    <a href="#differences">Why It Matters</a>
                    <a href="#faq">FAQ</a>
                </nav>
                <div class="topbar-actions">
                    <a class="button button-secondary" href="{{ route('landing') }}" data-track-page="compare" data-track-placement="topbar" data-track-cta="back_to_site">Back to site</a>
                    <a class="button button-primary" href="{{ $sampleCaseUrl }}" data-track-page="compare" data-track-placement="topbar" data-track-cta="sample_case">View Sample Case</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell">
                <div class="hero-card">
                    <div class="hero-grid">
                        <article class="hero-copy">
                            <span class="eyebrow">Compare</span>
                            <h1>Generic inspection apps collect photos. Dossentry builds a defensible return record.</h1>
                            <p>
                                If a brand questions how your warehouse handled a return, you need more than a checklist
                                and a few images. Dossentry gives your team brand-specific playbooks, evidence completeness,
                                timeline, recommendation, and one shareable review link.
                            </p>
                            <div class="hero-actions">
                                <a class="button button-primary" href="{{ $sampleCaseUrl }}" data-track-page="compare" data-track-placement="hero" data-track-cta="sample_case">View Sample Case</a>
                                <a class="button button-secondary" href="{{ $demoLoginUrl }}" data-track-page="compare" data-track-placement="hero" data-track-cta="guest_demo">Enter Guest Demo</a>
                                <a class="button button-quiet" href="{{ route('landing') }}#review-request" data-track-page="compare" data-track-placement="hero" data-track-cta="workflow_review">Request Workflow Review</a>
                            </div>
                            <div class="hero-proof">
                                <span class="hero-proof-pill">Brand Review Link</span>
                                <span class="hero-proof-pill">Phone-first evidence capture</span>
                                <span class="hero-proof-pill">No station rebuild</span>
                                <span class="hero-proof-pill">Docker self-hosted</span>
                            </div>
                        </article>

                        <aside class="hero-panel">
                            <h3>What generic tools usually stop short of</h3>
                            <div class="hero-list">
                                <div class="hero-item">
                                    <strong>Brand-specific return rules</strong>
                                    <span>One brand needs serial proof. Another needs packaging photos. Another wants damage close-ups before review.</span>
                                </div>
                                <div class="hero-item">
                                    <strong>Evidence completeness</strong>
                                    <span>Not just "photos uploaded" but whether the required evidence set is complete for that case.</span>
                                </div>
                                <div class="hero-item">
                                    <strong>External review record</strong>
                                    <span>One protected Brand Review Link with photos, timeline, rule snapshot, and recommendation.</span>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="comparison">
            <div class="shell">
                <div class="section-head">
                    <span class="section-kicker">Core comparison</span>
                    <h2>Where generic inspection tools stop, disputed return workflows start.</h2>
                    <p>
                        Tools like SafetyCulture, GoAudits, or MaintainX are useful when the job is documenting an inspection.
                        Dossentry is for a different moment: when a returned item becomes a disputed case and your warehouse needs
                        to explain and defend what happened next.
                    </p>
                    <div class="comparison-hint">On mobile, swipe sideways to compare the columns.</div>
                </div>
                <div class="comparison-card comparison-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Generic inspection apps</th>
                                <th>Dossentry</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Primary use case</strong></td>
                                <td><span>General inspections, audits, checklists, and operational walkthroughs.</span></td>
                                <td><span>Disputed returns, brand review, and warehouse-side exception control.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Mobile capture</strong></td>
                                <td><span>Yes, usually for checklists and inspection notes.</span></td>
                                <td><span>Yes, designed around phone-first close-up evidence capture on the warehouse floor.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Brand-specific return rules</strong></td>
                                <td><span>Usually manual or handled as generic templates.</span></td>
                                <td><span>Built around client playbooks with brand-specific evidence and decision requirements.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Evidence completeness</strong></td>
                                <td><span>Photos may be attached, but completeness is rarely a core control.</span></td>
                                <td><span>Required evidence logic makes it clear whether the case is actually review-ready.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Recommendation and decision support</strong></td>
                                <td><span>Usually out of scope or left to notes fields.</span></td>
                                <td><span>Captures recommendation, timeline, and case context in one record.</span></td>
                            </tr>
                            <tr>
                                <td><strong>External brand review link</strong></td>
                                <td><span>Usually not a core workflow output.</span></td>
                                <td><span>Brand Review Link is a first-class output, not an afterthought.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Multi-brand warehouse context</strong></td>
                                <td><span>Weak. Most tools assume one general inspection template can cover the job.</span></td>
                                <td><span>Positioned for multi-brand warehouse teams with rule drift and dispute pressure.</span></td>
                            </tr>
                            <tr>
                                <td><strong>Deployment model</strong></td>
                                <td><span>Usually hosted SaaS with limited customer-side control.</span></td>
                                <td><span>Customer-owned Docker deployment is part of the product story.</span></td>
                            </tr>
                            <tr>
                                <td><strong>WMS replacement required</strong></td>
                                <td><span>Not applicable, because they are rarely part of the return decision lane.</span></td>
                                <td><span>No. Dossentry works alongside your current WMS and returns stack.</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="section" id="differences">
            <div class="shell">
                <div class="section-grid">
                    <article class="section-card">
                        <span class="section-kicker">Difference 1</span>
                        <div class="section-copy">
                            <h3>A checklist is not the same as a defensible return record.</h3>
                            <p>
                                The hard part is not just collecting an inspection. The hard part is answering what rule applied,
                                whether the evidence was complete, who inspected the item, what action was recommended, and what
                                link ops or the brand should open next.
                            </p>
                            <div class="bullet-list">
                                <div class="bullet-item">
                                    <strong>What brand rule applied?</strong>
                                    <span>Disputed returns need rule context, not only checkboxes and photos.</span>
                                </div>
                                <div class="bullet-item">
                                    <strong>Was the evidence complete?</strong>
                                    <span>Decision pressure usually starts after incomplete or scattered evidence is discovered too late.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="section-card">
                        <span class="section-kicker">Difference 2</span>
                        <div class="section-copy">
                            <h3>Built for multi-brand return disputes, not generic audit workflows.</h3>
                            <p>
                                Generic inspection software assumes one template can cover the job. Warehouse returns do not work
                                that way. One brand wants serial proof. Another wants packaging photos. Another needs specific
                                damage close-ups before review.
                            </p>
                            <div class="bullet-list">
                                <div class="bullet-item">
                                    <strong>Live playbooks</strong>
                                    <span>Dossentry turns client rules into live inspection playbooks instead of leaving them in SOP PDFs and chat threads.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="section-card">
                        <span class="section-kicker">Difference 3</span>
                        <div class="section-copy">
                            <h3>Phones capture the evidence mounted stations miss.</h3>
                            <p>
                                Mounted cameras capture the station. Dossentry is built for the close-ups brands actually ask for:
                                serial labels, packaging damage, inside-the-box proof, and side-angle condition photos.
                            </p>
                            <div class="bullet-list">
                                <div class="bullet-item">
                                    <strong>No station rebuild</strong>
                                    <span>Start on the devices your team already uses instead of planning a camera install project.</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="section-card">
                        <span class="section-kicker">Difference 4</span>
                        <div class="section-copy">
                            <h3>One Brand Review Link instead of Slack threads and photo folders.</h3>
                            <p>
                                The most important output is not the inspection form. It is the review record you can send when
                                someone questions the handling decision.
                            </p>
                            <div class="bullet-list">
                                <div class="bullet-item">
                                    <strong>What the link includes</strong>
                                    <span>Photos, timeline, rule snapshot, recommendation, and evidence status in one protected record.</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="faq">
            <div class="shell">
                <div class="faq-grid">
                    <div class="faq-intro">
                        <span class="section-kicker">FAQ</span>
                        <h3>What buyers usually ask next.</h3>
                        <p>
                            This page is not claiming generic inspection apps are useless. The point is narrower: disputed
                            return cases need a different output.
                        </p>
                    </div>
                    <section class="faq-panel">
                        <details class="faq-item">
                            <summary>Why not just use SafetyCulture or another checklist app?</summary>
                            <div class="faq-answer">Because the hard part is not collecting an inspection. The hard part is defending a disputed return with the right evidence, the right brand rule, and a clean external review record.</div>
                        </details>
                        <details class="faq-item">
                            <summary>Do we need to replace our WMS?</summary>
                            <div class="faq-answer">No. Dossentry is designed to work alongside your existing warehouse systems. Your WMS remains the system of record while Dossentry handles the messy review layer.</div>
                        </details>
                        <details class="faq-item">
                            <summary>Is this for all returns?</summary>
                            <div class="faq-answer">No. The strongest fit is high-risk or disputed return cases where evidence quality, rule drift, and review clarity matter more than general throughput.</div>
                        </details>
                        <details class="faq-item">
                            <summary>Why emphasize self-hosted deployment here?</summary>
                            <div class="faq-answer">Because many warehouse and 3PL teams do not want another hosted tool holding operational case data. Customer-owned Docker deployment is part of the trust story, not just an infrastructure detail.</div>
                        </details>
                    </section>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="shell">
                <div class="cta-panel">
                    <div class="cta-grid">
                        <div class="cta-copy">
                            <span class="section-kicker">Next step</span>
                            <h2>Still using generic inspection tools for disputed returns?</h2>
                            <p>
                                See what a brand-ready warehouse return record actually looks like, then compare it to how your
                                team handles the same case today.
                            </p>
                        <div class="cta-actions">
                            <a class="button button-primary" href="{{ $sampleCaseUrl }}" data-track-page="compare" data-track-placement="cta" data-track-cta="sample_case">View Sample Case</a>
                            <a class="button button-secondary" href="{{ $demoLoginUrl }}" data-track-page="compare" data-track-placement="cta" data-track-cta="guest_demo">Enter Guest Demo</a>
                            <a class="button button-quiet" href="{{ route('landing') }}#review-request" data-track-page="compare" data-track-placement="cta" data-track-cta="workflow_review">Request Workflow Review</a>
                        </div>
                        </div>
                        <div class="cta-notes">
                            <article class="cta-note">
                                <strong>Start with one visible proof artifact</strong>
                                <p>The fastest way to understand Dossentry is to open the public sample case and see the exact record your team could send to a client brand.</p>
                            </article>
                            <article class="cta-note">
                                <strong>No replacement project required</strong>
                                <p>Dossentry fits next to your current WMS and returns stack. The point is not to replace everything. The point is to stop rebuilding messy cases manually.</p>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <div>{{ $appName }}. Brand-ready return evidence and decision workflows.</div>
            <div class="footer-links">
                <a href="{{ route('landing') }}" data-track-page="compare" data-track-placement="footer" data-track-cta="back_to_site">Home</a>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}">Terms of Service</a>
                <a href="{{ $demoLoginUrl }}" data-track-page="compare" data-track-placement="footer" data-track-cta="guest_demo">Live demo</a>
            </div>
        </div>
    </footer>
    @include('partials.marketing-click-tracking', ['pageKey' => 'compare'])
</body>
</html>
