<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.marketing-meta', [
        'metaTitle' => 'Dossentry | Free Return Exception Audit for 3PLs',
        'metaDescription' => 'Send 3 anonymized return cases or one return SOP. Dossentry will flag refund leakage, SKU mismatch, missing-item risk, and brand-rule gaps before a pilot.',
        'metaImage' => asset('assets/dossentry/og-home.png'),
        'metaImageAlt' => 'Dossentry return exception audit for 3PL warehouses.',
    ])
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/dossentry/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --surface-blue: #eff6ff;
            --surface-amber: #fff7ed;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #dbe4ef;
            --accent: #2563eb;
            --accent-deep: #1d4ed8;
            --danger: #b45309;
            --shadow-soft: 0 24px 64px rgba(15, 23, 42, 0.08);
            --shadow-card: 0 10px 28px rgba(15, 23, 42, 0.06);
            --max: 1180px;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.14) 0%, rgba(248, 250, 252, 0.94) 34%, transparent 60%),
                linear-gradient(180deg, #ffffff 0%, var(--bg) 100%);
        }

        a { color: inherit; text-decoration: none; }

        .shell {
            width: min(var(--max), calc(100% - 40px));
            margin: 0 auto;
        }

        .topbar {
            padding: 22px 0 12px;
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
            font-weight: 900;
            letter-spacing: -0.04em;
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
            font-weight: 700;
        }

        .topnav a:hover,
        .footer-links a:hover {
            color: var(--accent);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 14px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 900;
            transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease, border-color 160ms ease;
        }

        .button:hover { transform: translateY(-1px); }

        .button-primary {
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(37, 99, 235, 0.24);
        }

        .button-secondary {
            background: #ffffff;
            color: var(--ink);
            border-color: var(--line);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .hero {
            padding: 34px 0 28px;
        }

        .hero-panel,
        .card,
        .form-panel,
        .quote-panel {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(219, 228, 239, 0.96);
            border-radius: 30px;
            box-shadow: var(--shadow-soft);
        }

        .hero-panel {
            padding: 42px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.02fr) minmax(340px, 0.88fr);
            gap: 34px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--surface-blue);
            color: var(--accent);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1, h2, h3, p { margin: 0; }

        h1 {
            margin-top: 18px;
            max-width: 13ch;
            font-size: clamp(42px, 5vw, 66px);
            line-height: 1.02;
            letter-spacing: -0.055em;
            font-weight: 900;
        }

        h2 {
            font-size: clamp(28px, 3vw, 40px);
            line-height: 1.08;
            letter-spacing: -0.04em;
            font-weight: 900;
        }

        h3 {
            font-size: 19px;
            line-height: 1.25;
            letter-spacing: -0.025em;
            font-weight: 900;
        }

        .hero-copy {
            margin-top: 18px;
            max-width: 42rem;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.75;
        }

        .hero-actions,
        .form-actions,
        .inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 26px;
        }

        .audit-box {
            display: grid;
            gap: 14px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.92) 0%, rgba(239, 246, 255, 0.88) 100%);
            box-shadow: var(--shadow-card);
        }

        .audit-box strong {
            font-size: 15px;
            font-weight: 900;
            color: var(--ink);
        }

        .audit-box ul {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .audit-box li {
            position: relative;
            padding-left: 22px;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
        }

        .audit-box li::before {
            content: "";
            position: absolute;
            top: 9px;
            left: 0;
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--accent);
        }

        .main {
            padding: 0 0 42px;
        }

        .grid-3,
        .grid-2 {
            display: grid;
            gap: 18px;
        }

        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .section {
            margin-top: 22px;
        }

        .card {
            padding: 26px;
            box-shadow: var(--shadow-card);
        }

        .card p,
        .section-intro,
        .form-panel p,
        .quote-panel p {
            margin-top: 10px;
            color: var(--muted);
            line-height: 1.72;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .section-head h2 {
            max-width: 17ch;
        }

        .section-intro {
            max-width: 42rem;
        }

        .step-number {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--surface-blue);
            color: var(--accent);
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 16px;
        }

        .quote-panel {
            padding: 30px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
        }

        .quote-panel p {
            color: #cbd5e1;
        }

        .quote-copy {
            margin-top: 16px;
            padding: 18px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.06);
            color: #e2e8f0;
            line-height: 1.72;
            font-size: 15px;
        }

        .form-panel {
            padding: 34px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field--full {
            grid-column: 1 / -1;
        }

        label {
            color: #334155;
            font-size: 13px;
            font-weight: 900;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #ffffff;
            color: var(--ink);
            font: inherit;
            font-size: 15px;
            outline: none;
            padding: 14px 15px;
        }

        textarea {
            min-height: 142px;
            resize: vertical;
            line-height: 1.6;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(37, 99, 235, 0.62);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
        }

        .form-success,
        .form-errors {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-success {
            border: 1px solid #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .form-errors {
            border: 1px solid #fed7aa;
            background: var(--surface-amber);
            color: var(--danger);
        }

        .form-note {
            margin-top: 14px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .footer {
            padding: 0 0 34px;
            color: var(--muted);
            font-size: 13px;
        }

        @media (max-width: 980px) {
            .hero-grid,
            .grid-3,
            .grid-2,
            .form-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                max-width: none;
            }
        }

        @media (max-width: 700px) {
            .shell {
                width: min(var(--max), calc(100% - 24px));
            }

            .hero-panel,
            .card,
            .form-panel,
            .quote-panel {
                padding: 22px;
                border-radius: 24px;
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
                <a href="{{ $exceptionWorkflowUrl }}">3PL Workflow</a>
                <a href="{{ $sampleCaseUrl }}">Sample Case</a>
                <a href="{{ $returnExceptionChecklistPdfUrl }}" data-track-page="return_exception_audit" data-track-placement="topnav" data-track-cta="checklist_pdf">Checklist PDF</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <section class="hero">
            <div class="shell">
                <div class="hero-panel">
                    <div class="hero-grid">
                        <div>
                            <span class="eyebrow">Free return exception audit</span>
                            <h1>Find the return cases that leak refunds.</h1>
                            <p class="hero-copy">
                                Send 3 anonymized return cases or one return SOP. We will show where refund leakage,
                                SKU mismatch, missing-item risk, and brand-rule gaps are happening before you commit
                                to a pilot.
                            </p>
                            <div class="hero-actions">
                                <a class="button button-primary" href="#audit-request" data-track-page="return_exception_audit" data-track-placement="hero" data-track-cta="audit_request">Request Free Audit</a>
                                <a class="button button-secondary" href="{{ $returnExceptionChecklistPdfUrl }}" data-track-page="return_exception_audit" data-track-placement="hero" data-track-cta="checklist_pdf">Download Checklist PDF</a>
                            </div>
                        </div>

                        <aside class="audit-box" aria-label="Audit inputs and outputs">
                            <strong>What to send</strong>
                            <ul>
                                <li>3 anonymized disputed return cases, or</li>
                                <li>1 brand return SOP, inspection checklist, or refund hold rule.</li>
                            </ul>
                            <strong>What you get back</strong>
                            <ul>
                                <li>Exception-risk notes your team can review.</li>
                                <li>Where evidence, rules, and refund release decisions break down.</li>
                                <li>A practical next step: no-fit, checklist cleanup, or Dossentry pilot.</li>
                            </ul>
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        <div class="shell">
            <section class="section">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">What we audit</span>
                        <h2>Built around exception control, not generic inspections.</h2>
                    </div>
                    <p class="section-intro">
                        The audit is meant for multi-brand 3PLs and returns teams that already inspect goods, but still
                        lose time when a refund, mismatch, or brand question needs a defensible record.
                    </p>
                </div>

                <div class="grid-3">
                    <article class="card">
                        <h3>Expected vs actual gaps</h3>
                        <p>We check whether your team can compare the expected return record against the received SKU, serial, carton label, and item condition.</p>
                    </article>
                    <article class="card">
                        <h3>Evidence completeness</h3>
                        <p>We identify which photos, notes, or timestamps are missing when the case later needs brand, ops, or refund review.</p>
                    </article>
                    <article class="card">
                        <h3>Brand-rule execution</h3>
                        <p>We look for rules that live in Slack, PDFs, memory, or tribal knowledge instead of being attached to the operator workflow.</p>
                    </article>
                    <article class="card">
                        <h3>Refund hold posture</h3>
                        <p>We flag cases that should stay on hold before refund release because the facts are incomplete, contradictory, or high risk.</p>
                    </article>
                    <article class="card">
                        <h3>Missing-item risk</h3>
                        <p>We check whether your inspection flow clearly separates missing part, wrong item, damaged item, and unclear packaging scenarios.</p>
                    </article>
                    <article class="card">
                        <h3>Escalation record</h3>
                        <p>We check whether the next reviewer can understand the case in under one minute without rebuilding context from screenshots.</p>
                    </article>
                </div>
            </section>

            <section class="section">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">How it works</span>
                        <h2>One narrow audit before a software conversation.</h2>
                    </div>
                </div>

                <div class="grid-3">
                    <article class="card">
                        <span class="step-number">1</span>
                        <h3>Send a sample</h3>
                        <p>Share 3 anonymized return cases, one brand SOP, or a blank inspection checklist. Remove customer names and private data.</p>
                    </article>
                    <article class="card">
                        <span class="step-number">2</span>
                        <h3>We map the breakpoints</h3>
                        <p>We look for rule gaps, evidence gaps, hold-decision gaps, and places where your team must rebuild context manually.</p>
                    </article>
                    <article class="card">
                        <span class="step-number">3</span>
                        <h3>You get a decision</h3>
                        <p>You get a short recommendation: keep your current workflow, clean up the checklist, or run a Dossentry pilot.</p>
                    </article>
                </div>
            </section>

            <section class="section">
                <div class="grid-2">
                    <div class="quote-panel">
                        <span class="eyebrow">Email-ready offer</span>
                        <h2>Use this in outreach.</h2>
                        <p>Paste this into a cold email or LinkedIn message when a 3PL asks what the audit means.</p>
                        <div class="quote-copy">
                            Send us 3 anonymized return cases or one return SOP. We will show where refund leakage,
                            SKU mismatch, missing-item risk, and brand-rule gaps are happening. No WMS integration
                            is required for the audit.
                        </div>
                    </div>

                    <div class="card">
                        <span class="eyebrow">Checklist PDF</span>
                        <h2>Give operators a concrete checklist first.</h2>
                        <p>
                            The PDF is a lightweight leave-behind for warehouse managers, returns leads, and 3PL
                            founders. It explains what to check before a refund moves forward.
                        </p>
                        <div class="inline-actions">
                            <a class="button button-primary" href="{{ $returnExceptionChecklistPdfUrl }}" data-track-page="return_exception_audit" data-track-placement="checklist_section" data-track-cta="checklist_pdf">Download PDF</a>
                            <a class="button button-secondary" href="{{ $sampleCaseUrl }}" data-track-page="return_exception_audit" data-track-placement="checklist_section" data-track-cta="sample_case">View Sample Case</a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section" id="audit-request">
                <div class="form-panel">
                    <span class="eyebrow">Request audit</span>
                    <h2>Start with one messy workflow.</h2>
                    <p>
                        Use this form if you want us to review a return exception process before a demo. Do not submit
                        private customer data. Anonymized case details or SOP language are enough.
                    </p>

                    @if(session('reviewRequestSubmitted'))
                        <div class="form-success">
                            <strong>Audit request received.</strong>
                            Send the anonymized cases or SOP to the same email thread if needed, and we will map the exception gaps.
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="form-errors">
                            <strong>We still need a few details.</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="post" action="{{ route('workflow-review-requests.store') }}">
                        @csrf
                        <input type="hidden" name="success_path" value="/return-exception-audit#audit-request">
                        <div class="form-grid">
                            <div class="field">
                                <label for="full_name">Full name</label>
                                <input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Jane Smith" required>
                            </div>
                            <div class="field">
                                <label for="work_email">Work email</label>
                                <input id="work_email" type="email" name="work_email" value="{{ old('work_email') }}" placeholder="jane@company.com" required>
                            </div>
                            <div class="field">
                                <label for="company_name">Company</label>
                                <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" placeholder="North Dock Logistics" required>
                            </div>
                            <div class="field">
                                <label for="role_title">Role</label>
                                <input id="role_title" type="text" name="role_title" value="{{ old('role_title') }}" placeholder="Returns Manager">
                            </div>
                            <div class="field">
                                <label for="volume_note">Monthly return volume</label>
                                <select id="volume_note" name="volume_note">
                                    <option value="">Choose one</option>
                                    @foreach(['Under 100', '100-500', '500-1,000', '1,000+'] as $volumeOption)
                                        <option value="{{ $volumeOption }}" {{ old('volume_note') === $volumeOption ? 'selected' : '' }}>{{ $volumeOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field field--full">
                                <label for="workflow_note">What should we audit?</label>
                                <textarea id="workflow_note" name="workflow_note" placeholder="Example: We need a better way to decide when a return should stay on hold because the item, photos, or brand rules do not match.">{{ old('workflow_note') }}</textarea>
                            </div>
                            <div class="field" style="display:none" aria-hidden="true">
                                <label for="website">Website</label>
                                <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Request Free Audit</button>
                            <a class="button button-secondary" href="{{ $returnExceptionChecklistPdfUrl }}" data-track-page="return_exception_audit" data-track-placement="form" data-track-cta="checklist_pdf">Download Checklist First</a>
                        </div>
                        <p class="form-note">No WMS integration is required for the audit. Start with anonymized examples.</p>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <div>{{ $appName }}. Return exception audit, brand rules, and review-ready evidence.</div>
            <nav class="footer-links">
                <a href="{{ route('landing') }}">Home</a>
                <a href="{{ $exceptionWorkflowUrl }}">3PL workflow</a>
                <a href="{{ $returnExceptionChecklistPdfUrl }}">Checklist PDF</a>
                <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}">Terms of Service</a>
            </nav>
        </div>
    </footer>

    @include('partials.marketing-click-tracking', ['pageKey' => 'return_exception_audit'])
</body>
</html>
