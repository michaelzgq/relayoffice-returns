<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $appName }} | Brand-Ready Return Evidence</title>
    <meta name="description" content="Dossentry helps multi-brand 3PLs and operators turn disputed return cases into brand-ready evidence, review links, and decision-ready case records.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --bg-soft: #f8fafc;
            --bg-muted: #f1f5f9;
            --surface: #ffffff;
            --surface-soft: #f8fbff;
            --surface-blue: #eef4ff;
            --surface-cream: #fffaf2;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --line-strong: #cbd5e1;
            --accent: #2563eb;
            --accent-deep: #1d4ed8;
            --accent-soft: #dbeafe;
            --success-soft: #dcfce7;
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --shadow-soft: 0 16px 48px rgba(15, 23, 42, 0.08);
            --shadow-card: 0 8px 24px rgba(15, 23, 42, 0.06);
            --max: 1240px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top center, rgba(219, 234, 254, 0.44) 0%, rgba(255, 255, 255, 0.94) 36%, #ffffff 68%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .shell {
            width: min(var(--max), calc(100% - 40px));
            margin: 0 auto;
        }

        .topbar {
            padding: 22px 0 12px;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 0;
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
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
        }

        .topnav {
            display: flex;
            align-items: center;
            gap: 32px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .topnav a:hover,
        .login-link:hover {
            color: var(--accent);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .login-link {
            color: #475569;
            font-size: 14px;
            font-weight: 700;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 48px;
            padding: 0 22px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 800;
            border: 1px solid transparent;
            transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease, border-color 180ms ease, color 180ms ease;
            cursor: pointer;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: linear-gradient(180deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #ffffff;
            box-shadow: 0 16px 36px rgba(37, 99, 235, 0.24);
        }

        .button-secondary {
            background: var(--surface);
            color: var(--ink);
            border-color: var(--line);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }

        .hero {
            padding: 42px 0 52px;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(226, 232, 240, 0.88);
            border-radius: 32px;
            box-shadow: var(--shadow-soft);
            padding: 46px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(390px, 0.96fr);
            gap: 40px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--surface-blue);
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
            margin-top: 22px;
            font-size: clamp(46px, 6vw, 72px);
            line-height: 1.02;
            font-weight: 900;
            max-width: 10ch;
        }

        .hero-accent {
            color: var(--accent);
        }

        .hero-copy p {
            margin: 22px 0 0;
            max-width: 34rem;
            color: var(--muted);
            font-size: 19px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 30px;
        }

        .hero-proof {
            margin-top: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-proof-pill {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid var(--line);
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .hero-visual {
            position: relative;
        }

        .product-stage {
            position: relative;
            padding: 26px;
            border-radius: 28px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 26px 56px rgba(15, 23, 42, 0.12);
        }

        .product-stage::before {
            content: "";
            position: absolute;
            inset: 22px 26px auto auto;
            width: 140px;
            height: 140px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.18) 0%, rgba(37, 99, 235, 0) 72%);
            pointer-events: none;
        }

        .product-window {
            position: relative;
            display: grid;
            grid-template-columns: 120px 1fr;
            min-height: 420px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--line);
            background: #ffffff;
            box-shadow: var(--shadow-card);
        }

        .product-sidebar {
            padding: 18px 14px;
            background: linear-gradient(180deg, #f8fbff 0%, #f1f5f9 100%);
            border-right: 1px solid var(--line);
        }

        .product-sidebar-brand {
            font-size: 13px;
            font-weight: 800;
            color: var(--accent);
            letter-spacing: -0.02em;
            margin-bottom: 18px;
        }

        .product-nav {
            display: grid;
            gap: 10px;
        }

        .product-nav span {
            display: block;
            padding: 10px 10px;
            border-radius: 12px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .product-nav span:first-child {
            background: var(--surface-blue);
            color: var(--accent);
        }

        .product-main {
            padding: 18px;
            display: grid;
            gap: 16px;
            background: #ffffff;
        }

        .product-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .product-topline strong {
            font-size: 18px;
            font-weight: 800;
        }

        .topline-chip {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: var(--success-soft);
            color: #15803d;
            font-size: 12px;
            font-weight: 800;
        }

        .product-subcopy {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .product-map {
            min-height: 112px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background:
                linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(255, 255, 255, 0.4)),
                linear-gradient(0deg, transparent 24%, rgba(148, 163, 184, 0.14) 25%, rgba(148, 163, 184, 0.14) 26%, transparent 27%, transparent 74%, rgba(148, 163, 184, 0.14) 75%, rgba(148, 163, 184, 0.14) 76%, transparent 77%),
                linear-gradient(90deg, transparent 24%, rgba(148, 163, 184, 0.14) 25%, rgba(148, 163, 184, 0.14) 26%, transparent 27%, transparent 74%, rgba(148, 163, 184, 0.14) 75%, rgba(148, 163, 184, 0.14) 76%, transparent 77%),
                #f8fbff;
            display: grid;
            place-items: center;
            color: #475569;
            font-size: 14px;
            font-weight: 700;
        }

        .evidence-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .evidence-card {
            padding: 12px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            display: grid;
            gap: 10px;
        }

        .evidence-thumb {
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            border: 1px solid var(--line);
            background:
                linear-gradient(135deg, rgba(226, 232, 240, 0.55), rgba(248, 250, 252, 0.9)),
                url('{{ asset('assets/dossentry/hero-bg.png') }}') center/cover;
        }

        .evidence-thumb--serial {
            background:
                linear-gradient(135deg, rgba(219, 234, 254, 0.42), rgba(255, 255, 255, 0.94)),
                linear-gradient(90deg, rgba(15, 23, 42, 0.16) 0 10%, transparent 10% 14%, rgba(15, 23, 42, 0.16) 14% 18%, transparent 18% 24%, rgba(15, 23, 42, 0.16) 24% 27%, transparent 27% 31%, rgba(15, 23, 42, 0.16) 31% 35%, transparent 35% 41%, rgba(15, 23, 42, 0.16) 41% 45%, transparent 45% 52%, rgba(15, 23, 42, 0.16) 52% 58%, transparent 58% 100%);
        }

        .evidence-thumb--labels {
            background:
                linear-gradient(135deg, rgba(255, 247, 237, 0.82), rgba(255, 255, 255, 0.94)),
                radial-gradient(circle at top left, rgba(249, 115, 22, 0.2), transparent 44%);
        }

        .evidence-card strong {
            font-size: 13px;
            font-weight: 800;
        }

        .evidence-card span {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .suggestion-panel {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
            border: 1px solid rgba(37, 99, 235, 0.14);
            display: grid;
            gap: 12px;
        }

        .suggestion-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
        }

        .suggestion-row strong {
            color: var(--ink);
            font-size: 13px;
            font-weight: 800;
        }

        .section {
            padding: 72px 0;
        }

        .section-head {
            margin-bottom: 28px;
        }

        .section-kicker {
            display: inline-block;
            margin-bottom: 12px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h2 {
            font-size: clamp(34px, 4vw, 50px);
            line-height: 1.06;
            font-weight: 900;
            max-width: 14ch;
        }

        .section-head p {
            margin: 14px 0 0;
            max-width: 64ch;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.7;
        }

        .workflow-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .workflow-card {
            min-height: 232px;
            padding: 28px 24px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background: var(--surface);
            box-shadow: var(--shadow-card);
        }

        .workflow-index {
            display: block;
            margin-bottom: 22px;
            color: #d8dee8;
            font-size: 56px;
            font-weight: 900;
            letter-spacing: -0.06em;
            line-height: 1;
        }

        .workflow-card h3 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .workflow-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.72;
            font-size: 15px;
        }

        .split-section {
            display: grid;
            grid-template-columns: minmax(0, 0.92fr) minmax(0, 1.08fr);
            gap: 28px;
            align-items: start;
        }

        .audience-panel,
        .deployment-panel,
        .faq-panel,
        .cta-panel {
            padding: 30px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: var(--surface);
            box-shadow: var(--shadow-card);
        }

        .audience-panel {
            display: grid;
            gap: 20px;
        }

        .audience-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .audience-chip {
            min-height: 82px;
            padding: 16px;
            border-radius: 18px;
            background: var(--bg-soft);
            border: 1px solid var(--line);
            display: grid;
            align-content: center;
            justify-items: center;
            text-align: center;
            gap: 6px;
        }

        .audience-chip strong {
            font-size: 14px;
            font-weight: 800;
        }

        .audience-chip span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .deployment-panel h2 {
            max-width: none;
            font-size: clamp(30px, 3vw, 42px);
            text-align: center;
        }

        .deployment-panel p {
            margin: 12px auto 0;
            max-width: 58ch;
            text-align: center;
            color: var(--muted);
            line-height: 1.72;
        }

        .deployment-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-top: 26px;
        }

        .deployment-card {
            min-height: 188px;
            padding: 20px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        }

        .deployment-card--blue {
            background: linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%);
        }

        .deployment-card--cream {
            background: linear-gradient(180deg, #ffffff 0%, #fff8ef 100%);
        }

        .deployment-card--ink {
            background: linear-gradient(180deg, #18212d 0%, #1f2937 100%);
            border-color: rgba(15, 23, 42, 0.22);
            color: #f8fafc;
        }

        .deployment-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            background: rgba(37, 99, 235, 0.12);
            color: var(--accent);
            font-size: 18px;
            font-weight: 900;
        }

        .deployment-card--cream .deployment-icon {
            background: rgba(251, 146, 60, 0.12);
            color: #c2410c;
        }

        .deployment-card--ink .deployment-icon {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .deployment-card h3 {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.15;
        }

        .deployment-card p {
            margin: 0;
            text-align: left;
            max-width: none;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .deployment-card--ink p {
            color: rgba(248, 250, 252, 0.84);
        }

        .faq-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 24px;
            margin-top: 28px;
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

        .faq-panel {
            padding: 10px 22px;
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
            content: "−";
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
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .cta-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
            gap: 24px;
        }

        .review-lead p {
            margin: 14px 0 0;
            max-width: 58ch;
            color: var(--muted);
            line-height: 1.72;
        }

        .review-form {
            margin-top: 26px;
            display: grid;
            gap: 14px;
            padding: 24px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
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
            color: var(--ink);
            font-size: 13px;
            font-weight: 800;
            line-height: 1.4;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid var(--line-strong);
            background: #ffffff;
            color: var(--ink);
            font: inherit;
            transition: border-color 180ms ease, box-shadow 180ms ease;
            outline: none;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: rgba(37, 99, 235, 0.5);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .field textarea {
            min-height: 148px;
            resize: vertical;
        }

        .field--full {
            grid-column: 1 / -1;
        }

        .form-success,
        .form-errors {
            border-radius: 18px;
            padding: 16px 18px;
            border: 1px solid var(--line);
            font-size: 14px;
            line-height: 1.7;
        }

        .form-success {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .form-errors {
            background: #fff1f2;
            color: #be123c;
        }

        .form-errors ul {
            margin: 10px 0 0;
            padding-left: 18px;
        }

        .form-note {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .cta-meta {
            display: grid;
            gap: 14px;
            align-content: start;
        }

        .cta-note {
            padding: 20px;
            border-radius: 20px;
            border: 1px solid var(--line);
            background: #ffffff;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        }

        .cta-note strong {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
        }

        .cta-note p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.72;
        }

        .credential-list {
            margin-top: 14px;
            display: grid;
            gap: 10px;
        }

        .credential-row {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 10px;
            align-items: center;
        }

        .credential-row span {
            color: #475569;
            font-size: 13px;
            font-weight: 800;
        }

        .credential-row code {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 10px 12px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: var(--bg-soft);
            color: var(--ink);
            font-size: 13px;
            word-break: break-word;
        }

        .stack-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .footer {
            padding: 0 0 36px;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 1120px) {
            .hero-card,
            .audience-panel,
            .deployment-panel,
            .cta-panel {
                padding: 32px;
            }

            .hero-grid,
            .split-section,
            .cta-grid {
                grid-template-columns: 1fr;
            }

            .workflow-grid,
            .deployment-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .audience-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .faq-layout {
                grid-template-columns: 1fr;
            }

            h1 {
                max-width: 12ch;
            }
        }

        @media (max-width: 820px) {
            .shell {
                width: min(var(--max), calc(100% - 24px));
            }

            .topbar-inner,
            .topbar-actions {
                flex-wrap: wrap;
            }

            .topbar-inner {
                gap: 18px;
            }

            .topnav,
            .topbar-actions {
                width: 100%;
            }

            .topbar-actions {
                justify-content: flex-start;
            }

            .product-window {
                grid-template-columns: 1fr;
            }

            .product-sidebar {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .evidence-grid,
            .workflow-grid,
            .deployment-grid,
            .form-grid,
            .audience-grid {
                grid-template-columns: 1fr;
            }

            .hero-actions,
            .stack-actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }

        @media (max-width: 560px) {
            .hero-card,
            .audience-panel,
            .deployment-panel,
            .cta-panel {
                padding: 22px;
            }

            .hero {
                padding-top: 26px;
            }

            h1 {
                font-size: clamp(38px, 12vw, 52px);
            }

            h2 {
                font-size: 34px;
            }

            .credential-row {
                grid-template-columns: 1fr;
            }

            .review-form {
                padding: 18px;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="shell">
            <div class="topbar-inner">
                <a class="brand" href="#top">
                    <span class="brand-mark">D</span>
                    <span>{{ $appName }}</span>
                </a>
                <nav class="topnav">
                    <a href="#workflow">Products</a>
                    <a href="#deployment">Solutions</a>
                    <a href="#faq">Resources</a>
                </nav>
                <div class="topbar-actions">
                    <a class="login-link" href="{{ $demoLoginUrl }}">Log in</a>
                    <a class="button button-primary" href="{{ $demoLoginUrl }}">Enter Guest Demo</a>
                </div>
            </div>
        </div>
    </header>

    <main id="top">
        <section class="hero">
            <div class="shell">
                <div class="hero-card">
                    <div class="hero-grid">
                        <article class="hero-copy">
                            <span class="eyebrow">Brand-ready review workflow</span>
                            <h1>Defensible Return <span class="hero-accent">Evidence</span>, Generated Instantly.</h1>
                            <p>
                                Stop reconstruction guesswork. Capture brand-ready photo evidence, playbooks,
                                and recommendations in one clean, shareable review link.
                            </p>
                            <div class="hero-actions">
                                <a class="button button-primary" href="#review-request">Request Workflow Review</a>
                                <a class="button button-secondary" href="{{ $demoLoginUrl }}">Enter Guest Demo</a>
                            </div>
                            <div class="hero-proof">
                                <span class="hero-proof-pill">No station cameras</span>
                                <span class="hero-proof-pill">No warehouse rebuild</span>
                                <span class="hero-proof-pill">Customer-side Docker deployment</span>
                            </div>
                        </article>

                        <aside class="hero-visual">
                            <div class="product-stage">
                                <div class="product-window">
                                    <div class="product-sidebar">
                                        <div class="product-sidebar-brand">{{ $appName }}</div>
                                        <div class="product-nav">
                                            <span>Timeline</span>
                                            <span>Playbooks</span>
                                            <span>Evidence</span>
                                            <span>Recommendation</span>
                                            <span>Share</span>
                                        </div>
                                    </div>
                                    <div class="product-main">
                                        <div class="product-topline">
                                            <strong>Brand Review Link</strong>
                                            <span class="topline-chip">Mobile view</span>
                                        </div>
                                        <div class="product-subcopy">Return Case: RMA-1003</div>
                                        <div class="product-map">Defensible return timeline and review record</div>
                                        <div class="evidence-grid">
                                            <article class="evidence-card">
                                                <div class="evidence-thumb"></div>
                                                <strong>Close-up</strong>
                                                <span>Damage evidence captured at inspection.</span>
                                            </article>
                                            <article class="evidence-card">
                                                <div class="evidence-thumb evidence-thumb--serial"></div>
                                                <strong>Serial Number</strong>
                                                <span>Readable serial photo attached to the case.</span>
                                            </article>
                                            <article class="evidence-card">
                                                <div class="evidence-thumb evidence-thumb--labels"></div>
                                                <strong>Labels</strong>
                                                <span>Warehouse label and packaging reference stored.</span>
                                            </article>
                                        </div>
                                        <div class="suggestion-panel">
                                            <div class="suggestion-row">
                                                <strong>Playbook Applied</strong>
                                                <span>Apple Accessories</span>
                                            </div>
                                            <div class="suggestion-row">
                                                <strong>Suggested Action</strong>
                                                <span>Hold for brand review</span>
                                            </div>
                                            <div class="suggestion-row">
                                                <strong>Suggested Reason</strong>
                                                <span>Serial mismatch and packaging damage</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="workflow">
            <div class="shell">
                <div class="section-head">
                    <span class="section-kicker">Workflow</span>
                    <h2>One clean workflow from capture to share.</h2>
                    <p>
                        Built for disputed returns that need evidence, rule coverage, and a reviewable recommendation,
                        not just stock movement.
                    </p>
                </div>
                <div class="workflow-grid">
                    <article class="workflow-card">
                        <span class="workflow-index">01.</span>
                        <h3>Capture</h3>
                        <p>Capture brand-ready photo evidence directly from any warehouse phone with close-ups, serials, and packaging details.</p>
                    </article>
                    <article class="workflow-card">
                        <span class="workflow-index">02.</span>
                        <h3>Playbook</h3>
                        <p>Apply client-specific rules automatically during inspection so your team does not memorize SOP PDFs.</p>
                    </article>
                    <article class="workflow-card">
                        <span class="workflow-index">03.</span>
                        <h3>Review</h3>
                        <p>Centralize inspection, timeline, and recommendation in one defensible case record for ops leads.</p>
                    </article>
                    <article class="workflow-card">
                        <span class="workflow-index">04.</span>
                        <h3>Share</h3>
                        <p>Send one clean, expiration-protected Brand Review Link instead of screenshots, folders, and email stitching.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="deployment">
            <div class="shell">
                <div class="split-section">
                    <section class="audience-panel">
                        <div class="section-head" style="margin-bottom: 0;">
                            <span class="section-kicker">Built for</span>
                            <h2 style="max-width: 12ch; font-size: clamp(32px, 3.6vw, 44px);">Operations that need defensible return evidence.</h2>
                            <p>Use Dossentry for the returns that create brand questions, not just the ones that quietly move back into stock.</p>
                        </div>
                        <div class="audience-grid">
                            <div class="audience-chip">
                                <strong>Multi-brand 3PLs</strong>
                                <span>Different client playbooks</span>
                            </div>
                            <div class="audience-chip">
                                <strong>DTC Operators</strong>
                                <span>Own warehouse and returns</span>
                            </div>
                            <div class="audience-chip">
                                <strong>Ops Managers</strong>
                                <span>Need one review surface</span>
                            </div>
                            <div class="audience-chip">
                                <strong>Warehouse Teams</strong>
                                <span>Phone-first inspection flow</span>
                            </div>
                        </div>
                    </section>

                    <div>
                        <section class="deployment-panel">
                            <span class="section-kicker">Data ownership</span>
                            <h2>Your Data. Your Infrastructure. Docker Self-Hosted.</h2>
                            <p>
                                Formal customer use is delivered as a self-hosted Docker package so the database,
                                uploaded evidence, and staff accounts stay inside the customer’s own environment.
                            </p>
                            <div class="deployment-grid">
                                <article class="deployment-card deployment-card--blue">
                                    <span class="deployment-icon">D</span>
                                    <h3>Docker Deployment</h3>
                                    <p>Install Dossentry on your own infrastructure instead of operating from a shared vendor workspace.</p>
                                </article>
                                <article class="deployment-card deployment-card--cream">
                                    <span class="deployment-icon">DB</span>
                                    <h3>Private Database</h3>
                                    <p>Case records, uploads, and evidence stay in the customer database and storage, not ours.</p>
                                </article>
                                <article class="deployment-card deployment-card--ink">
                                    <span class="deployment-icon">AI</span>
                                    <h3>Customer AI Keys</h3>
                                    <p>Optional Pro setup can connect a local knowledge workspace to customer-owned OpenAI-compatible API keys.</p>
                                </article>
                            </div>
                        </section>

                        <div class="faq-layout" id="faq">
                            <div class="faq-intro">
                                <span class="section-kicker">FAQ</span>
                                <h3>Still Have Questions?</h3>
                                <p>These are the objections buyers usually raise first before trying the workflow.</p>
                            </div>
                            <section class="faq-panel">
                                <details class="faq-item">
                                    <summary>Is this a replacement for our WMS?</summary>
                                    <div class="faq-answer">No. Your WMS remains the system of record. Dossentry is for the cases that need evidence, recommendation, and a clean review trail.</div>
                                </details>
                                <details class="faq-item">
                                    <summary>Do we need special hardware?</summary>
                                    <div class="faq-answer">No. The workflow runs in a browser on standard iPhone and Android devices, including shared warehouse phones. No station cameras or scanner rollout required.</div>
                                </details>
                                <details class="faq-item">
                                    <summary>Is production data shared with Dossentry?</summary>
                                    <div class="faq-answer">No. Formal customer deployments can run as a self-hosted Docker package, so the database and uploaded evidence stay in the customer environment.</div>
                                </details>
                                <details class="faq-item">
                                    <summary>Can we add a local knowledge workspace later?</summary>
                                    <div class="faq-answer">Yes. As a Pro add-on, we can install a customer-side knowledge workspace and connect it to customer-owned OpenAI-compatible API keys.</div>
                                </details>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta" id="review-request">
            <div class="shell">
                <div class="cta-panel">
                    <div class="cta-grid">
                        <div>
                            <div class="review-lead">
                                <span class="section-kicker">Request review</span>
                                <h2 style="max-width: 12ch;">Start with a workflow review, not a heavy rollout.</h2>
                                <p>
                                    Tell us how your team currently handles disputed returns. We will use that to
                                    pressure-test whether Dossentry fits your operation before you commit to a pilot.
                                </p>
                            </div>

                            @if(session('reviewRequestSubmitted'))
                                <div class="form-success" style="margin-top: 20px;">
                                    <strong>Request received.</strong>
                                    We now have your workflow review request in the workspace. The next step is to open the live demo and compare it to your current process.
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="form-errors" style="margin-top: 20px;">
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
                                <p class="form-note">Start with one real workflow. Formal production deployment can run in your own Docker environment.</p>
                            </form>
                        </div>

                        <div class="cta-meta">
                            <article class="cta-note">
                                <strong>Start with a real example</strong>
                                <p>Open a sample Brand Review Link first. It is the fastest way to see the exact record your team could send when a brand challenges a return decision.</p>
                                @if($sampleBrandReviewUrl)
                                    <div class="stack-actions">
                                        <a class="button button-secondary" href="{{ $sampleBrandReviewUrl }}">View sample review record</a>
                                    </div>
                                @endif
                            </article>

                            <article class="cta-note">
                                <strong>Shared guest demo</strong>
                                <p>Use the live workspace exactly the way a warehouse or ops lead would. This is sample data only, and the workspace resets regularly.</p>
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
                                <div class="stack-actions">
                                    <a class="button button-secondary" href="{{ $demoLoginUrl }}">Open guest demo</a>
                                </div>
                            </article>

                            <article class="cta-note">
                                <strong>What you get back</strong>
                                <p>A short review of your current evidence flow, where cases break down, and whether Dossentry fits the way your team actually works.</p>
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
            <a href="{{ $demoLoginUrl }}">Live demo</a>
        </div>
    </footer>
</body>
</html>
