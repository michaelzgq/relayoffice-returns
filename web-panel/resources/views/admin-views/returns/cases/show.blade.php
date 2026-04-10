@extends('layouts.admin.app')

@section('title', \App\CPU\translate('return_case_detail'))

@section('content')
    @php
        $badgeMap = [
            'hold' => 'badge-soft-warning',
            'ready_to_release' => 'badge-soft-info',
            'needs_review' => 'badge-soft-danger',
            'released' => 'badge-soft-success',
        ];
        $statusLabels = \App\Models\ReturnCase::decisionStatusLabels();
        $statusHelp = \App\Models\ReturnCase::decisionStatusHelp();
        $canInspect = \App\CPU\Helpers::admin_has_module('returns_inspect_section');
    @endphp
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h1 class="page-header-title mb-0">Case #{{ $resource->id }}</h1>
                <p class="text-muted mb-0">Return ID {{ $resource->return_id }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($canInspect)
                    <a href="{{ route('admin.returns.inspect', ['case_id' => $resource->id]) }}" class="btn btn-light">Edit inspection</a>
                @endif
                <a href="{{ route('admin.returns.cases.export', $resource->id) }}" class="btn btn-primary" target="_blank">Open Brand Defense Pack</a>
                <a href="{{ route('admin.returns.cases.export', ['id' => $resource->id, 'download' => 'pdf']) }}" class="btn btn-outline-primary">Download PDF</a>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Brand</div>
                        <div class="h4 mb-0">{{ $resource->brand?->name ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Decision State</div>
                        <div class="mt-2">
                            <span class="badge {{ $badgeMap[$resource->refund_status] ?? 'badge-soft-secondary' }}">
                                {{ $statusLabels[$resource->refund_status] ?? str_replace('_', ' ', $resource->refund_status) }}
                            </span>
                        </div>
                        <div class="text-muted small mt-2">{{ $statusHelp[$resource->refund_status] ?? 'Case status summary is available in the timeline.' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Evidence Progress</div>
                        <div class="h4 mb-0">{{ $resource->evidence_photo_count }}/{{ $resource->required_photo_count }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">SLA Age</div>
                        <div class="h4 mb-0">{{ $resource->sla_age_hours }}h</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Inspection Summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Condition</div>
                                <div class="font-weight-bold text-capitalize">{{ str_replace('_', ' ', $resource->condition_code) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Disposition</div>
                                <div class="font-weight-bold text-capitalize">{{ str_replace('_', ' ', $resource->disposition_code) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">SKU / Serial</div>
                                <div class="font-weight-bold">{{ $resource->product_sku ?: 'N/A' }}{{ $resource->serial_number ? ' / ' . $resource->serial_number : '' }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $resource->notes ?: 'No notes recorded.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Brand Review Link</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Create a signed read-only case record for brand-side review without exposing internal queue controls.</p>
                        <form method="get" action="{{ route('admin.returns.cases.show', $resource->id) }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="title">Link expires in</label>
                                    <select class="form-control" name="share_days" onchange="this.form.submit()">
                                        @foreach($shareExpiryOptions as $days => $label)
                                            <option value="{{ $days }}" {{ (int) $shareExpiryDays === (int) $days ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="text-muted small">This link stays valid until {{ $shareExpiresAt->format('Y-m-d H:i') }}.</div>
                                </div>
                            </div>
                        </form>

                        <div class="form-group">
                            <label class="title">Brand Review Link</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="brand-review-link" value="{{ $brandReviewUrl }}" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="copy-brand-review-link" data-copy-target="#brand-review-link">Copy</button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="title">Signed PDF Link</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="brand-review-pdf-link" value="{{ $brandReviewPdfUrl }}" readonly>
                                <div class="input-group-append">
                                    <a class="btn btn-light" href="{{ $brandReviewUrl }}" target="_blank">Open</a>
                                    <a class="btn btn-outline-primary" href="{{ $brandReviewPdfUrl }}" target="_blank">PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Evidence</h4>
                    </div>
                    <div class="card-body">
                        @include('admin-views.returns.partials._evidence_grid', ['resource' => $resource])
                    </div>
                </div>

                @include('admin-views.returns.partials._timeline', ['resource' => $resource])
            </div>

            <div class="col-lg-4">
                @if($canManageRefundGate)
                    <div class="card mb-3">
                        <div class="card-header border-0">
                            <h4 class="mb-0">Decision Review</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('admin.returns.cases.refund-decision', $resource->id) }}">
                                @csrf
                                <div class="form-group">
                                    <label class="title">Decision state</label>
                                    <select class="form-control" name="refund_status">
                                        @foreach($refundStatusOptions as $status)
                                            <option value="{{ $status }}" {{ $resource->refund_status === $status ? 'selected' : '' }}>
                                                {{ $statusLabels[$status] ?? str_replace('_', ' ', ucfirst($status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="title">Decision note</label>
                                    <textarea class="form-control" rows="4" name="decision_note" placeholder="Explain what should happen next and why.">{{ old('decision_note', $resource->refundDecision?->reason) }}</textarea>
                                </div>
                                <button class="btn btn-primary btn-block" type="submit">Update decision review</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card mb-3">
                        <div class="card-header border-0">
                            <h4 class="mb-0">Decision Review</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-muted small mb-2">Current decision</div>
                            <span class="badge {{ $badgeMap[$resource->refund_status] ?? 'badge-soft-secondary' }}">
                                {{ $statusLabels[$resource->refund_status] ?? str_replace('_', ' ', $resource->refund_status) }}
                            </span>
                            <div class="text-muted small mt-3">Inspectors can review evidence and notes here, but only ops users can change the decision state.</div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Playbook</h4>
                    </div>
                    <div class="card-body">
                        @if($resource->ruleProfile)
                            <div class="mb-2"><strong>{{ $resource->ruleProfile->profile_name }}</strong></div>
                            <div class="text-muted small mb-2">Default decision state: {{ \App\Models\ReturnCase::decisionStatusLabel($resource->ruleProfile->default_refund_status) }}</div>
                            <div class="text-muted small mb-2">Recommended actions: {{ collect($resource->ruleProfile->recommended_dispositions ?? [])->map(fn ($item, $condition) => str_replace('_', ' ', $condition) . ' -> ' . str_replace('_', ' ', $item))->implode(', ') ?: 'No default actions' }}</div>
                            <div class="text-muted small mb-2">Conditions: {{ implode(', ', array_map(fn ($item) => str_replace('_', ' ', $item), $resource->ruleProfile->allowed_conditions ?? [])) }}</div>
                            <div class="text-muted small">Dispositions: {{ implode(', ', array_map(fn ($item) => str_replace('_', ' ', $item), $resource->ruleProfile->allowed_dispositions ?? [])) }}</div>
                        @else
                            <div class="text-muted">No brand rule profile linked yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        $(function () {
            $('#copy-brand-review-link').on('click', async function () {
                const target = document.querySelector($(this).data('copy-target'));
                if (!target) {
                    return;
                }

                target.select();
                target.setSelectionRange(0, target.value.length);

                try {
                    await navigator.clipboard.writeText(target.value);
                    toastr.success('Brand Review Link copied');
                } catch (error) {
                    document.execCommand('copy');
                    toastr.success('Brand Review Link copied');
                }
            });
        });
    </script>
@endpush
