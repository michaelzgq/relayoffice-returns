@extends('layouts.admin.app')

@section('title', 'Decision Queue')

@push('css_or_js')
    <style>
        .queue-card {
            border: 1px solid rgba(55, 125, 255, .12);
            border-radius: 1rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        }

        .queue-card.evidence-risk {
            border-color: rgba(220, 53, 69, .2);
            background: linear-gradient(180deg, #ffffff 0%, #fff8f8 100%);
        }

        .queue-card .queue-meta {
            color: #6c757d;
            font-size: .875rem;
        }

        .queue-card .queue-note {
            background: rgba(55, 125, 255, .06);
            border-radius: .75rem;
            padding: .75rem;
            font-size: .875rem;
        }

        .queue-column {
            min-height: 100%;
        }

        .queue-toolbar-card,
        .queue-filter-card {
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }
    </style>
@endpush

@section('content')
    @php
        $grouped = $resources->getCollection()->groupBy('refund_status');
        $statusLabels = \App\Models\ReturnCase::decisionStatusLabels();
        $queueReadOnly = \App\CPU\Helpers::returns_user_is_guest_demo();
        $statusBadgeMap = [
            'hold' => 'badge-soft-warning',
            'ready_to_release' => 'badge-soft-info',
            'needs_review' => 'badge-soft-danger',
            'released' => 'badge-soft-success',
        ];
    @endphp
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">Decision Queue</h1>
                <p class="text-muted mb-0">Review evidence readiness, move cases forward, and keep the audit trail in one place.</p>
            </div>
        </div>

        <div class="card queue-filter-card mb-3">
            <div class="card-body">
                <form method="get" action="{{ route('admin.returns.queue.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <label class="title">Search</label>
                            <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Return ID, SKU, serial">
                        </div>
                        <div class="col-lg-2">
                            <label class="title">Brand</label>
                            <select class="form-control" name="brand_id">
                                <option value="">All brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="title">Status</label>
                            <select class="form-control" name="filter_status">
                                <option value="">All queue states</option>
                                @foreach(['hold', 'ready_to_release', 'needs_review'] as $status)
                                    <option value="{{ $status }}" {{ request('filter_status', request('refund_status')) === $status ? 'selected' : '' }}>
                                        {{ $statusLabels[$status] ?? str_replace('_', ' ', ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="title">SLA Age</label>
                            <select class="form-control" name="min_sla_hours">
                                <option value="">Any age</option>
                                <option value="24" {{ request('min_sla_hours') == '24' ? 'selected' : '' }}>24h and older</option>
                                <option value="48" {{ request('min_sla_hours') == '48' ? 'selected' : '' }}>48h and older</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group mb-0">
                                <label class="title d-block">Evidence</label>
                                <div class="custom-control custom-checkbox mt-2">
                                    <input class="custom-control-input" type="checkbox" name="evidence_missing" id="queue-evidence-missing" value="1" {{ request()->boolean('evidence_missing') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="queue-evidence-missing">Only missing</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-block" type="submit">Filter</button>
                                <a class="btn btn-light btn-block" href="{{ route('admin.returns.queue.index') }}">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @unless($queueReadOnly)
            <div class="card queue-toolbar-card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                        <div>
                            <h4 class="mb-1">Bulk action</h4>
                            <p class="text-muted mb-0">Select visible cases, then move them together with one note for the audit trail.</p>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <button class="btn btn-outline-secondary" type="button" id="queue-select-all">Select shown</button>
                            <button class="btn btn-outline-secondary" type="button" id="queue-clear-selection">Clear</button>
                            <span class="badge badge-soft-dark" id="queue-selection-count">0 selected</span>
                        </div>
                    </div>

                    <form method="post" action="{{ route('admin.returns.queue.refund-decision') }}" class="mt-3" id="queue-bulk-form">
                        @csrf
                        <div id="queue-selected-case-ids"></div>
                        @if(request()->filled('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request()->filled('brand_id'))
                            <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                        @endif
                        @if(request()->boolean('evidence_missing'))
                            <input type="hidden" name="evidence_missing" value="1">
                        @endif
                        @if(request()->filled('filter_status'))
                            <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                        @endif
                        @if(request()->filled('min_sla_hours'))
                            <input type="hidden" name="min_sla_hours" value="{{ request('min_sla_hours') }}">
                        @endif

                        <div class="row g-3 align-items-end">
                            <div class="col-lg-3">
                                <label class="title">Move selected to</label>
                                <select class="form-control" name="refund_status" required>
                                    <option value="">Choose status</option>
                                    @foreach($refundStatusOptions as $status)
                                        <option value="{{ $status }}">{{ $statusLabels[$status] ?? str_replace('_', ' ', ucfirst($status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-7">
                                <label class="title">Batch note</label>
                                <input class="form-control" type="text" name="decision_note" maxlength="1000" placeholder="Explain why these cases are moving. Required for needs review.">
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-primary btn-block" type="submit" id="queue-bulk-submit" disabled>Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div class="card queue-toolbar-card mb-3">
                <div class="card-body">
                    <h4 class="mb-1">Shared demo mode</h4>
                    <p class="text-muted mb-0">This workspace stays read-only for guests. Open cases, review evidence, and inspect the decision trail without changing queue state.</p>
                </div>
            </div>
        @endunless

        <div class="row g-3">
            @foreach(['hold', 'ready_to_release', 'needs_review'] as $status)
                <div class="col-lg-4">
                    <div class="card queue-column h-100">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $statusLabels[$status] ?? str_replace('_', ' ', ucfirst($status)) }}</h4>
                                <div class="text-muted small">{{ $columnCounts[$status] ?? 0 }} total in filtered queue</div>
                            </div>
                            <span class="badge badge-primary rounded">{{ $grouped->get($status, collect())->count() }}</span>
                        </div>
                        <div class="card-body">
                            @forelse($grouped->get($status, collect()) as $resource)
                                @php
                                    $ageBadge = $resource->sla_age_hours >= 48
                                        ? 'badge-soft-danger'
                                        : ($resource->sla_age_hours >= 24 ? 'badge-soft-warning' : 'badge-soft-success');
                                @endphp
                                <div class="queue-card {{ $resource->evidence_complete ? '' : 'evidence-risk' }} p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="d-flex gap-2">
                                            @unless($queueReadOnly)
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input queue-case-checkbox"
                                                           type="checkbox"
                                                           id="queue-case-{{ $resource->id }}"
                                                           value="{{ $resource->id }}"
                                                           data-return-id="{{ $resource->return_id }}">
                                                    <label class="custom-control-label" for="queue-case-{{ $resource->id }}"></label>
                                                </div>
                                            @endunless
                                            <div>
                                                <div class="font-weight-bold">{{ $resource->return_id }}</div>
                                                <div class="queue-meta">{{ $resource->brand?->name ?? 'N/A' }} | {{ $resource->product_sku ?: 'No SKU' }}</div>
                                            </div>
                                        </div>
                                        <span class="badge {{ $ageBadge }}">{{ $resource->sla_age_hours }}h</span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <span class="badge {{ $statusBadgeMap[$resource->refund_status] ?? 'badge-soft-secondary' }}">
                                            {{ $statusLabels[$resource->refund_status] ?? str_replace('_', ' ', $resource->refund_status) }}
                                        </span>
                                        <span class="badge {{ $resource->evidence_complete ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            Evidence {{ $resource->evidence_photo_count }}/{{ $resource->required_photo_count }}
                                        </span>
                                    </div>

                                    <div class="queue-meta mt-3 text-capitalize">
                                        {{ str_replace('_', ' ', $resource->condition_code) }} / {{ str_replace('_', ' ', $resource->disposition_code) }}
                                    </div>
                                    <div class="queue-meta mt-1">
                                        {{ $resource->serial_number ? 'Serial ' . $resource->serial_number : 'No serial recorded' }}
                                    </div>

                                    @if($resource->refundDecision?->reason)
                                        <div class="queue-note mt-3">
                                            <div class="font-weight-bold mb-1">Latest note</div>
                                            <div>{{ $resource->refundDecision->reason }}</div>
                                        </div>
                                    @endif

                                    @if(!$resource->evidence_complete)
                                        <div class="small text-danger mt-3">
                                            This case cannot move to Ready for brand review or Decision completed until evidence is complete.
                                        </div>
                                    @endif

                                    @if($queueReadOnly)
                                        <div class="mt-3 pt-3 border-top">
                                            <div class="text-muted small mb-2">Recommendation</div>
                                            <div class="font-weight-bold">{{ $statusLabels[$resource->refund_status] ?? str_replace('_', ' ', ucfirst($resource->refund_status)) }}</div>
                                            <div class="text-muted small mt-2">Guest demo users can inspect the evidence trail here, but decision changes stay disabled.</div>
                                            <div class="d-flex gap-2 mt-3">
                                                <a class="btn btn-light btn-sm btn-block" href="{{ route('admin.returns.cases.show', $resource->id) }}">Open case</a>
                                            </div>
                                        </div>
                                    @else
                                        <form method="post" action="{{ route('admin.returns.cases.refund-decision', $resource->id) }}" class="mt-3 pt-3 border-top">
                                            @csrf
                                            <input type="hidden" name="redirect_to" value="queue">
                                            @if(request()->filled('search'))
                                                <input type="hidden" name="search" value="{{ request('search') }}">
                                            @endif
                                            @if(request()->filled('brand_id'))
                                                <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                                            @endif
                                            @if(request()->boolean('evidence_missing'))
                                                <input type="hidden" name="evidence_missing" value="1">
                                            @endif
                                            @if(request()->filled('filter_status'))
                                                <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                                            @endif
                                            @if(request()->filled('min_sla_hours'))
                                                <input type="hidden" name="min_sla_hours" value="{{ request('min_sla_hours') }}">
                                            @endif

                                            <div class="form-group mb-2">
                                                <label class="title">Move to</label>
                                                <select class="form-control form-control-sm" name="refund_status">
                                                    @foreach($refundStatusOptions as $option)
                                                        @php($blocked = !$resource->evidence_complete && in_array($option, ['ready_to_release', 'released'], true))
                                                        <option value="{{ $option }}"
                                                                {{ $resource->refund_status === $option ? 'selected' : '' }}
                                                                {{ $blocked ? 'disabled' : '' }}>
                                                            {{ $statusLabels[$option] ?? str_replace('_', ' ', ucfirst($option)) }}{{ $blocked ? ' - needs evidence' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-2">
                                                <label class="title">Decision note</label>
                                                <input class="form-control form-control-sm"
                                                       type="text"
                                                       name="decision_note"
                                                       maxlength="1000"
                                                       placeholder="Required for needs review">
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm btn-block" type="submit">Update</button>
                                                <a class="btn btn-light btn-sm btn-block" href="{{ route('admin.returns.cases.show', $resource->id) }}">Open case</a>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="text-muted">No cards for this column on the current page.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $resources->links() }}
        </div>
    </div>
@endsection

@push('script_2')
    @unless($queueReadOnly)
    <script>
        "use strict";

        $(function () {
            const $checkboxes = $('.queue-case-checkbox');
            const $selectionCount = $('#queue-selection-count');
            const $bulkSubmit = $('#queue-bulk-submit');
            const $selectedCaseIds = $('#queue-selected-case-ids');
            const $bulkForm = $('#queue-bulk-form');

            function syncSelectionUi() {
                const selected = $checkboxes.filter(':checked');
                $selectionCount.text(`${selected.length} selected`);
                $bulkSubmit.prop('disabled', selected.length === 0);
            }

            function buildBulkHiddenInputs() {
                $selectedCaseIds.empty();

                $checkboxes.filter(':checked').each(function () {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'case_ids[]';
                    input.value = this.value;
                    $selectedCaseIds.append(input);
                });
            }

            $checkboxes.on('change', syncSelectionUi);

            $('#queue-select-all').on('click', function () {
                $checkboxes.prop('checked', true);
                syncSelectionUi();
            });

            $('#queue-clear-selection').on('click', function () {
                $checkboxes.prop('checked', false);
                syncSelectionUi();
            });

            $bulkForm.on('submit', function (event) {
                buildBulkHiddenInputs();

                if ($selectedCaseIds.children().length === 0) {
                    event.preventDefault();
                    toastr.error('Select at least one case before running a batch action.', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            syncSelectionUi();
        });
    </script>
    @endunless
@endpush
