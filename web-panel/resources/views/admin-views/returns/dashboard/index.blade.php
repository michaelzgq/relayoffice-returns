@extends('layouts.admin.app')

@section('title', 'Ops Board')

@push('css_or_js')
    <style>
        .sla-card-link {
            color: inherit;
            text-decoration: none;
            display: block;
            height: 100%;
        }

        .sla-card-link:hover {
            color: inherit;
            text-decoration: none;
        }

        .sla-card-link .card {
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .sla-card-link:hover .card {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, .08);
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">Ops Board</h1>
                <p class="text-muted mb-0">One screen for daily inspection throughput, refund decisions, backlog, and evidence gaps.</p>
            </div>
            <div class="col-sm-auto mt-3 mt-sm-0">
                <div class="d-flex gap-2 flex-wrap">
                    <a class="btn btn-light" href="{{ route('admin.returns.cases.index') }}">Open cases</a>
                    <a class="btn btn-light" href="{{ route('admin.returns.queue.index') }}">Open queue</a>
                    <a class="btn btn-primary" href="{{ route('admin.returns.inspect') }}">Start inspection</a>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-sm-6">
                <a class="sla-card-link" href="{{ route('admin.returns.cases.index') }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Inspections today</div>
                            <div class="display-4">{{ $inspectionsToday }}</div>
                            <div class="small text-primary mt-2">Open the full case list</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-sm-6">
                <a class="sla-card-link" href="{{ route('admin.returns.queue.index') }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Awaiting refund action</div>
                            <div class="display-4">{{ $awaitingRefundAction }}</div>
                            <div class="small text-primary mt-2">Open the live refund queue</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-sm-6">
                <a class="sla-card-link" href="{{ route('admin.returns.queue.index', ['filter_status' => 'ready_to_release']) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Ready to release</div>
                            <div class="display-4">{{ $readyToReleaseCount }}</div>
                            <div class="small text-primary mt-2">Open release-ready cases</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-sm-6">
                <a class="sla-card-link" href="{{ route('admin.returns.queue.index', ['filter_status' => 'hold', 'min_sla_hours' => 48]) }}">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-muted small">Over 48h stuck in hold</div>
                            <div class="display-4">{{ $over48hStuck }}</div>
                            <div class="small text-primary mt-2">Open aged hold queue</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Brands with Backlog</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Brand</th>
                                <th class="text-right">Backlog</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($brandsWithHighestBacklog as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.returns.queue.index', ['brand_id' => $item->brand_id]) }}">
                                            {{ $item->brand?->name ?? 'Unknown brand' }}
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.returns.queue.index', ['brand_id' => $item->brand_id]) }}">
                                            {{ $item->backlog }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">No backlog yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">Missing Evidence</h4>
                                <div class="text-muted small mt-1">{{ $missingEvidenceCount }} open case(s) blocked by evidence gaps</div>
                            </div>
                            <a class="btn btn-sm btn-light" href="{{ route('admin.returns.queue.index', ['evidence_missing' => 1]) }}">Open queue</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Return ID</th>
                                <th>Brand</th>
                                <th>Evidence</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($missingEvidenceCases as $case)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.returns.cases.show', $case->id) }}">{{ $case->return_id }}</a>
                                    </td>
                                    <td>{{ $case->brand?->name ?? 'N/A' }}</td>
                                    <td>{{ $case->evidence_photo_count }}/{{ $case->required_photo_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No missing evidence cases.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">Recent Inspections</h4>
                            <div class="text-muted small mt-1">Latest dock-door submissions</div>
                        </div>
                        <a class="btn btn-sm btn-light" href="{{ route('admin.returns.cases.index') }}">Open cases</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Return ID</th>
                                <th>Brand</th>
                                <th class="text-right">SLA</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentInspections as $case)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.returns.cases.show', $case->id) }}">{{ $case->return_id }}</a>
                                    </td>
                                    <td>{{ $case->brand?->name ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $case->sla_age_hours }}h</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No inspections yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
