<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} | Brand-Ready Return Evidence</title>
    <meta name="description" content="Dossentry helps multi-brand 3PLs and operators turn disputed return cases into brand-ready evidence, review links, and decision-ready case records.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f7fb;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --ink: #111827;
            --muted: #667085;
            --line: rgba(17, 24, 39, 0.1);
            --accent: #155eef;
            --accent-deep: #0040c9;
            --accent-soft: #e8f0ff;
            --warm: #b93815;
            --warm-soft: #f8e6df;
            --shadow: 0 18px 48px rgba(15, 23, 42, 0.08);
            --shadow-soft: 0 10px 28px rgba(15, 23, 42, 0.06);
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --max: 1200px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--ink);
            position: relative;
            isolation: isolate;
            overflow-x: clip;
            background: linear-gradient(180deg, #fbfcff 0%, var(--bg) 44%, #f1f4f9 100%);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            pointer-events: none;
            background-image:
                linear-gradient(90deg, rgba(251, 252, 255, 0.985) 0%, rgba(251, 252, 255, 0.97) 30%, rgba(251, 252, 255, 0.88) 48%, rgba(251, 252, 255, 0.62) 68%, rgba(251, 252, 255, 0.18) 100%),
                linear-gradient(180deg, rgba(251, 252, 255, 0.08) 0%, rgba(245, 247, 251, 0.34) 54%, rgba(241, 244, 249, 0.88) 100%),
                url('{{ asset('assets/dossentry/hero-bg.png') }}');
            background-repeat: no-repeat;
            background-size: auto, auto, min(960px, 78vw) auto;
            background-position: center, center, calc(100% + 48px) 86px;
            filter: saturate(0.9) contrast(1.01);
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background:
                radial-gradient(circle at top left, rgba(21, 94, 239, 0.09), transparent 30%),
                radial-gradient(circle at top right, rgba(185, 56, 21, 0.08), transparent 24%);
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
            padding: 18px 0 10px;
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            padding: 14px 18px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow-soft);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #edf3ff 0%, #dfeaff 100%);
            color: var(--accent-deep);
            font-family: "Fraunces", serif;
            font-size: 18px;
            border: 1px solid rgba(21, 94, 239, 0.14);
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex: 1;
        }

        .topnav {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }

        .login-link {
            color: var(--ink);
            font-size: 14px;
            font-weight: 700;
        }

        .hero {
            padding: 36px 0 46px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(390px, 0.96fr);
            gap: 44px;
            align-items: start;
        }

        .hero-copy,
        .hero-panel,
        .section-card,
        .signal-card,
        .workflow-card,
        .audience-card,
        .faq-item,
        .cta-card {
            border: 1px solid var(--line);
            background: var(--surface);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .hero-copy {
            border: 0;
            box-shadow: none;
            background: transparent;
            padding: 26px 0 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3 {
            margin: 0;
            letter-spacing: -0.035em;
        }

        h1 {
            margin-top: 18px;
            font-family: "Fraunces", serif;
            font-size: clamp(46px, 7vw, 72px);
            line-height: 0.94;
            max-width: 9.5ch;
        }

        .hero-copy p {
            max-width: 35rem;
            margin: 18px 0 0;
            font-size: 18px;
            line-height: 1.65;
            color: var(--muted);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 50px;
            padding: 0 18px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 14px;
            transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease, border-color 180ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #f8fbff;
            box-shadow: 0 12px 24px rgba(21, 94, 239, 0.22);
        }

        .button-secondary {
            background: rgba(255, 255, 255, 0.94);
            color: var(--ink);
            border: 1px solid rgba(24, 37, 44, 0.14);
        }

        .micro-proof {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 30px;
            align-items: stretch;
        }

        .micro-proof-card {
            min-height: 0;
            padding: 18px 18px 20px;
            border-radius: 18px;
            border: 1px solid rgba(24, 37, 44, 0.08);
            display: grid;
            align-content: start;
            gap: 10px;
            box-shadow: var(--shadow-soft);
        }

        .micro-proof-card--accent {
            background: linear-gradient(180deg, rgba(220, 238, 233, 0.96) 0%, rgba(239, 248, 245, 0.9) 100%);
        }

        .micro-proof-card--warm {
            background: linear-gradient(180deg, rgba(245, 223, 210, 0.95) 0%, rgba(255, 247, 242, 0.9) 100%);
        }

        .micro-proof-card--ink {
            background: linear-gradient(180deg, rgba(24, 37, 44, 0.95) 0%, rgba(37, 53, 61, 0.92) 100%);
            border-color: rgba(24, 37, 44, 0.2);
        }

        .micro-proof-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.7);
            color: rgba(24, 37, 44, 0.72);
        }

        .micro-proof-card--ink .micro-proof-kicker {
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.82);
        }

        .micro-proof strong {
            display: block;
            color: var(--ink);
            font-family: "Fraunces", serif;
            font-size: clamp(20px, 2vw, 26px);
            line-height: 1.04;
            letter-spacing: -0.035em;
            max-width: 10ch;
            text-wrap: balance;
        }

        .micro-proof p {
            margin: 0;
            font-size: 13px;
            line-height: 1.65;
            color: var(--muted);
        }

        .micro-proof-card--ink strong,
        .micro-proof-card--ink p {
            color: rgba(255, 250, 241, 0.96);
        }

        .hero-panel {
            border-radius: var(--radius-xl);
            padding: 20px;
            display: grid;
            gap: 16px;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 20px 52px rgba(17, 24, 39, 0.1);
        }

        .panel-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--warm-soft);
            color: #8b471f;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .mock-window {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 24px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            padding: 18px;
            box-shadow: 0 14px 34px rgba(17, 24, 39, 0.08);
        }

        .mock-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .mock-topline {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #d1c4b4;
        }

        .mock-title {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }

        .mock-title strong {
            font-size: 18px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 10px;
            background: var(--accent-soft);
            color: var(--accent-deep);
            font-size: 12px;
            font-weight: 800;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: 108px 1fr;
            gap: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(24, 37, 44, 0.08);
            color: var(--muted);
            font-size: 14px;
        }

        .timeline-item strong {
            color: var(--ink);
            display: block;
            margin-bottom: 3px;
        }

        .hero-sidecard {
            padding: 18px;
            border-radius: 18px;
            background: rgba(248, 251, 255, 0.95);
            border: 1px solid rgba(21, 94, 239, 0.1);
            display: grid;
            gap: 14px;
        }

        .hero-sidecard strong {
            font-size: 16px;
            line-height: 1.3;
        }

        .hero-sidecard p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .credential-list {
            display: grid;
            gap: 10px;
        }

        .credential-row {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 10px;
            align-items: center;
            font-size: 14px;
        }

        .credential-row span {
            color: var(--muted);
            font-weight: 700;
        }

        .credential-row code {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(24, 37, 44, 0.06);
            border: 1px solid rgba(24, 37, 44, 0.08);
            color: var(--ink);
            font-size: 13px;
            word-break: break-word;
        }

        .stack-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .section {
            padding: 34px 0;
        }

        .section-heading {
            display: grid;
            gap: 12px;
            margin-bottom: 22px;
        }

        .section-heading p {
            margin: 0;
            max-width: 68ch;
            color: var(--muted);
            line-height: 1.68;
        }

        h2 {
            font-family: "Fraunces", serif;
            font-size: clamp(28px, 4.8vw, 46px);
            line-height: 1.02;
        }

        .signals-grid,
        .workflow-grid,
        .audience-grid,
        .faq-grid {
            display: grid;
            gap: 18px;
        }

        .signals-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .signal-card,
        .workflow-card,
        .audience-card,
        .faq-item,
        .cta-card {
            border-radius: var(--radius-lg);
            padding: 22px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: var(--shadow-soft);
        }

        .signal-card h3,
        .workflow-card h3,
        .audience-card h3,
        .faq-item h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .signal-card p,
        .workflow-card p,
        .audience-card p,
        .faq-item p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .signal-card .kicker,
        .workflow-card .kicker,
        .audience-card .kicker {
            display: inline-block;
            margin-bottom: 12px;
            color: var(--warm);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .workflow-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .workflow-card {
            position: relative;
            overflow: hidden;
            min-height: 184px;
        }

        .workflow-index {
            font-family: "Fraunces", serif;
            font-size: 46px;
            color: rgba(17, 24, 39, 0.12);
            margin-bottom: 12px;
        }

        .audience-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .proof-banner {
            margin-top: 18px;
            border-radius: var(--radius-xl);
            padding: 22px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: rgba(255, 255, 255, 0.84);
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            box-shadow: var(--shadow-soft);
        }

        .proof-banner strong {
            display: block;
            margin-bottom: 6px;
            font-size: 18px;
        }

        .proof-banner p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .deployment-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .deployment-pill {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid rgba(24, 37, 44, 0.08);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.01em;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.26);
        }

        .deployment-pill--accent {
            background: rgba(220, 238, 233, 0.95);
            color: var(--accent-deep);
        }

        .deployment-pill--warm {
            background: rgba(245, 223, 210, 0.95);
            color: #8b471f;
        }

        .deployment-pill--ink {
            background: rgba(24, 37, 44, 0.94);
            color: rgba(255, 250, 241, 0.96);
            border-color: rgba(24, 37, 44, 0.2);
        }

        .signal-card--ownership {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(246, 248, 252, 0.92) 100%);
        }

        .signal-card--ownership h3 {
            font-family: "Fraunces", serif;
            font-size: 24px;
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .faq-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cta {
            padding: 34px 0 78px;
        }

        .cta-card {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(280px, 360px);
            gap: 24px;
            align-items: start;
            padding: 28px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(237, 243, 255, 0.94));
        }

        .cta-card p {
            margin: 10px 0 0;
            color: var(--muted);
            max-width: 60ch;
            line-height: 1.7;
        }

        .review-form-wrap {
            display: grid;
            gap: 18px;
            min-width: 0;
        }

        .review-lead {
            display: grid;
            gap: 10px;
            max-width: 58ch;
        }

        .review-lead p {
            margin: 0;
        }

        .review-form {
            display: grid;
            gap: 14px;
            padding: 22px;
            border-radius: 24px;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.05);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field label {
            font-size: 13px;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: 0.01em;
            line-height: 1.4;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid rgba(24, 37, 44, 0.14);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.82);
            padding: 14px 16px;
            font: inherit;
            color: var(--ink);
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, background 180ms ease;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: rgba(15, 108, 103, 0.55);
            box-shadow: 0 0 0 4px rgba(15, 108, 103, 0.12);
            background: #fff;
        }

        .field textarea {
            min-height: 144px;
            resize: vertical;
        }

        .field--full {
            grid-column: 1 / -1;
        }

        .form-note {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.65;
        }

        .form-success,
        .form-errors {
            border-radius: 18px;
            padding: 16px 18px;
            border: 1px solid rgba(24, 37, 44, 0.08);
        }

        .form-success {
            background: rgba(220, 238, 233, 0.92);
            color: var(--accent-deep);
        }

        .form-errors {
            background: rgba(245, 223, 210, 0.92);
            color: #7f3a18;
        }

        .form-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .cta-meta {
            display: grid;
            gap: 14px;
            align-content: start;
            min-width: 0;
        }

        .cta-note {
            padding: 18px;
            border-radius: 20px;
            background: rgba(24, 37, 44, 0.05);
            border: 1px solid rgba(24, 37, 44, 0.08);
        }

        .cta-note strong {
            display: block;
            margin-bottom: 6px;
            font-size: 15px;
            line-height: 1.35;
        }

        .cta-note p {
            margin: 0;
            font-size: 14px;
            line-height: 1.65;
            max-width: none;
        }

        .cta-note .button {
            margin-top: 14px;
            min-height: 48px;
            padding-inline: 18px;
            font-size: 14px;
        }

        .footer {
            padding: 0 0 34px;
            color: var(--muted);
            font-size: 14px;
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding-top: 18px;
            border-top: 1px solid rgba(24, 37, 44, 0.12);
        }

        .fade-up {
            animation: fadeUp 640ms ease both;
        }

        .fade-up.delay-1 {
            animation-delay: 120ms;
        }

        .fade-up.delay-2 {
            animation-delay: 220ms;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1080px) {
            body::before {
                background-size: auto, auto, 760px auto;
                background-position: center, center, calc(100% + 150px) 118px;
            }

            .hero-grid,
            .signals-grid,
            .workflow-grid,
            .proof-banner,
            .faq-grid,
            .audience-grid,
            .cta-card,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .cta-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .micro-proof {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .topbar-inner,
            .topbar-nav {
                flex-wrap: wrap;
            }

            .topbar-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 720px) {
            body::before {
                background-size: auto, auto, 560px auto;
                background-position: center, center, calc(100% + 210px) 114px;
            }

            .shell {
                width: min(var(--max), calc(100% - 22px));
            }

            .topbar-inner {
                border-radius: 20px;
                align-items: flex-start;
            }

            .topbar-nav,
            .topbar-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .hero-copy,
            .hero-panel,
            .signal-card,
            .workflow-card,
            .audience-card,
            .faq-item,
            .cta-card {
                padding: 20px;
            }

            .micro-proof {
                grid-template-columns: 1fr;
            }

            .topnav {
                flex-direction: column;
                gap: 14px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }

            .timeline-item {
                grid-template-columns: 1fr;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .review-form {
                padding: 18px;
            }

            .cta-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell">
            <div class="topbar-inner fade-up">
                <div class="brand">
                    <span class="brand-mark">D</span>
                    <span>{{ $appName }}</span>
                </div>
                <div class="topbar-nav">
                    <nav class="topnav">
                        <a href="#workflow">Workflow</a>
                        <a href="#problem">Evidence</a>
                        <a href="#deployment">Deployment</a>
                        <a href="#faq">FAQ</a>
                    </nav>
                    <div class="topbar-actions">
                        <a class="login-link" href="{{ $demoLoginUrl }}">Log in</a>
                        <a class="button button-primary" href="{{ $demoLoginUrl }}">Enter Guest Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell hero-grid">
                <article class="hero-copy fade-up delay-1">
                    <span class="eyebrow">For Multi-Brand 3PLs And Operators</span>
                    <h1>Capture the return details fixed stations miss.</h1>
                    <p>
                        {{ $appName }} helps warehouse teams capture the close-ups, serials, packaging damage,
                        timeline, rule snapshot, and recommendation brands actually ask for. No station cameras.
                        No warehouse rebuild. One clean review record instead of Slack threads, folders, and guesswork.
                    </p>
                    <div class="hero-actions">
                        @if($sampleBrandReviewUrl)
                            <a class="button button-primary" href="{{ $sampleBrandReviewUrl }}">View Sample Brand Review Link</a>
                        @else
                            <a class="button button-primary" href="#review-request">Request Workflow Review</a>
                        @endif
                        <a class="button button-secondary" href="{{ $demoLoginUrl }}">Enter Guest Demo</a>
                    </div>
                    <div class="micro-proof">
                        <article class="micro-proof-card micro-proof-card--accent">
                            <span class="micro-proof-kicker">Deployment</span>
                            <strong>Docker on your infrastructure</strong>
                            <p>Formal customer use installs as a self-hosted package in the customer’s own environment, not a shared vendor workspace.</p>
                        </article>
                        <article class="micro-proof-card micro-proof-card--warm">
                            <span class="micro-proof-kicker">Data ownership</span>
                            <strong>Database and evidence stay with the customer</strong>
                            <p>Uploaded photos, case records, and staff accounts live in the customer’s own stack instead of being locked inside our hosted demo.</p>
                        </article>
                        <article class="micro-proof-card micro-proof-card--ink">
                            <span class="micro-proof-kicker">Optional Pro add-on</span>
                            <strong>Local knowledge workspace with customer-owned AI keys</strong>
                            <p>For teams that want SOP lookup and internal Q&amp;A, we can install a local knowledge workspace and connect the customer’s own AI API keys.</p>
                        </article>
                    </div>
                </article>

                <aside class="hero-panel fade-up delay-2">
                    <span class="panel-label">Sample Brand Review Record</span>
                    <div class="mock-window">
                        <div class="mock-topline">
                            <span class="dot"></span>
                            <span class="dot"></span>
                            <span class="dot"></span>
                        </div>
                        <div class="mock-title">
                            <div>
                                <strong>RMA-1003</strong>
                                <div style="color: var(--muted); font-size: 14px; margin-top: 4px;">Electronics / serial required / damage claim</div>
                            </div>
                            <span class="pill">Ready for brand review</span>
                        </div>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div>09:11 AM</div>
                                <div>
                                    <strong>Inspection captured</strong>
                                    Close-up damage, serial, packaging interior, and label photos captured from the inspector phone.
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>09:19 AM</div>
                                <div>
                                    <strong>Playbook applied</strong>
                                    Client-specific rule set required serial, damage close-up, and recommendation note.
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>09:27 AM</div>
                                <div>
                                    <strong>Recommendation prepared</strong>
                                    Evidence complete. Suggested action: hold pending brand review.
                                </div>
                            </div>
                        </div>
                        <div class="mock-actions">
                            @if($sampleBrandReviewUrl)
                                <a class="button button-secondary" href="{{ $sampleBrandReviewUrl }}">Open sample review</a>
                            @endif
                        </div>
                    </div>
                    <div class="hero-sidecard">
                        <strong>{{ $guestDemo['workspace_label'] ?? 'Shared guest workspace' }}</strong>
                        <p>
                            Use the live workspace exactly the way a warehouse or ops lead would. This is sample data only, and the workspace resets regularly.
                        </p>
                        <div class="credential-list">
                            <div class="credential-row">
                                <span>Email</span>
                                <code>{{ $guestDemo['email'] ?? 'guest@dossentry.com' }}</code>
                            </div>
                            <div class="credential-row">
                                <span>Password</span>
                                <code>{{ $guestDemo['password'] ?? '12345678' }}</code>
                            </div>
                        </div>
                        <p>{{ $guestDemo['disclaimer'] ?? 'Sample data only. Resets regularly.' }}</p>
                        <div class="stack-actions">
                            <a class="button button-secondary" href="{{ $demoLoginUrl }}">Go to guest demo</a>
                            <a class="button button-secondary" href="#review-request">Request a pilot workspace</a>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section" id="problem">
            <div class="shell">
                <div class="section-heading fade-up">
                    <h2>Your WMS records the return. It does not defend the decision.</h2>
                    <p>
                        Most warehouse systems can receive an RMA, update stock, and move inventory. They do not
                        give your team a clean, shareable case record when a brand questions how an item was graded,
                        why a recommendation was made, or whether the evidence was complete.
                    </p>
                </div>

                <div class="signals-grid">
                    <article class="signal-card fade-up">
                        <span class="kicker">What breaks today</span>
                        <h3>The proof is scattered</h3>
                        <p>Photos live in phones, chat threads, shared folders, and one-off emails. By the time a brand asks, the case has to be rebuilt from scratch.</p>
                    </article>
                    <article class="signal-card fade-up delay-1">
                        <span class="kicker">What creates friction</span>
                        <h3>Each client wants something different</h3>
                        <p>One brand wants serial and box shots. Another wants a damage close-up and note. Your team should not memorize that from SOP PDFs.</p>
                    </article>
                    <article class="signal-card fade-up delay-2">
                        <span class="kicker">What Dossentry changes</span>
                        <h3>One review-ready case link</h3>
                        <p>Inspection, rule coverage, evidence completeness, recommendation, and timeline all stay attached to one case record that can be reviewed later.</p>
                    </article>
                </div>

                <div class="proof-banner fade-up">
                    <div>
                        <strong>Designed for disputed cases</strong>
                        <p>Use it for the returns that create back-and-forth, not just normal inbound flow.</p>
                    </div>
                    <div>
                        <strong>Multi-brand by default</strong>
                        <p>Each client gets its own playbook, evidence requirements, and decision logic.</p>
                    </div>
                    <div>
                        <strong>No station rebuild</strong>
                        <p>No mounted camera install. No scanner rollout. Start on the phones your team already uses.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="workflow">
            <div class="shell">
                <div class="section-heading fade-up">
                    <h2>How the workflow actually moves</h2>
                    <p>
                        Dossentry is not a shopper portal and not a replacement WMS. It sits on the cases that need
                        evidence, recommendation, and a defensible review trail.
                    </p>
                </div>

                <div class="workflow-grid">
                    <article class="workflow-card fade-up">
                        <div class="workflow-index">01</div>
                        <h3>Apply the client playbook</h3>
                        <p>Set condition options, required photos, notes, serial rules, and recommended action by client.</p>
                    </article>
                    <article class="workflow-card fade-up delay-1">
                        <div class="workflow-index">02</div>
                        <h3>Capture evidence from any angle</h3>
                        <p>Warehouse staff use a phone to capture close-ups, serials, packaging damage, SKU, and notes in one fast inspection flow.</p>
                    </article>
                    <article class="workflow-card fade-up delay-2">
                        <div class="workflow-index">03</div>
                        <h3>Review evidence and recommendation</h3>
                        <p>Ops can confirm whether the case is complete, track the timeline, and prepare the recommendation.</p>
                    </article>
                    <article class="workflow-card fade-up">
                        <div class="workflow-index">04</div>
                        <h3>Share one clean review record</h3>
                        <p>The Brand Review Link presents what happened, when it happened, and what the team recommends without stitching documents together.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="fit">
            <div class="shell">
                <div class="section-heading fade-up">
                    <h2>Who this is actually for</h2>
                    <p>
                        The best fit is not “everyone who processes returns.” The best fit is the team that has already
                        been challenged on how a return was handled and needs a faster way to show its work.
                    </p>
                </div>

                <div class="audience-grid">
                    <article class="audience-card fade-up">
                        <span class="kicker">Primary fit</span>
                        <h3>Multi-brand 3PLs</h3>
                        <p>You serve several DTC brands, each with different return rules, and your ops team needs one place to standardize evidence and review-ready case records.</p>
                    </article>
                    <article class="audience-card fade-up delay-1">
                        <span class="kicker">Secondary fit</span>
                        <h3>Multi-brand operators with their own warehouse</h3>
                        <p>You control both fulfillment and refund handling, so the same team needs brand-specific rules, consistent inspections, and a defensible record for high-risk cases.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="deployment">
            <div class="shell">
                <div class="section-heading fade-up">
                    <h2>Production runs on the customer side, not in a shared vendor workspace</h2>
                    <p>
                        The live demo is hosted so you can evaluate the workflow quickly. Formal production use is delivered as a
                        self-hosted Docker package, so the customer controls the database, uploaded evidence, and staff accounts.
                    </p>
                </div>

                <div class="deployment-strip fade-up">
                    <span class="deployment-pill deployment-pill--accent">Self-hosted Docker deployment</span>
                    <span class="deployment-pill deployment-pill--warm">Customer-owned database and uploaded evidence</span>
                    <span class="deployment-pill deployment-pill--ink">Optional Pro local knowledge workspace with customer-owned AI keys</span>
                </div>

                <div class="signals-grid">
                    <article class="signal-card signal-card--ownership fade-up">
                        <span class="kicker">Production deployment</span>
                        <h3>Install with Docker on your own infrastructure</h3>
                        <p>We provide a self-hosted web package for formal customer use. Your team runs the app in its own environment instead of sharing data with our demo server.</p>
                    </article>
                    <article class="signal-card signal-card--ownership fade-up delay-1">
                        <span class="kicker">Data ownership</span>
                        <h3>Database, uploaded evidence, and staff accounts stay on the customer side</h3>
                        <p>The live demo is only for evaluation. Formal customer use keeps case data and uploaded evidence inside the customer’s own environment, not in a shared Dossentry workspace.</p>
                    </article>
                    <article class="signal-card signal-card--ownership fade-up delay-2">
                        <span class="kicker">Optional Pro add-on</span>
                        <h3>Managed local knowledge workspace with your own AI keys</h3>
                        <p>For teams that want faster SOP lookup and internal Q&amp;A, we can install a local knowledge workspace on the customer side and connect the customer’s own OpenAI-compatible API keys.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="faq">
            <div class="shell">
                <div class="section-heading fade-up">
                    <h2>Short answers to the objections you will hear first</h2>
                </div>
                <div class="faq-grid">
                    <article class="faq-item fade-up">
                        <h3>Is this a replacement for our WMS?</h3>
                        <p>No. Your WMS remains the system of record. Dossentry is for the cases where stock movement is not enough and your team needs evidence plus a decision-ready record.</p>
                    </article>
                    <article class="faq-item fade-up delay-1">
                        <h3>Is this a shopper-facing returns portal?</h3>
                        <p>No. This is for warehouse-side inspection, review, and brand-facing case documentation after the return has reached the operation.</p>
                    </article>
                    <article class="faq-item fade-up">
                        <h3>Do we need hardware?</h3>
                        <p>No. The workflow runs in a browser on standard iPhone and Android devices, including shared warehouse phones. You do not need station cameras, a scanner rollout, or a warehouse rebuild to start.</p>
                    </article>
                    <article class="faq-item fade-up delay-1">
                        <h3>What makes it different?</h3>
                        <p>Brand-specific playbooks, evidence completeness, close-up mobile capture, one clean Brand Review Link, and a defensible record that survives after the warehouse floor has moved on.</p>
                    </article>
                    <article class="faq-item fade-up">
                        <h3>Is production data shared with Dossentry?</h3>
                        <p>No. Formal customer deployments can run as a self-hosted Docker package, so the database and uploaded evidence stay in the customer’s own environment.</p>
                    </article>
                    <article class="faq-item fade-up delay-1">
                        <h3>Can we add our own local knowledge base later?</h3>
                        <p>Yes, as a Pro add-on. The most practical version is a customer-owned local knowledge workspace with the customer’s own OpenAI-compatible API keys, not a shared vendor AI service.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="cta" id="review-request">
            <div class="shell">
                <div class="cta-card fade-up">
                    <div class="review-form-wrap">
                        <div class="review-lead">
                            <h2>Start with the live demo, then review one real workflow.</h2>
                            <p>
                                The fastest next step is a short workflow review. Tell us how your team currently handles
                                disputed returns, and we will use that to pressure-test whether Dossentry fits your operation.
                            </p>
                        </div>

                        @if(session('reviewRequestSubmitted'))
                            <div class="form-success">
                                <strong>Request received.</strong>
                                We now have your workflow review request in the workspace. The next step is to open the live demo and compare it to your current process.
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

                        <form class="review-form" method="post" action="{{ route('workflow-review-requests.store') }}">
                            @csrf
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
                                    <input id="role_title" type="text" name="role_title" value="{{ old('role_title') }}" placeholder="Ops Manager">
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
                                    <label for="workflow_note">What happens today when a brand questions how your warehouse handled a return?</label>
                                    <textarea id="workflow_note" name="workflow_note" placeholder="Example: our inspector uploads photos to Slack, ops checks the SOP PDF, and then we send screenshots back to the brand.">{{ old('workflow_note') }}</textarea>
                                </div>
                                <div class="field" style="display:none" aria-hidden="true">
                                    <label for="website">Website</label>
                                    <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
                                </div>
                            </div>
                            <div class="hero-actions">
                                <button class="button button-primary" type="submit">Request Workflow Review</button>
                                <a class="button button-secondary" href="{{ $demoLoginUrl }}">Enter Guest Demo</a>
                            </div>
                            <p class="form-note">No heavy rollout. No shopper portal project. Start with one real workflow and one brand challenge scenario.</p>
                        </form>
                    </div>
                    <div class="cta-meta">
                        <div class="cta-note">
                            <strong>Start with a real example</strong>
                            <p>Open a sample Brand Review Link first. It is the fastest way to see the exact record your team could send when a brand challenges a return decision.</p>
                            @if($sampleBrandReviewUrl)
                                <a class="button button-secondary" href="{{ $sampleBrandReviewUrl }}">View sample review record</a>
                            @endif
                        </div>
                        <div class="cta-note">
                            <strong>Shared guest demo</strong>
                            <p>Try the product before you talk to us. Use the shared workspace below, then request a pilot workspace if you want your own setup.</p>
                            <div class="credential-list">
                                <div class="credential-row">
                                    <span>Email</span>
                                    <code>{{ $guestDemo['email'] ?? 'guest@dossentry.com' }}</code>
                                </div>
                                <div class="credential-row">
                                    <span>Password</span>
                                    <code>{{ $guestDemo['password'] ?? '12345678' }}</code>
                                </div>
                            </div>
                            <a class="button button-secondary" href="{{ $demoLoginUrl }}">Open guest demo</a>
                        </div>
                        <div class="cta-note">
                            <strong>What you get back</strong>
                            <p>A short review of your current evidence flow, where cases break down, and whether Dossentry fits the way your team actually works.</p>
                        </div>
                        <div class="cta-note">
                            <strong>Best fit</strong>
                            <p>Teams that have already been challenged by a brand and need a cleaner review record than Slack, spreadsheets, or photo folders.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="shell footer-inner">
            <div>{{ $appName }}. Brand-ready return evidence and decision workflows.</div>
            <a href="{{ $demoLoginUrl }}">Live demo</a>
        </div>
    </footer>
</body>
</html>
