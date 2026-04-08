<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Review Record {{ $resource->return_id }}</title>
    <style>
        :root {
            --ink: #10233b;
            --muted: #607081;
            --line: #d7e1ec;
            --surface: #f6f9fc;
            --accent: #0f6fff;
            --danger: #c63a2a;
            --success: #147a52;
            --warning: #8c5a00;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f4f7fb;
            color: var(--ink);
            font-family: Inter, Arial, sans-serif;
            line-height: 1.5;
        }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 10px 16px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-light {
            background: #fff;
            border-color: var(--line);
            color: var(--ink);
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 24px;
            background: linear-gradient(135deg, #ffffff 0%, #edf4ff 100%);
            padding: 24px;
            margin-bottom: 18px;
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.12;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            max-width: 760px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 14px;
            background: #fff;
        }

        .pill.good { color: var(--success); border-color: rgba(20, 122, 82, .18); }
        .pill.warn { color: var(--warning); border-color: rgba(140, 90, 0, .22); }
        .pill.bad { color: var(--danger); border-color: rgba(198, 58, 42, .18); }

        .grid {
            display: grid;
            gap: 18px;
        }

        .facts {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .cols {
            grid-template-columns: 1.15fr .85fr;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #fff;
            overflow: hidden;
        }

        .card-header {
            padding: 16px 18px;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }

        .card-header h2,
        .card-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .card-body {
            padding: 18px;
        }

        .fact {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            padding: 16px;
            min-height: 112px;
        }

        .fact-label {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
        }

        .fact-value {
            font-size: 18px;
            font-weight: 700;
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
            font-weight: 700;
            padding-right: 10px;
        }

        .checklist,
        .flags,
        .timeline {
            display: grid;
            gap: 10px;
        }

        .check-item,
        .flag,
        .timeline-item {
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 14px;
            background: #fff;
        }

        .check-item.good,
        .flag.good {
            border-color: rgba(20, 122, 82, .18);
            background: rgba(20, 122, 82, .05);
            color: var(--success);
        }

        .check-item.bad,
        .flag.bad {
            border-color: rgba(198, 58, 42, .18);
            background: rgba(198, 58, 42, .05);
            color: var(--danger);
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
            background: #fff;
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

        .photo-meta strong {
            display: block;
            margin-bottom: 4px;
        }

        .muted {
            color: var(--muted);
        }

        .small {
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .facts,
            .cols,
            .photo-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div>
            <div class="small muted">Secure read-only review link</div>
            <div class="small muted">Link expires {{ $brandReviewExpiresAt->timezone(config('app.timezone', 'UTC'))->format('Y-m-d H:i') }}</div>
        </div>
        <div class="topbar-actions">
            <a class="btn btn-light" href="{{ $brandReviewPdfUrl }}">Download PDF</a>
        </div>
    </div>

    <section class="hero">
        <div class="pill {{ $shareReadiness['tone'] }}">Brand Review Record</div>
        <h1>{{ $resource->brand?->name ?? 'Unknown brand' }} / {{ $resource->return_id }}</h1>
        <p>{{ $brandDefenseSummary }}</p>
        <p class="small muted" style="margin-top:12px;">{{ $shareReadiness['summary'] }}</p>
    </section>

    <section class="grid facts" style="margin-bottom:18px;">
        <div class="fact">
            <div class="fact-label">Decision State</div>
            <div class="fact-value">{{ $decisionStateLabel }}</div>
        </div>
        <div class="fact">
            <div class="fact-label">Evidence Progress</div>
            <div class="fact-value">{{ $resource->evidence_photo_count }}/{{ $resource->required_photo_count }}</div>
        </div>
        <div class="fact">
            <div class="fact-label">Warehouse Action</div>
            <div class="fact-value">{{ ucfirst(str_replace('_', ' ', $resource->disposition_code)) }}</div>
        </div>
        <div class="fact">
            <div class="fact-label">SLA Age</div>
            <div class="fact-value">{{ $resource->sla_age_hours }}h</div>
        </div>
    </section>

    <section class="grid cols" style="margin-bottom:18px;">
        <div class="card">
            <div class="card-header">
                <h2>Case Snapshot</h2>
            </div>
            <div class="card-body">
                <table class="summary-table">
                    <tr><td>Return ID</td><td>{{ $resource->return_id }}</td></tr>
                    <tr><td>Brand</td><td>{{ $resource->brand?->name ?? 'N/A' }}</td></tr>
                    <tr><td>Condition</td><td>{{ ucfirst(str_replace('_', ' ', $resource->condition_code)) }}</td></tr>
                    <tr><td>Warehouse Action</td><td>{{ ucfirst(str_replace('_', ' ', $resource->disposition_code)) }}</td></tr>
                    <tr><td>SKU / Serial</td><td>{{ $resource->product_sku ?: 'N/A' }}{{ $resource->serial_number ? ' / ' . $resource->serial_number : '' }}</td></tr>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Review Basis</h2>
            </div>
            <div class="card-body">
                <table class="summary-table">
                    @foreach($decisionBasis as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td>{{ $item['value'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

    <section class="card" style="margin-bottom:18px;">
        <div class="card-header">
            <h2>Evidence Readiness</h2>
        </div>
        <div class="card-body">
            <div class="checklist">
                @forelse($evidenceChecklist as $item)
                    <div class="check-item {{ $item['captured'] ? 'good' : 'bad' }}">
                        <strong>{{ ucfirst($item['label']) }}</strong>
                        <span>{{ $item['captured'] ? 'Captured' : 'Missing' }}</span>
                    </div>
                @empty
                    <div class="flag good">No rule-based evidence checklist is attached to this case.</div>
                @endforelse
            </div>

            <div class="flags" style="margin-top:14px;">
                @forelse($actionsNeeded as $item)
                    <div class="flag bad">{{ $item }}</div>
                @empty
                    <div class="flag good">No action blockers detected. This record is ready for review.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card" style="margin-bottom:18px;">
        <div class="card-header">
            <h2>What This Record Shows</h2>
        </div>
        <div class="card-body">
            <div class="flags">
                @foreach($whatThisPackShows as $fact)
                    <div class="flag good">{{ $fact }}</div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="card" style="margin-bottom:18px;">
        <div class="card-header">
            <h2>Evidence Gallery</h2>
        </div>
        <div class="card-body">
            <div class="photo-grid">
                @forelse($mediaAssets as $media)
                    <div class="photo-card">
                        @if($media['web_url'])
                            <img src="{{ $media['web_url'] }}" alt="{{ $media['capture_type'] }}">
                        @else
                            <div style="padding:18px;" class="small muted">No image available for this evidence slot.</div>
                        @endif
                        <div class="photo-meta">
                            <strong>{{ ucfirst(str_replace('_', ' ', $media['capture_type'])) }}</strong>
                            <div class="small muted">Media slot {{ $media['sort_order'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="flag bad">No evidence photos uploaded.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-header">
            <h2>Timeline</h2>
        </div>
        <div class="card-body">
            <div class="timeline">
                @forelse($timelineItems as $event)
                    <div class="timeline-item">
                        <div class="small muted">{{ $event['time'] }}</div>
                        <strong>{{ $event['title'] }}</strong>
                    </div>
                @empty
                    <div class="flag bad">No timeline events recorded.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
</body>
</html>
