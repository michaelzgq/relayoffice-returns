@extends('layouts.admin.app')

@section('title', 'Workflow Review Requests')

@section('content')
    @php
        $badgeMap = [
            'new' => 'badge-soft-warning',
            'reviewed' => 'badge-soft-success',
        ];
        $notificationBadgeMap = [
            'pending' => 'badge-soft-secondary',
            'sent' => 'badge-soft-success',
            'failed' => 'badge-soft-danger',
        ];
        $trackedCtas = [
            'sample_review' => 'Sample review',
            'guest_demo' => 'Guest demo',
            'workflow_review' => 'Workflow review',
            'compare' => 'Compare',
            'login' => 'Log in',
            'back_to_site' => 'Back to site',
        ];
    @endphp

    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">Workflow Review Requests</h1>
                <p class="text-muted mb-0">Inbound leads captured from the public landing page.</p>
            </div>
            <div class="col-sm-auto mt-3 mt-sm-0 d-flex flex-wrap gap-2">
                <span class="badge badge-soft-warning p-2">New {{ $statusCounts['new'] ?? 0 }}</span>
                <span class="badge badge-soft-success p-2">Reviewed {{ $statusCounts['reviewed'] ?? 0 }}</span>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <h4 class="mb-2">Notification diagnostics</h4>
                    <div class="small text-muted mb-1">Recipient: <span class="text-dark">{{ $mailDiagnostics['notificationEmail'] ?: 'Not configured' }}</span></div>
                    <div class="small text-muted mb-1">From address: <span class="text-dark">{{ $mailDiagnostics['mailFromAddress'] ?: 'Not configured' }}</span></div>
                    <div class="small text-muted">Mailer: <span class="text-dark">{{ $mailDiagnostics['mailMailer'] ?: 'Not configured' }}</span></div>
                    @if($mailDiagnostics['sameGmailMailbox'])
                        <div class="alert alert-warning mt-3 mb-0">
                            Gmail self-send detected. If the notification recipient matches the Gmail sender, the message may appear in
                            <strong>All Mail</strong>, <strong>Sent</strong>, or an existing thread instead of surfacing like a fresh inbox alert.
                        </div>
                    @endif
                </div>
                <div class="d-flex flex-column align-items-lg-end justify-content-center gap-2">
                    <form method="post" action="{{ route('admin.returns.review-requests.test-notification') }}">
                        @csrf
                        <button class="btn btn-outline-primary" type="submit">Send test email</button>
                    </form>
                    <div class="small text-muted text-lg-right">Use this before testing the public form again.</div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2">
                    <div class="col-md-6">
                        <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, company, or work email">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="status">
                            <option value="">All statuses</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end gap-2">
                        <a class="btn btn-light" href="{{ route('admin.returns.review-requests.index') }}">Reset</a>
                        <button class="btn btn-primary" type="submit">Apply filters</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="mb-2">CTA Click Tracking</h4>
                        <p class="text-muted mb-0">First-party click events from public landing and compare pages.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-start">
                        @foreach($trackedCtas as $ctaKey => $ctaLabel)
                            <span class="badge badge-soft-secondary p-2">
                                {{ $ctaLabel }} {{ $clickSummary['ctaCounts30Days'][$ctaKey] ?? 0 }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-1">Last 7 days</div>
                            <div class="h3 mb-0">{{ $clickSummary['last7Days'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-1">Last 30 days</div>
                            <div class="h3 mb-0">{{ $clickSummary['last30Days'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="small text-muted mb-1">Unique clients (30d)</div>
                            <div class="h3 mb-0">{{ $clickSummary['uniqueClients30Days'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="mb-2">Top CTA combinations (30d)</h5>
                    @if(($clickSummary['topCtas30Days'] ?? collect())->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach($clickSummary['topCtas30Days'] as $row)
                                <div class="d-flex flex-column flex-lg-row justify-content-between border rounded px-3 py-2 gap-2">
                                    <div>
                                        <strong>{{ \Illuminate\Support\Str::of($row->cta_key)->replace('_', ' ')->title() }}</strong>
                                        <span class="text-muted">on {{ \Illuminate\Support\Str::of($row->page_key)->replace('_', ' ')->title() }}</span>
                                        <span class="text-muted">/ {{ $row->placement ?: 'unknown placement' }}</span>
                                    </div>
                                    <div class="font-weight-bold">{{ $row->total }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">No CTA click events yet.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Contact</th>
                        <th>Company</th>
                        <th>Role</th>
                        <th>Volume</th>
                        <th>Workflow note</th>
                        <th>Status</th>
                        <th>Notification</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $resource->full_name }}</div>
                                <div class="small text-muted">{{ $resource->work_email }}</div>
                                @if($resource->submitted_from_host)
                                    <div class="small text-muted">{{ $resource->submitted_from_host }}</div>
                                @endif
                            </td>
                            <td>{{ $resource->company_name }}</td>
                            <td>{{ $resource->role_title ?: 'N/A' }}</td>
                            <td>{{ $resource->volume_note ?: 'Not specified' }}</td>
                            <td style="min-width: 320px;">
                                <div class="text-wrap">{{ $resource->workflow_note }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeMap[$resource->status] ?? 'badge-soft-secondary' }}">
                                    {{ ucfirst($resource->status) }}
                                </span>
                                @if($resource->reviewed_at)
                                    <div class="small text-muted mt-1">{{ $resource->reviewed_at->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td style="min-width: 260px;">
                                <span class="badge {{ $notificationBadgeMap[$resource->notification_status ?? 'pending'] ?? 'badge-soft-secondary' }}">
                                    {{ ucfirst($resource->notification_status ?? 'pending') }}
                                </span>
                                @if($resource->notification_sent_at)
                                    <div class="small text-muted mt-1">
                                        Sent {{ $resource->notification_sent_at->format('M j, Y g:i A') }}
                                    </div>
                                @elseif($resource->notification_attempted_at)
                                    <div class="small text-muted mt-1">
                                        Attempted {{ $resource->notification_attempted_at->format('M j, Y g:i A') }}
                                    </div>
                                @endif
                                @if($resource->notification_error)
                                    <div class="small text-danger mt-1 text-wrap">
                                        {{ \Illuminate\Support\Str::limit($resource->notification_error, 180) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $resource->created_at->format('M j, Y') }}</div>
                                <div class="small text-muted">{{ $resource->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="text-right">
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <form method="post" action="{{ route('admin.returns.review-requests.resend-notification', $resource->id) }}">
                                        @csrf
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit">Resend email</button>
                                    </form>

                                    @if($resource->status !== 'reviewed')
                                        <form method="post" action="{{ route('admin.returns.review-requests.mark-reviewed', $resource->id) }}">
                                            @csrf
                                            <input type="hidden" name="search" value="{{ request('search') }}">
                                            <input type="hidden" name="status" value="{{ request('status') }}">
                                            <input type="hidden" name="page" value="{{ request('page') }}">
                                            <button class="btn btn-sm btn-outline-primary" type="submit">Mark reviewed</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Completed</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No workflow review requests yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer border-0">
                {{ $resources->links() }}
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header border-0 pb-0">
                <h4 class="mb-1">Recent CTA clicks</h4>
                <p class="text-muted mb-0">Latest public click events recorded before a workflow review request is submitted.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>CTA</th>
                        <th>Page</th>
                        <th>Source</th>
                        <th>Target</th>
                        <th>UTM</th>
                        <th>When</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($clickSummary['recentClicks'] as $click)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ \Illuminate\Support\Str::of($click->cta_key)->replace('_', ' ')->title() }}</div>
                                <div class="small text-muted">{{ $click->cta_label ?: 'No label captured' }}</div>
                            </td>
                            <td>
                                <div>{{ \Illuminate\Support\Str::of($click->page_key)->replace('_', ' ')->title() }}</div>
                                <div class="small text-muted">{{ $click->placement ?: 'unknown placement' }}</div>
                            </td>
                            <td>
                                <div class="small">{{ $click->source_host ?: 'unknown host' }}</div>
                                <div class="small text-muted text-wrap">{{ $click->landing_path ?: $click->source_path ?: '/' }}</div>
                            </td>
                            <td>
                                <div class="small">{{ $click->target_host ?: 'unknown host' }}</div>
                                <div class="small text-muted text-wrap">{{ $click->target_path ?: '/' }}</div>
                            </td>
                            <td style="min-width: 200px;">
                                <div class="small text-wrap">
                                    @if($click->utm_source || $click->utm_medium || $click->utm_campaign)
                                        {{ collect([
                                            $click->utm_source ? 'src=' . $click->utm_source : null,
                                            $click->utm_medium ? 'med=' . $click->utm_medium : null,
                                            $click->utm_campaign ? 'cmp=' . $click->utm_campaign : null,
                                        ])->filter()->implode(' · ') }}
                                    @else
                                        <span class="text-muted">No UTM</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>{{ $click->created_at->format('M j, Y') }}</div>
                                <div class="small text-muted">{{ $click->created_at->format('g:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No CTA click events yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
