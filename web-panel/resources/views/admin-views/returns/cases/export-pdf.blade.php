<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $externalView ?? false ? 'Brand Review Record' : 'Brand Defense Pack' }} {{ $resource->return_id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #10233b;
            line-height: 1.45;
        }

        h1, h2, h3, p {
            margin: 0 0 8px;
        }

        .header {
            border: 1px solid #d8e0ea;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
            background: #f7f9fc;
        }

        .subtle {
            color: #5f6f82;
        }

        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #d8e0ea;
            margin-bottom: 8px;
        }

        .pill.good {
            color: #147a52;
            border-color: #b7dfcc;
        }

        .pill.warn {
            color: #a76200;
            border-color: #e3c49f;
        }

        .pill.bad {
            color: #cf2e2e;
            border-color: #f0b0b0;
        }

        .section {
            margin-bottom: 14px;
            border: 1px solid #d8e0ea;
            border-radius: 10px;
            overflow: hidden;
        }

        .section-header {
            padding: 10px 12px;
            background: #f7f9fc;
            border-bottom: 1px solid #d8e0ea;
        }

        .section-body {
            padding: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border-bottom: 1px solid #e7edf5;
            padding: 8px 0;
            vertical-align: top;
            text-align: left;
        }

        .label {
            width: 34%;
            color: #5f6f82;
            font-weight: bold;
            padding-right: 10px;
        }

        .flag {
            border: 1px solid #d8e0ea;
            border-radius: 8px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .flag.good {
            background: #f1fbf5;
            border-color: #b7dfcc;
            color: #147a52;
        }

        .flag.bad {
            background: #fff5f5;
            border-color: #f0b0b0;
            color: #b42318;
        }

        .two-col {
            width: 100%;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
            border-bottom: 0;
        }

        .photo-block {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #d8e0ea;
            border-radius: 8px;
            overflow: hidden;
        }

        .photo-block img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
            background: #eef2f7;
        }

        .photo-meta {
            padding: 8px 10px;
        }

        .placeholder {
            height: 90px;
            padding: 16px;
            background: #f7f9fc;
            color: #5f6f82;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="pill {{ $shareReadiness['tone'] }}">{{ ($externalView ?? false) ? 'Brand Review Record' : 'Brand Defense Pack' }}</div>
        <h1>{{ $resource->brand?->name ?? 'Unknown brand' }} - {{ $resource->return_id }}</h1>
        <p>{{ $brandDefenseSummary }}</p>
        <p class="subtle">{{ $shareReadiness['summary'] }}</p>
    </div>

    <table class="two-col" role="presentation">
        <tr>
            <td>
                <div class="section">
                    <div class="section-header"><h2>Case Snapshot</h2></div>
                    <div class="section-body">
                        <table>
                            <tr><td class="label">Case ID</td><td>#{{ $resource->id }} / {{ $resource->return_id }}</td></tr>
                            <tr><td class="label">Brand</td><td>{{ $resource->brand?->name ?? 'N/A' }}</td></tr>
                            <tr><td class="label">Condition</td><td>{{ ucfirst(str_replace('_', ' ', $resource->condition_code)) }}</td></tr>
                            <tr><td class="label">Warehouse Action</td><td>{{ ucfirst(str_replace('_', ' ', $resource->disposition_code)) }}</td></tr>
                            <tr><td class="label">Decision State</td><td>{{ $decisionStateLabel }}</td></tr>
                            <tr><td class="label">SLA Age</td><td>{{ $resource->sla_age_hours }}h</td></tr>
                            <tr><td class="label">SKU / Serial</td><td>{{ $resource->product_sku ?: 'N/A' }}{{ $resource->serial_number ? ' / ' . $resource->serial_number : '' }}</td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>
                <div class="section">
                    <div class="section-header"><h2>Decision Basis</h2></div>
                    <div class="section-body">
                        <table>
                            @foreach($decisionBasis as $item)
                                <tr><td class="label">{{ $item['label'] }}</td><td>{{ $item['value'] }}</td></tr>
                            @endforeach
                            @unless($externalView ?? false)
                                <tr><td class="label">Inspector Note</td><td>{{ $resource->notes ?: 'No inspector note recorded.' }}</td></tr>
                            @endunless
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-header"><h2>Rule Coverage</h2></div>
        <div class="section-body">
            @forelse($evidenceChecklist as $item)
                <div class="flag {{ $item['captured'] ? 'good' : 'bad' }}">
                    {{ ucfirst($item['label']) }}: {{ $item['captured'] ? 'Captured' : 'Missing' }}
                </div>
            @empty
                <div class="flag good">No rule-based evidence checklist is attached to this case.</div>
            @endforelse

            @forelse($actionsNeeded as $item)
                <div class="flag bad">{{ $item }}</div>
            @empty
                <div class="flag good">No action blockers detected. This record is ready for review.</div>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-header"><h2>What This Pack Shows</h2></div>
        <div class="section-body">
            @foreach($whatThisPackShows as $fact)
                <div class="flag good">{{ $fact }}</div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <div class="section-header"><h2>Evidence Gallery</h2></div>
        <div class="section-body">
            @forelse($mediaAssets as $media)
                <div class="photo-block">
                    @if($media['has_file'])
                        <img src="{{ $media['pdf_path'] }}" alt="{{ $media['capture_type'] }}">
                    @else
                        <div class="placeholder">
                            Evidence file unavailable in local storage for this export. Capture type: {{ ucfirst(str_replace('_', ' ', $media['capture_type'])) }}.
                        </div>
                    @endif
                    <div class="photo-meta">
                        <strong>{{ ucfirst(str_replace('_', ' ', $media['capture_type'])) }}</strong><br>
                        <span class="subtle">Media slot {{ $media['sort_order'] }}</span>
                    </div>
                </div>
            @empty
                <div class="flag bad">No evidence photos uploaded.</div>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-header"><h2>Timeline Audit</h2></div>
        <div class="section-body">
            <table>
                @forelse($timelineItems as $event)
                    <tr>
                        <td class="label">{{ $event['time'] }}</td>
                        <td>
                            <strong>{{ $event['title'] }}</strong>
                            @if(!empty($event['description']))
                                <br>{{ $event['description'] }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No timeline events recorded.</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
</body>
</html>
