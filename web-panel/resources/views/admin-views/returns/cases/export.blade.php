<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Defense Pack #{{ $resource->id }}</title>
    <style>
        :root {
            --ink: #10233b;
            --muted: #5f6f82;
            --line: #d8e0ea;
            --surface: #f7f9fc;
            --accent: #0f6fff;
            --danger: #cf2e2e;
            --success: #147a52;
            --warning: #a76200;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 28px;
            font-family: Inter, Arial, sans-serif;
            color: var(--ink);
            background: #ffffff;
            line-height: 1.45;
        }

        .print-actions {
            margin-bottom: 18px;
        }

        .print-actions button {
            border: 0;
            border-radius: 999px;
            background: var(--accent);
            color: white;
            padding: 10px 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .header {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            background: linear-gradient(135deg, #f7fbff 0%, #eef4ff 100%);
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.15;
        }

        .hero-subtitle {
            color: var(--muted);
            font-size: 14px;
            max-width: 700px;
        }

        .status-panel {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 22px;
            background: var(--surface);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            border: 1px solid var(--line);
            background: white;
        }

        .pill.good { color: var(--success); border-color: rgba(20, 122, 82, .25); }
        .pill.warn { color: var(--warning); border-color: rgba(167, 98, 0, .25); }
        .pill.bad { color: var(--danger); border-color: rgba(207, 46, 46, .25); }

        .section {
            border: 1px solid var(--line);
            border-radius: 18px;
            margin-bottom: 18px;
            overflow: hidden;
        }

        .section-header {
            padding: 16px 20px;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }

        .section-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .section-body {
            padding: 20px;
        }

        .facts-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .fact-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 14px;
            background: white;
            min-height: 110px;
        }

        .fact-label {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
        }

        .fact-value {
            font-size: 17px;
            font-weight: 700;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 18px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 10px 0;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        .summary-table td:first-child {
            width: 38%;
            color: var(--muted);
            font-weight: 600;
            padding-right: 12px;
        }

        .checklist {
            display: grid;
            gap: 10px;
        }

        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .check-item.good {
            background: rgba(20, 122, 82, .05);
            border-color: rgba(20, 122, 82, .2);
        }

        .check-item.bad {
            background: rgba(207, 46, 46, .04);
            border-color: rgba(207, 46, 46, .18);
        }

        .flags {
            display: grid;
            gap: 10px;
        }

        .flag {
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
        }

        .flag.bad {
            color: var(--danger);
            background: rgba(207, 46, 46, .06);
            border: 1px solid rgba(207, 46, 46, .18);
        }

        .flag.good {
            color: var(--success);
            background: rgba(20, 122, 82, .06);
            border: 1px solid rgba(20, 122, 82, .2);
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .photo-card {
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            background: white;
        }

        .photo-card img {
            display: block;
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #eef2f7;
        }

        .photo-meta {
            padding: 12px 14px;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-item {
            border-left: 3px solid #cfe0ff;
            padding-left: 14px;
        }

        .timeline-time {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 4px;
        }

        .muted {
            color: var(--muted);
        }

        @page {
            margin: 12mm;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .section,
            .hero,
            .status-panel,
            .photo-card,
            .fact-card {
                break-inside: avoid;
            }
        }

        @media (max-width: 960px) {
            body { padding: 18px; }
            .header,
            .two-col,
            .facts-grid,
            .photo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $statusTone = $resource->evidence_complete ? ($resource->refund_status === 'released' ? 'good' : 'warn') : 'bad';
    @endphp

    <div class="print-actions">
        <button onclick="window.print()">Print / Save PDF</button>
    </div>

    <div class="header">
        <div class="hero">
            <div class="pill {{ $statusTone }}">Brand Defense Pack</div>
            <h1>Brand Defense Pack</h1>
            <div class="hero-subtitle">
                Built for operator review, decision support, and brand escalation. This pack combines inspection evidence,
                rule coverage, decision notes, and timeline audit history for return case <strong>{{ $resource->return_id }}</strong>.
            </div>
        </div>

        <div class="status-panel">
            <div class="fact-label">Share Readiness</div>
            <div class="fact-value" style="text-transform: capitalize; margin-bottom: 10px;">{{ $shareReadiness['label'] }}</div>
            <div class="pill {{ $shareReadiness['tone'] }}">
                {{ $resource->evidence_complete ? 'Evidence complete' : 'Evidence incomplete' }}
            </div>
            <div class="muted" style="margin-top: 14px; font-size: 14px;">
                Pack generated on {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>Executive Summary</h2>
        </div>
        <div class="section-body">
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">{{ $brandDefenseSummary }}</div>
            <div class="muted" style="font-size: 14px;">{{ $shareReadiness['summary'] }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>Case Snapshot</h2>
        </div>
        <div class="section-body">
            <div class="facts-grid">
                <div class="fact-card">
                    <div class="fact-label">Brand</div>
                    <div class="fact-value">{{ $resource->brand?->name ?? 'N/A' }}</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Condition</div>
                    <div class="fact-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $resource->condition_code) }}</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">Warehouse Action</div>
                    <div class="fact-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $resource->disposition_code) }}</div>
                </div>
                <div class="fact-card">
                    <div class="fact-label">SLA Age</div>
                    <div class="fact-value">{{ $resource->sla_age_hours }}h</div>
                </div>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div class="section">
            <div class="section-header">
                <h2>Decision Basis</h2>
            </div>
            <div class="section-body">
                <table class="summary-table">
                    @foreach($decisionBasis as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td>{{ $item['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Decision State</td>
                        <td>{{ $decisionStateLabel }}</td>
                    </tr>
                    <tr>
                        <td>SKU / Serial</td>
                        <td>{{ $resource->product_sku ?: 'N/A' }}{{ $resource->serial_number ? ' / ' . $resource->serial_number : '' }}</td>
                    </tr>
                    <tr>
                        <td>Received / Inspected</td>
                        <td>{{ $resource->received_at?->format('Y-m-d H:i') ?? 'N/A' }} / {{ $resource->inspected_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Inspector Note</td>
                        <td>{{ $resource->notes ?: 'No inspector note recorded.' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2>Rule Coverage & Share Readiness</h2>
            </div>
            <div class="section-body">
                <div class="checklist">
                    @forelse($evidenceChecklist as $item)
                        <div class="check-item {{ $item['captured'] ? 'good' : 'bad' }}">
                            <span style="text-transform: capitalize;">{{ $item['label'] }}</span>
                            <strong>{{ $item['captured'] ? 'Captured' : 'Missing' }}</strong>
                        </div>
                    @empty
                        <div class="muted">No rule-based evidence checklist is attached to this case.</div>
                    @endforelse
                </div>

                <div class="flags" style="margin-top: 16px;">
                    @forelse($actionsNeeded as $gap)
                        <div class="flag bad">{{ $gap }}</div>
                    @empty
                        <div class="flag good">No action blockers detected. This pack is ready for ops or brand-facing review.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>What This Pack Shows</h2>
        </div>
        <div class="section-body">
            <div class="flags">
                @foreach($whatThisPackShows as $fact)
                    <div class="flag good">{{ $fact }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>Evidence Gallery</h2>
        </div>
        <div class="section-body">
            <div class="photo-grid">
                @forelse($mediaAssets as $media)
                    <div class="photo-card">
                        <img src="{{ $media['web_url'] }}" alt="{{ $media['capture_type'] ?? 'evidence' }}">
                        <div class="photo-meta">
                            <div style="font-weight: 700; text-transform: capitalize;">
                                {{ str_replace('_', ' ', $media['capture_type'] ?? 'evidence') }}
                            </div>
                            <div class="muted" style="font-size: 12px; margin-top: 4px;">
                                Media slot {{ $media['sort_order'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="muted">No evidence photos uploaded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <h2>Timeline Audit</h2>
        </div>
        <div class="section-body">
            <div class="timeline">
                @forelse($timelineItems as $event)
                    <div class="timeline-item">
                        <div style="font-weight: 700;">{{ $event['title'] }}</div>
                        <div class="timeline-time">{{ $event['time'] }}</div>
                        <div>{{ $event['description'] ?: 'No event detail captured.' }}</div>
                    </div>
                @empty
                    <div class="muted">No timeline events recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</body>
</html>
