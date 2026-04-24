@extends('layouts.admin.app')

@section('title', \App\CPU\translate('inspect_return'))

@push('css_or_js')
    <style>
        .inspection-option {
            border: 1px solid rgba(55, 125, 255, 0.12);
            border-radius: 12px;
            padding: 14px;
            height: 100%;
            cursor: pointer;
            background: #fff;
        }
        .inspection-option input {
            margin-right: 8px;
        }
        .inspection-page .card {
            border-radius: 16px;
        }
        .inspection-option.is-disabled {
            opacity: .45;
            background: #f8fafd;
            cursor: not-allowed;
        }
        .rule-summary {
            border-radius: 14px;
        }
        .requirement-chip {
            font-weight: 600;
        }
        .recommendation-banner {
            border: 1px dashed rgba(55, 125, 255, 0.24);
            border-radius: 12px;
            background: #f8fbff;
        }
        .inspection-option.is-recommended {
            border-color: rgba(55, 125, 255, 0.45);
            box-shadow: 0 0 0 3px rgba(55, 125, 255, 0.08);
        }
        .inspection-recommendation-pill {
            display: inline-block;
            margin-top: 8px;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(55, 125, 255, 0.12);
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 600;
        }

        .scan-intake {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            margin-bottom: 18px;
            border: 1px solid rgba(55, 125, 255, 0.14);
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(55, 125, 255, 0.06), rgba(0, 201, 219, 0.05));
        }
        .scan-intake-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #1d4ed8;
            margin-bottom: 6px;
        }
        .scan-intake-copy {
            color: #52637a;
            margin: 0;
            max-width: 320px;
            line-height: 1.5;
        }
        .scan-status {
            display: none;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }
        .scan-status.is-visible {
            display: block;
        }
        .scan-status--info {
            background: #eef5ff;
            color: #1d4ed8;
        }
        .scan-status--success {
            background: #ecfdf3;
            color: #0f8a4b;
        }
        .scan-status--warning {
            background: #fff7ed;
            color: #c2410c;
        }
        .scan-input-row {
            display: flex;
            align-items: stretch;
            gap: 10px;
        }
        .scan-input-row .form-control {
            flex: 1 1 auto;
        }
        .scan-action-btn {
            border-radius: 12px;
            white-space: nowrap;
            min-width: 96px;
        }
        .scan-helper {
            font-size: 12px;
            color: #6c7a91;
            margin-top: 8px;
        }
        .scan-sheet {
            position: fixed;
            inset: 0;
            z-index: 2050;
            display: none;
        }
        .scan-sheet.is-open {
            display: block;
        }
        .scan-sheet__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
        }
        .scan-sheet__dialog {
            position: relative;
            max-width: 460px;
            margin: 4vh auto 0;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.32);
            overflow: hidden;
        }
        .scan-sheet__header,
        .scan-sheet__footer {
            padding: 18px 20px;
        }
        .scan-sheet__body {
            padding: 0 20px 20px;
        }
        .scan-sheet__eyebrow {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #1d4ed8;
            margin-bottom: 8px;
        }
        .scan-sheet__copy {
            color: #6b7280;
            margin-bottom: 14px;
        }
        .scan-viewport {
            position: relative;
            border-radius: 18px;
            background: #0f172a;
            min-height: 360px;
            overflow: hidden;
        }
        .scan-reader {
            width: 100%;
            min-height: 360px;
        }
        .scan-reader,
        .scan-reader > div {
            height: 100%;
        }
        .scan-viewport video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .scan-reader video,
        .scan-reader canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            border-radius: 18px;
        }
        .scan-reader__empty {
            min-height: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.72);
            font-size: 14px;
            text-align: center;
            padding: 24px;
        }
        .scan-viewport::after {
            content: '';
            position: absolute;
            inset: 15% 10%;
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 18px;
            box-shadow: 0 0 0 999px rgba(15, 23, 42, 0.18);
            pointer-events: none;
        }
        .scan-manual-help {
            margin-top: 12px;
            font-size: 12px;
            color: #6b7280;
        }
        .scan-target-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(55, 125, 255, 0.12);
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            margin-left: 6px;
        }
        @media (max-width: 767.98px) {
            .scan-intake,
            .scan-input-row {
                flex-direction: column;
            }
            .scan-action-btn {
                width: 100%;
            }
            .scan-sheet__dialog {
                margin: 0;
                min-height: 100vh;
                border-radius: 0;
            }
            .scan-viewport {
                min-height: 50vh;
            }
        }
    </style>
@endpush

@section('content')
    @php($selectedBrandId = (int) old('brand_id', $currentCase?->brand_id ?? $expectedInbound?->brand_id))
    @php($initialProfile = $selectedBrandId ? $profiles->get($selectedBrandId) : null)
    @php($canManageRefundGate = \App\CPU\Helpers::admin_has_module('returns_queue_section'))
    @php($inspectorView = \App\CPU\Helpers::returns_user_is_inspector())
    @php($initialCondition = old('condition_code', $currentCase?->condition_code ?? $expectedInbound?->expected_condition))
    @php($initialOfflineUuid = old('offline_draft_uuid', $currentCase?->offline_draft_uuid ?? (string) \Illuminate\Support\Str::uuid()))
    <div class="content container-fluid inspection-page">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h1 class="page-header-title mb-0">{{ $currentCase ? 'Update Inspection' : 'Inspect Return' }}</h1>
                <p class="text-muted mb-0">
                    {{ $inspectorView
                        ? 'Five quick steps: identify the return, pick the playbook, mark condition, choose warehouse action, and upload proof.'
                        : 'Capture evidence, mark item condition, and control the refund outcome from one page.' }}
                </p>
            </div>
            <a href="{{ route('admin.returns.cases.index') }}" class="btn btn-light">{{ $inspectorView ? 'Back to my cases' : 'Back to cases' }}</a>
        </div>

        @if($expectedInbound)
            <div class="alert alert-soft-info mb-3">
                <div class="font-weight-bold mb-1">Expected inbound matched</div>
                <div class="small mb-0">
                    {{ $expectedInbound->return_id }} from {{ $expectedInbound->brand?->name ?? 'N/A' }}.
                    Expected SKU {{ $expectedInbound->product_sku ?: 'N/A' }},
                    serial {{ $expectedInbound->serial_number ?: 'N/A' }},
                    condition {{ $expectedInbound->expected_condition ? str_replace('_', ' ', $expectedInbound->expected_condition) : 'N/A' }}.
                </div>
            </div>
        @endif

        <form method="post" action="{{ route('admin.returns.inspect.store') }}" enctype="multipart/form-data" data-local-draft-key="dossentry-inspection-draft-v1">
            @csrf
            @if($currentCase)
                <input type="hidden" name="case_id" value="{{ $currentCase->id }}">
            @endif
            @if($expectedInbound)
                <input type="hidden" name="expected_inbound_id" value="{{ $expectedInbound->id }}">
            @endif
            <input type="hidden" id="offline-draft-uuid-input" name="offline_draft_uuid" value="{{ $initialOfflineUuid }}">

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header border-0">
                            <h4 class="mb-0">1. Return Reference</h4>
                        </div>
                        <div class="card-body">
                            <div class="scan-intake">
                                <div>
                                    <div class="scan-intake-label">Fastest path</div>
                                    <p class="scan-intake-copy">Scan the return label first. Dossentry will try to pull the return ID, SKU / barcode, and serial in one pass before you touch the keyboard.</p>
                                </div>
                                <button class="btn btn-primary scan-action-btn" type="button" data-scan-mode="label">Scan return label</button>
                            </div>

                            <div class="scan-status scan-status--info" id="scan-status-banner" role="status" aria-live="polite"></div>

                            <div class="form-group">
                                <label class="title">Return ID</label>
                                <div class="scan-input-row">
                                    <input class="form-control form-control-lg" id="return-id-input" type="text" name="return_id" value="{{ old('return_id', $currentCase?->return_id ?? $expectedInbound?->return_id) }}" placeholder="Scan or type return ID" required>
                                    <button class="btn btn-outline-primary scan-action-btn" type="button" data-scan-mode="field" data-scan-target="return_id">Scan</button>
                                </div>
                                <div class="scan-helper">Works with camera scan, USB/Bluetooth barcode scanners, or manual typing.</div>
                            </div>
                            <div class="form-group">
                                <label class="title">Client / Brand</label>
                                <select class="form-control form-control-lg" id="brand-select-input" name="brand_id" required>
                                    <option value="">Choose the client playbook</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ (string) old('brand_id', $currentCase?->brand_id ?? $expectedInbound?->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="title">SKU / Barcode <span class="text-danger {{ $initialProfile?->sku_required ? '' : 'd-none' }}" id="sku-required-indicator">*</span></label>
                                <div class="scan-input-row">
                                    <input class="form-control form-control-lg" id="product-sku-input" type="text" name="product_sku" value="{{ old('product_sku', $currentCase?->product_sku ?? $expectedInbound?->product_sku) }}" placeholder="Scan or type SKU">
                                    <button class="btn btn-outline-primary scan-action-btn" type="button" data-scan-mode="field" data-scan-target="product_sku">Scan</button>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="title">Serial Number <span class="text-danger {{ $initialProfile?->serial_required ? '' : 'd-none' }}" id="serial-required-indicator">*</span></label>
                                <div class="scan-input-row">
                                    <input class="form-control form-control-lg" id="serial-number-input" type="text" name="serial_number" value="{{ old('serial_number', $currentCase?->serial_number ?? $expectedInbound?->serial_number) }}" placeholder="Only if this playbook requires it">
                                    <button class="btn btn-outline-primary scan-action-btn" type="button" data-scan-mode="field" data-scan-target="serial_number">Scan</button>
                                </div>
                            </div>

                            <div class="alert alert-soft-primary mt-3 mb-0 rule-summary" id="rule-summary"
                                 data-profiles='@json($profilesForJs)'
                                 data-existing-evidence="{{ $currentCase?->media?->count() ?? 0 }}">
                                <div class="font-weight-bold mb-2" id="rule-profile-name">
                                    {{ $initialProfile?->profile_name ?? 'Choose a client playbook first.' }}
                                </div>
                                <div class="small text-muted mb-3" id="rule-profile-status">
                                    {{ $initialProfile ? 'This playbook decides what proof is required and which decision state will be used by default.' : 'The form will load the right required fields after you choose a client.' }}
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge badge-soft-primary requirement-chip" id="rule-photo-count">
                                        {{ $initialProfile ? $initialProfile->required_photo_count . ' evidence photo(s) required' : 'Evidence requirement pending' }}
                                    </span>
                                    <span class="badge badge-soft-info requirement-chip" id="rule-default-refund">
                                        {{ $initialProfile ? 'Default decision state: ' . \App\Models\ReturnCase::decisionStatusLabel($initialProfile->default_refund_status) : 'Decision default pending' }}
                                    </span>
                                    <span class="badge badge-soft-dark requirement-chip" id="rule-version">
                                        {{ $initialProfile ? 'Rule v' . ($initialProfile->rule_version ?? 1) : 'Rule version pending' }}
                                    </span>
                                </div>

                                <div class="small mb-2" id="rule-recommended-actions">
                                    Recommended warehouse actions:
                                    {{ $initialProfile && !empty($initialProfile->recommended_dispositions) ? collect($initialProfile->recommended_dispositions)->map(fn ($item, $condition) => str_replace('_', ' ', $condition) . ' -> ' . str_replace('_', ' ', $item))->implode(', ') : 'Select a brand first' }}
                                </div>
                                <div class="small mb-2" id="rule-required-fields">
                                    Required fields:
                                    {{ $initialProfile ? collect([
                                        $initialProfile->sku_required ? 'SKU' : null,
                                        $initialProfile->serial_required ? 'Serial number' : null,
                                        $initialProfile->notes_required ? 'Notes' : null,
                                    ])->filter()->values()->implode(', ') ?: 'No extra fields required' : 'Select a brand first' }}
                                </div>
                                <div class="small mb-2" id="rule-photo-types">
                                    Required photo types:
                                    {{ $initialProfile && !empty($initialProfile->required_photo_types) ? collect($initialProfile->required_photo_types)->map(fn ($item) => str_replace('_', ' ', $item))->implode(', ') : 'Select a brand first' }}
                                </div>
                                <div class="small mb-0" id="rule-allowed-values">
                                    Allowed condition / disposition:
                                    {{ $initialProfile ? collect($initialProfile->allowed_conditions ?? [])->map(fn ($item) => str_replace('_', ' ', $item))->implode(', ') . ' / ' . collect($initialProfile->allowed_dispositions ?? [])->map(fn ($item) => str_replace('_', ' ', $item))->implode(', ') : 'Select a brand first' }}
                                </div>
                                <div class="small mt-2" id="rule-auto-hold-triggers">
                                    Auto-hold triggers:
                                    {{ $initialProfile && !empty($initialProfile->auto_hold_triggers) ? collect($initialProfile->auto_hold_triggers)->map(fn ($item) => \App\Models\BrandRuleProfile::autoHoldTriggerOptions()[$item] ?? str_replace('_', ' ', $item))->implode(', ') : 'No auto-hold triggers set' }}
                                </div>
                                <div class="small mt-2" id="rule-reviewer-template">
                                    Reviewer note template:
                                    {{ $initialProfile?->reviewer_note_template ?: 'No template set' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header border-0">
                            <h4 class="mb-0">2. What Came Back?</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach($conditionOptions as $condition)
                                    <div class="col-sm-6 col-xl-3">
                                        <label class="inspection-option d-flex align-items-center condition-option" data-value="{{ $condition }}">
                                            <input class="condition-input" type="radio" name="condition_code" value="{{ $condition }}" {{ $initialCondition === $condition ? 'checked' : '' }} required>
                                            <span class="text-capitalize">{{ str_replace('_', ' ', $condition) }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header border-0">
                            <h4 class="mb-0">3. What Should Warehouse Do?</h4>
                        </div>
                        <div class="card-body">
                            <div class="recommendation-banner p-3 mb-3">
                                <div class="font-weight-bold mb-1" id="recommendation-title">
                                    {{ $initialProfile?->recommendedDispositionForCondition($initialCondition) ? 'Recommended action ready' : 'Choose item condition first' }}
                                </div>
                                <div class="small text-muted mb-0" id="recommendation-copy">
                                    @php($initialRecommendedDisposition = $initialProfile?->recommendedDispositionForCondition($initialCondition))
                                    {{ $initialRecommendedDisposition
                                        ? 'Playbook suggests ' . str_replace('_', ' ', $initialRecommendedDisposition) . ' for this condition. Inspectors can submit with the default or choose another allowed action.'
                                        : 'The playbook will suggest a warehouse action after you select the return condition.' }}
                                </div>
                            </div>
                            <div class="row g-2">
                                @foreach($dispositionOptions as $disposition)
                                    <div class="col-sm-6 col-xl-4">
                                        <label class="inspection-option d-flex align-items-center disposition-option" data-value="{{ $disposition }}">
                                            <input class="disposition-input" type="radio" name="disposition_code" value="{{ $disposition }}" {{ old('disposition_code', $currentCase?->disposition_code) === $disposition ? 'checked' : '' }} required>
                                            <span>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $disposition) }}</span>
                                                <span class="inspection-recommendation-pill d-none">Playbook default</span>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="mb-0">{{ $canManageRefundGate ? '4. Notes + Ops Controls' : '4. Notes' }}</h4>
                        </div>
                        <div class="card-body">
                            @if($canManageRefundGate)
                                <div class="form-group">
                                    <label class="title">Decision state</label>
                                    <select class="form-control form-control-lg" id="refund-status-input" name="refund_status">
                                        @foreach($refundStatusOptions as $status)
                                            <option value="{{ $status }}" {{ old('refund_status', $currentCase?->refund_status ?? 'hold') === $status ? 'selected' : '' }}>
                                                {{ \App\Models\ReturnCase::decisionStatusLabel($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="title">Received At</label>
                                    <input class="form-control form-control-lg" type="datetime-local" name="received_at" value="{{ old('received_at', optional($currentCase?->received_at)->format('Y-m-d\\TH:i')) }}">
                                </div>
                            @else
                                <div class="alert alert-soft-primary mb-3">
                                    <div class="font-weight-bold mb-1">Decision state will be set automatically</div>
                                    <div class="small mb-0">Inspectors only capture facts here. The selected playbook decides the default decision state, and ops can review it later in Queue.</div>
                                </div>
                            @endif
                            <div class="form-group mb-0">
                                <label class="title">Notes <span class="text-danger {{ $initialProfile?->notes_required ? '' : 'd-none' }}" id="notes-required-indicator">*</span></label>
                                <textarea class="form-control" id="notes-input" name="notes" rows="5" placeholder="Missing charger, seal broken, front panel scratched...">{{ old('notes', $currentCase?->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="mb-0">5. Required Photos</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="title">Photos</label>
                                <input class="form-control form-control-lg" type="file" name="photos[]" multiple accept="image/*">
                                <small class="text-muted d-block mt-2" id="photo-requirement-help">
                                    @if($initialProfile)
                                        Need at least {{ $initialProfile->required_photo_count }} photo(s). Existing evidence: {{ $currentCase?->media?->count() ?? 0 }}.
                                        Required captures:
                                        {{ !empty($initialProfile->required_photo_types) ? collect($initialProfile->required_photo_types)->map(fn ($item) => str_replace('_', ' ', $item))->implode(', ') : 'No specific capture types' }}.
                                    @else
                                        Select a brand to load evidence requirements.
                                    @endif
                                </small>
                            </div>
                            @if($currentCase && $currentCase->media->isNotEmpty())
                                <div class="border-top pt-3">
                                    <h5 class="mb-3">Existing evidence</h5>
                                    @include('admin-views.returns.partials._evidence_grid', ['resource' => $currentCase])
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a class="btn btn-light" href="{{ route('admin.returns.cases.index') }}">Cancel</a>
                        <button class="btn btn-outline-secondary btn-lg" type="button" id="save-local-draft-button">Save local draft</button>
                        <button class="btn btn-light btn-lg" type="button" id="restore-local-draft-button">Restore local draft</button>
                        <button class="btn btn-outline-primary btn-lg" type="submit" name="save_as_draft" value="1" formnovalidate>Save server draft</button>
                        <button class="btn btn-primary btn-lg" type="submit">{{ $currentCase ? 'Update inspection' : 'Submit inspection' }}</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="scan-sheet d-none" id="scan-sheet" aria-hidden="true">
            <div class="scan-sheet__backdrop" data-scan-close></div>
            <div class="scan-sheet__dialog" role="dialog" aria-modal="true" aria-labelledby="scan-sheet-title">
                <div class="scan-sheet__header">
                    <div class="scan-sheet__eyebrow">Warehouse scan</div>
                    <h3 class="mb-1" id="scan-sheet-title">Scan return label</h3>
                    <div class="small text-muted" id="scan-sheet-subtitle">Point the camera at a QR code or barcode.</div>
                </div>
                <div class="scan-sheet__body">
                    <div class="scan-viewport" id="scan-viewport">
                        <div class="scan-reader d-none" id="scan-reader">
                            <div class="scan-reader__empty">Opening camera…</div>
                        </div>
                        <video class="d-none" id="scan-video" playsinline muted></video>
                    </div>
                    <div class="scan-manual-help" id="scan-manual-help">Tip: if live camera scan is unavailable on this browser, use a label photo or a USB / Bluetooth scanner instead.</div>
                </div>
                <input class="d-none" id="scan-file-input" type="file" accept="image/*" capture="environment">
                <div class="scan-sheet__footer d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <div class="small text-muted">Target <span class="scan-target-pill" id="scan-target-pill">Return label</span></div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary" id="scan-file-button" type="button">Use camera photo</button>
                        <button class="btn btn-light" type="button" data-scan-close>Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endpush

@push('script_2')
    <script>
        "use strict";

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.inspection-page form');
            const summary = document.getElementById('rule-summary');

            if (!form || !summary) {
                return;
            }

            const profiles = JSON.parse(summary.dataset.profiles || '{}');
            const existingEvidence = Number(summary.dataset.existingEvidence || 0);
            const isEditMode = Boolean(form.querySelector('[name="case_id"]'));

            const fieldInputs = {
                return_id: document.getElementById('return-id-input'),
                product_sku: document.getElementById('product-sku-input'),
                serial_number: document.getElementById('serial-number-input'),
            };

            const brandSelect = document.getElementById('brand-select-input');
            const refundSelect = document.getElementById('refund-status-input');
            const skuInput = fieldInputs.product_sku;
            const serialInput = fieldInputs.serial_number;
            const notesInput = document.getElementById('notes-input');

            const skuRequiredIndicator = document.getElementById('sku-required-indicator');
            const serialRequiredIndicator = document.getElementById('serial-required-indicator');
            const notesRequiredIndicator = document.getElementById('notes-required-indicator');

            const ruleProfileName = document.getElementById('rule-profile-name');
            const ruleProfileStatus = document.getElementById('rule-profile-status');
            const rulePhotoCount = document.getElementById('rule-photo-count');
            const ruleDefaultRefund = document.getElementById('rule-default-refund');
            const ruleVersion = document.getElementById('rule-version');
            const ruleRecommendedActions = document.getElementById('rule-recommended-actions');
            const ruleRequiredFields = document.getElementById('rule-required-fields');
            const rulePhotoTypes = document.getElementById('rule-photo-types');
            const ruleAllowedValues = document.getElementById('rule-allowed-values');
            const ruleAutoHoldTriggers = document.getElementById('rule-auto-hold-triggers');
            const ruleReviewerTemplate = document.getElementById('rule-reviewer-template');
            const photoRequirementHelp = document.getElementById('photo-requirement-help');
            const recommendationTitle = document.getElementById('recommendation-title');
            const recommendationCopy = document.getElementById('recommendation-copy');
            const conditionInputs = Array.from(form.querySelectorAll('.condition-input'));
            const dispositionInputs = Array.from(form.querySelectorAll('.disposition-input'));
            const offlineDraftUuidInput = document.getElementById('offline-draft-uuid-input');
            const saveLocalDraftButton = document.getElementById('save-local-draft-button');
            const restoreLocalDraftButton = document.getElementById('restore-local-draft-button');
            const scanStatusBanner = document.getElementById('scan-status-banner');
            const scanSheet = document.getElementById('scan-sheet');
            const scanReader = document.getElementById('scan-reader');
            const scanVideo = document.getElementById('scan-video');
            const scanTitle = document.getElementById('scan-sheet-title');
            const scanSubtitle = document.getElementById('scan-sheet-subtitle');
            const scanTargetPill = document.getElementById('scan-target-pill');
            const scanManualHelp = document.getElementById('scan-manual-help');
            const scanFileButton = document.getElementById('scan-file-button');
            const scanFileInput = document.getElementById('scan-file-input');
            const scanButtons = Array.from(document.querySelectorAll('[data-scan-mode]'));
            const scanClosers = Array.from(document.querySelectorAll('[data-scan-close]'));

            const statusLabels = @json(\App\Models\ReturnCase::decisionStatusLabels());
            const autoHoldTriggerLabels = @json(\App\Models\BrandRuleProfile::autoHoldTriggerOptions());
            const humanize = (value) => statusLabels[value] || String(value || '').replaceAll('_', ' ');
            const humanizeList = (values) => (values || []).map((value) => humanize(value)).join(', ');
            const mapToHumanList = (mapping) => Object.entries(mapping || {}).map(([condition, disposition]) => `${humanize(condition)} -> ${humanize(disposition)}`).join(', ');
            const triggerList = (values) => (values || []).map((value) => autoHoldTriggerLabels[value] || humanize(value)).join(', ');

            const scanState = {
                active: false,
                mode: 'label',
                target: 'return_id',
                stream: null,
                detector: null,
                rafId: null,
                lastRawValue: null,
                engine: null,
                html5Qrcode: null,
            };

            const supportedHtml5Formats = (() => {
                if (!window.Html5QrcodeSupportedFormats) {
                    return [];
                }

                return [
                    window.Html5QrcodeSupportedFormats.QR_CODE,
                    window.Html5QrcodeSupportedFormats.CODE_128,
                    window.Html5QrcodeSupportedFormats.CODE_39,
                    window.Html5QrcodeSupportedFormats.EAN_13,
                    window.Html5QrcodeSupportedFormats.EAN_8,
                    window.Html5QrcodeSupportedFormats.UPC_A,
                    window.Html5QrcodeSupportedFormats.UPC_E,
                    window.Html5QrcodeSupportedFormats.ITF,
                    window.Html5QrcodeSupportedFormats.CODABAR,
                ].filter(Boolean);
            })();

            const setRequiredState = (input, indicator, required) => {
                if (!input || !indicator) {
                    return;
                }

                input.required = required;
                indicator.classList.toggle('d-none', !required);
            };

            const setScanStatus = (message, tone = 'info') => {
                if (!scanStatusBanner) {
                    return;
                }

                scanStatusBanner.textContent = message;
                scanStatusBanner.className = `scan-status scan-status--${tone} is-visible`;
            };

            const openScanSheet = (mode, target) => {
                scanTitle.textContent = mode === 'label' ? 'Scan return label' : 'Scan field value';
                scanSubtitle.textContent = mode === 'label'
                    ? 'Point the camera at the return label. Structured QR codes will auto-fill multiple fields.'
                    : 'Point the camera at the barcode or QR code for this field.';
                scanTargetPill.textContent = mode === 'label' ? 'Return label' : humanize(target);
                scanManualHelp.textContent = mode === 'label'
                    ? 'If live camera scan does not start on this phone, tap "Use camera photo" and capture the label instead.'
                    : 'If live camera scan does not start on this phone, tap "Use camera photo" and capture the barcode or QR code instead.';
                scanSheet.classList.remove('d-none');
                scanSheet.classList.add('is-open');
                scanSheet.setAttribute('aria-hidden', 'false');
            };

            const hideScanSheet = () => {
                scanSheet.classList.add('d-none');
                scanSheet.classList.remove('is-open');
                scanSheet.setAttribute('aria-hidden', 'true');
            };

            const resetScanViewport = () => {
                if (scanReader) {
                    scanReader.classList.add('d-none');
                    scanReader.innerHTML = '<div class="scan-reader__empty">Opening camera…</div>';
                }

                if (scanVideo) {
                    scanVideo.classList.add('d-none');
                    scanVideo.pause();
                    scanVideo.srcObject = null;
                }
            };

            const syncOptionGroup = (selector, allowedValues) => {
                const allowAll = !Array.isArray(allowedValues) || allowedValues.length === 0;

                form.querySelectorAll(selector).forEach((optionLabel) => {
                    const input = optionLabel.querySelector('input');
                    const isAllowed = allowAll || allowedValues.includes(input.value);

                    optionLabel.classList.toggle('is-disabled', !isAllowed);
                    input.disabled = !isAllowed;

                    if (!isAllowed && input.checked) {
                        input.checked = false;
                    }
                });
            };

            const syncRecommendedDisposition = (profile, resetTouched = false) => {
                if (resetTouched) {
                    dispositionInputs.forEach((input) => {
                        delete input.dataset.userTouched;
                    });
                }

                const selectedCondition = conditionInputs.find((input) => input.checked)?.value;
                const recommendedDisposition = selectedCondition
                    ? (profile?.recommended_dispositions || {})[selectedCondition]
                    : null;

                form.querySelectorAll('.disposition-option').forEach((optionLabel) => {
                    const input = optionLabel.querySelector('input');
                    const pill = optionLabel.querySelector('.inspection-recommendation-pill');
                    const isRecommended = Boolean(recommendedDisposition) && input.value === recommendedDisposition;

                    optionLabel.classList.toggle('is-recommended', isRecommended);
                    pill.classList.toggle('d-none', !isRecommended);
                });

                if (!selectedCondition) {
                    recommendationTitle.textContent = 'Choose item condition first';
                    recommendationCopy.textContent = 'The playbook will suggest a warehouse action after you select the return condition.';
                    return;
                }

                if (!recommendedDisposition) {
                    recommendationTitle.textContent = 'No default action for this condition';
                    recommendationCopy.textContent = 'This playbook allows warehouse staff to choose any permitted action for the selected condition.';
                    return;
                }

                recommendationTitle.textContent = 'Recommended action ready';
                recommendationCopy.textContent = `Playbook suggests ${humanize(recommendedDisposition)} for ${humanize(selectedCondition)}. You can submit with the default or choose another allowed action.`;

                const currentSelection = dispositionInputs.find((input) => input.checked);
                const userTouched = Boolean(dispositionInputs.find((input) => input.dataset.userTouched === '1'));
                const shouldAutoSelect = !userTouched || !currentSelection;

                if (shouldAutoSelect) {
                    const recommendedInput = dispositionInputs.find((input) => input.value === recommendedDisposition && !input.disabled);
                    if (recommendedInput) {
                        recommendedInput.checked = true;
                    }
                }
            };

            const syncRuleProfile = () => {
                const profile = profiles[brandSelect.value];

                if (!profile) {
                    summary.className = 'alert alert-soft-warning mt-3 mb-0 rule-summary';
                    ruleProfileName.textContent = 'No active rule profile found for this brand.';
                    ruleProfileStatus.textContent = 'Create or activate a client playbook before submitting inspections.';
                    rulePhotoCount.textContent = 'Evidence requirement unavailable';
                    ruleDefaultRefund.textContent = 'Decision default unavailable';
                    ruleVersion.textContent = 'Rule version unavailable';
                    ruleRecommendedActions.textContent = 'Recommended warehouse actions: brand rule missing';
                    ruleRequiredFields.textContent = 'Required fields: brand rule missing';
                    rulePhotoTypes.textContent = 'Required photo types: brand rule missing';
                    ruleAllowedValues.textContent = 'Allowed condition / disposition: brand rule missing';
                    ruleAutoHoldTriggers.textContent = 'Auto-hold triggers: brand rule missing';
                    ruleReviewerTemplate.textContent = 'Reviewer note template: brand rule missing';
                    photoRequirementHelp.textContent = 'This brand cannot be submitted until an active rule profile exists.';

                    setRequiredState(skuInput, skuRequiredIndicator, false);
                    setRequiredState(serialInput, serialRequiredIndicator, false);
                    setRequiredState(notesInput, notesRequiredIndicator, false);
                    syncOptionGroup('.condition-option', []);
                    syncOptionGroup('.disposition-option', []);
                    syncRecommendedDisposition(null, true);
                    return;
                }

                summary.className = 'alert alert-soft-primary mt-3 mb-0 rule-summary';
                ruleProfileName.textContent = profile.profile_name;
                ruleProfileStatus.textContent = 'This playbook controls what proof is required and which decision state is used by default.';
                rulePhotoCount.textContent = `${profile.required_photo_count} evidence photo(s) required`;
                ruleDefaultRefund.textContent = `Default decision state: ${humanize(profile.default_refund_status)}`;
                ruleVersion.textContent = `Rule v${profile.rule_version || 1}`;
                ruleRecommendedActions.textContent = `Recommended warehouse actions: ${mapToHumanList(profile.recommended_dispositions || {}) || 'No default actions'}`;

                const requiredFields = [];
                if (profile.sku_required) requiredFields.push('SKU');
                if (profile.serial_required) requiredFields.push('Serial number');
                if (profile.notes_required) requiredFields.push('Notes');

                ruleRequiredFields.textContent = `Required fields: ${requiredFields.length ? requiredFields.join(', ') : 'No extra fields required'}`;
                rulePhotoTypes.textContent = `Required photo types: ${profile.required_photo_types.length ? humanizeList(profile.required_photo_types) : 'No specific capture types'}`;
                ruleAllowedValues.textContent = `Allowed condition / disposition: ${humanizeList(profile.allowed_conditions)} / ${humanizeList(profile.allowed_dispositions)}`;
                ruleAutoHoldTriggers.textContent = `Auto-hold triggers: ${triggerList(profile.auto_hold_triggers || []) || 'No auto-hold triggers set'}`;
                ruleReviewerTemplate.textContent = `Reviewer note template: ${profile.reviewer_note_template || 'No template set'}`;
                photoRequirementHelp.textContent = `Need at least ${profile.required_photo_count} photo(s). Existing evidence: ${existingEvidence}. Required captures: ${profile.required_photo_types.length ? humanizeList(profile.required_photo_types) : 'No specific capture types'}.`;

                setRequiredState(skuInput, skuRequiredIndicator, Boolean(profile.sku_required));
                setRequiredState(serialInput, serialRequiredIndicator, Boolean(profile.serial_required));
                setRequiredState(notesInput, notesRequiredIndicator, Boolean(profile.notes_required));

                syncOptionGroup('.condition-option', profile.allowed_conditions || []);
                syncOptionGroup('.disposition-option', profile.allowed_dispositions || []);
                syncRecommendedDisposition(profile, true);

                if (refundSelect && !isEditMode && !refundSelect.dataset.userTouched) {
                    refundSelect.value = profile.default_refund_status || 'hold';
                }
            };

            const normalizeKey = (key) => String(key || '')
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');

            const parseUrlPayload = (rawValue) => {
                try {
                    const url = new URL(rawValue);
                    const payload = {};
                    url.searchParams.forEach((value, key) => {
                        payload[key] = value;
                    });
                    return Object.keys(payload).length ? payload : null;
                } catch (error) {
                    return null;
                }
            };

            const parseDelimitedPayload = (rawValue) => {
                const payload = {};
                String(rawValue || '').split(/[\n;|]+/).forEach((segment) => {
                    const trimmed = segment.trim();
                    if (!trimmed) {
                        return;
                    }

                    const match = trimmed.match(/^([^:=]+)\s*[:=]\s*(.+)$/);
                    if (match) {
                        payload[match[1].trim()] = match[2].trim();
                    }
                });
                return Object.keys(payload).length ? payload : null;
            };

            const normalizePayload = (rawValue) => {
                const trimmed = String(rawValue || '').trim();
                if (!trimmed) {
                    return null;
                }

                const candidates = [];
                try {
                    const json = JSON.parse(trimmed);
                    if (json && typeof json === 'object' && !Array.isArray(json)) {
                        candidates.push(json);
                    }
                } catch (error) {
                    // no-op
                }

                const urlPayload = parseUrlPayload(trimmed);
                if (urlPayload) {
                    candidates.push(urlPayload);
                }

                const delimitedPayload = parseDelimitedPayload(trimmed);
                if (delimitedPayload) {
                    candidates.push(delimitedPayload);
                }

                const normalized = {};
                for (const payload of candidates) {
                    Object.entries(payload).forEach(([key, value]) => {
                        const normalizedKey = normalizeKey(key);
                        if (!normalizedKey || value === null || value === undefined || value === '') {
                            return;
                        }

                        if (['return_id', 'returnid', 'return_reference', 'rma', 'rma_id'].includes(normalizedKey)) {
                            normalized.return_id = String(value).trim();
                        }

                        if (['sku', 'barcode', 'product_sku', 'productsku', 'item_sku'].includes(normalizedKey)) {
                            normalized.product_sku = String(value).trim();
                        }

                        if (['serial', 'serial_number', 'serialnumber', 'sn'].includes(normalizedKey)) {
                            normalized.serial_number = String(value).trim();
                        }

                        if (['brand', 'client', 'merchant'].includes(normalizedKey)) {
                            normalized.brand_label = String(value).trim();
                        }

                        if (['brand_id', 'client_id', 'merchant_id'].includes(normalizedKey)) {
                            normalized.brand_id = String(value).trim();
                        }
                    });
                }

                return Object.keys(normalized).length ? normalized : null;
            };

            const applyBrandSelection = (parsedPayload) => {
                if (!parsedPayload) {
                    return false;
                }

                let matched = false;
                const normalizedBrandLabel = String(parsedPayload.brand_label || '').trim().toLowerCase();
                const normalizedBrandId = String(parsedPayload.brand_id || '').trim();

                Array.from(brandSelect.options).forEach((option) => {
                    const optionLabel = option.textContent.trim().toLowerCase();
                    const matchesId = normalizedBrandId !== '' && option.value === normalizedBrandId;
                    const matchesLabel = normalizedBrandLabel !== '' && optionLabel === normalizedBrandLabel;

                    if (!matched && (matchesId || matchesLabel)) {
                        brandSelect.value = option.value;
                        matched = true;
                    }
                });

                if (matched) {
                    syncRuleProfile();
                }

                return matched;
            };

            const focusField = (target) => {
                const input = fieldInputs[target] || fieldInputs.return_id;
                input?.focus();
            };

            const applyRawFieldValue = (target, rawValue) => {
                const input = fieldInputs[target] || fieldInputs.return_id;
                if (!input) {
                    return;
                }
                input.value = String(rawValue || '').trim();
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                focusField(target);
            };

            const applyParsedPayload = (rawValue) => {
                const parsedPayload = normalizePayload(rawValue);
                if (!parsedPayload) {
                    return false;
                }

                if (parsedPayload.return_id) {
                    applyRawFieldValue('return_id', parsedPayload.return_id);
                }
                if (parsedPayload.product_sku) {
                    applyRawFieldValue('product_sku', parsedPayload.product_sku);
                }
                if (parsedPayload.serial_number) {
                    applyRawFieldValue('serial_number', parsedPayload.serial_number);
                }

                const matchedBrand = applyBrandSelection(parsedPayload);
                setScanStatus(`Label scanned. Filled ${[
                    parsedPayload.return_id ? 'return ID' : null,
                    parsedPayload.product_sku ? 'SKU / barcode' : null,
                    parsedPayload.serial_number ? 'serial' : null,
                    matchedBrand ? 'brand' : null,
                ].filter(Boolean).join(', ')}.`, 'success');
                focusField(parsedPayload.serial_number ? 'serial_number' : (parsedPayload.product_sku ? 'product_sku' : 'return_id'));
                return true;
            };

            const stopNativeScanner = () => {
                if (scanState.rafId) {
                    cancelAnimationFrame(scanState.rafId);
                    scanState.rafId = null;
                }

                if (scanState.stream) {
                    scanState.stream.getTracks().forEach((track) => track.stop());
                    scanState.stream = null;
                }

                scanState.detector = null;
            };

            const stopHtml5Scanner = async () => {
                const html5Qrcode = scanState.html5Qrcode;
                scanState.html5Qrcode = null;

                if (!html5Qrcode) {
                    return;
                }

                try {
                    await html5Qrcode.stop();
                } catch (error) {
                    // Ignore stop failures when the scanner never reached an active state.
                }

                try {
                    await html5Qrcode.clear();
                } catch (error) {
                    // Ignore clear failures from partially initialized scanners.
                }
            };

            const stopScannerSession = async () => {
                scanState.active = false;
                scanState.engine = null;

                stopNativeScanner();
                await stopHtml5Scanner();
                resetScanViewport();
            };

            const closeScanner = async () => {
                await stopScannerSession();
                hideScanSheet();
            };

            const handleDetectedCode = async (rawValue) => {
                if (!rawValue || rawValue === scanState.lastRawValue) {
                    return;
                }

                scanState.lastRawValue = rawValue;
                await closeScanner();

                if (scanState.mode === 'label') {
                    if (applyParsedPayload(rawValue)) {
                        return;
                    }

                    applyRawFieldValue('return_id', rawValue);
                    setScanStatus('Scanned one code. Added it to Return ID because the label did not include structured fields.', 'warning');
                    return;
                }

                applyRawFieldValue(scanState.target, rawValue);
                setScanStatus(`${String(scanState.target).replace('_', ' ')} captured from scanner.`, 'success');
            };

            const scanLoop = async () => {
                if (!scanState.active || !scanState.detector || !scanVideo || scanVideo.readyState < 2) {
                    scanState.rafId = requestAnimationFrame(scanLoop);
                    return;
                }

                try {
                    const barcodes = await scanState.detector.detect(scanVideo);
                    if (barcodes.length > 0) {
                        const rawValue = barcodes.find((item) => item.rawValue)?.rawValue;
                        if (rawValue) {
                            void handleDetectedCode(rawValue);
                            return;
                        }
                    }
                } catch (error) {
                    setScanStatus('Live camera scanning failed on this browser. Use "Use camera photo" or scan into the active field with a hardware scanner.', 'warning');
                    void closeScanner();
                    return;
                }

                scanState.rafId = requestAnimationFrame(scanLoop);
            };

            const getSupportedFormats = async () => {
                const fallbackFormats = ['qr_code', 'code_128', 'code_39', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'itf', 'codabar'];
                if (!window.BarcodeDetector) {
                    return fallbackFormats;
                }

                try {
                    const supported = await window.BarcodeDetector.getSupportedFormats();
                    return fallbackFormats.filter((format) => supported.includes(format));
                } catch (error) {
                    return fallbackFormats;
                }
            };

            const startNativeScanner = async () => {
                if (!window.BarcodeDetector || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('native-barcode-detector-unavailable');
                }

                const formats = await getSupportedFormats();
                scanState.detector = new window.BarcodeDetector({ formats: formats.length ? formats : undefined });
                scanState.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                    },
                    audio: false,
                });

                scanState.engine = 'native';
                scanVideo.classList.remove('d-none');
                if (scanReader) {
                    scanReader.classList.add('d-none');
                }
                scanVideo.srcObject = scanState.stream;
                await scanVideo.play();
                scanState.active = true;
                scanLoop();
            };

            const getPreferredHtml5CameraCandidate = async () => {
                if (!window.Html5Qrcode || typeof window.Html5Qrcode.getCameras !== 'function') {
                    return { facingMode: 'environment' };
                }

                try {
                    const cameras = await window.Html5Qrcode.getCameras();
                    const preferredCamera = cameras.find((camera) => /back|rear|environment/i.test(String(camera.label || '')))
                        || cameras[cameras.length - 1];

                    if (preferredCamera?.id) {
                        return { deviceId: { exact: preferredCamera.id } };
                    }
                } catch (error) {
                    // Fall back to facing mode constraints below.
                }

                return { facingMode: 'environment' };
            };

            const startHtml5Scanner = async () => {
                if (!window.Html5Qrcode || !scanReader) {
                    throw new Error('html5-qrcode-unavailable');
                }

                scanReader.classList.remove('d-none');
                scanReader.innerHTML = '<div class="scan-reader__empty">Opening camera…</div>';
                scanVideo.classList.add('d-none');

                const html5Qrcode = new window.Html5Qrcode('scan-reader', false);
                scanState.html5Qrcode = html5Qrcode;

                const config = {
                    fps: 10,
                    rememberLastUsedCamera: true,
                    formatsToSupport: supportedHtml5Formats.length ? supportedHtml5Formats : undefined,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true,
                    },
                };

                const preferredCamera = await getPreferredHtml5CameraCandidate();
                await html5Qrcode.start(
                    preferredCamera,
                    config,
                    (decodedText) => {
                        void handleDetectedCode(decodedText);
                    },
                    () => {
                        // Ignore frame-level decode misses and keep scanning.
                    }
                );

                scanState.engine = 'html5';
                scanState.active = true;
            };

            const scanFromImageFile = async (file) => {
                if (!file) {
                    return;
                }

                if (!window.Html5Qrcode || !scanReader) {
                    setScanStatus('Photo-based scan is unavailable because the scanner library did not load. Type the value or use a hardware scanner instead.', 'warning');
                    focusField(scanState.target);
                    return;
                }

                openScanSheet(scanState.mode, scanState.target);
                await stopScannerSession();

                scanReader.classList.remove('d-none');
                scanReader.innerHTML = '<div class="scan-reader__empty">Reading the captured label…</div>';

                const html5Qrcode = new window.Html5Qrcode('scan-reader', false);
                scanState.html5Qrcode = html5Qrcode;
                scanState.engine = 'file';

                try {
                    const decodedText = await html5Qrcode.scanFile(file, true);
                    await handleDetectedCode(decodedText);
                } catch (error) {
                    setScanStatus('Could not read a barcode or QR code from that photo. Try a tighter close-up, better light, or type it manually.', 'warning');
                }
            };

            const openScanner = async (mode, target = 'return_id') => {
                if (scanState.active || scanState.engine) {
                    await closeScanner();
                }

                scanState.mode = mode;
                scanState.target = target;
                scanState.lastRawValue = null;

                openScanSheet(mode, target);
                resetScanViewport();

                try {
                    await startHtml5Scanner();
                    return;
                } catch (html5Error) {
                    await stopHtml5Scanner();
                }

                try {
                    await startNativeScanner();
                    return;
                } catch (nativeError) {
                    setScanStatus('Live camera scan is unavailable on this phone right now. Allow camera access, or tap "Use camera photo" to capture the label instead.', 'warning');
                }
            };

            const localDraftKey = form.dataset.localDraftKey || 'dossentry-inspection-draft-v1';
            const draftableElements = () => Array.from(form.elements).filter((element) => {
                return element.name
                    && !['_token', 'photos[]', 'save_as_draft'].includes(element.name)
                    && element.type !== 'file';
            });

            const collectLocalDraft = () => {
                const draft = {};

                draftableElements().forEach((element) => {
                    if ((element.type === 'radio' || element.type === 'checkbox') && !element.checked) {
                        return;
                    }

                    draft[element.name] = element.value;
                });

                draft.saved_at = new Date().toISOString();
                return draft;
            };

            const applyLocalDraft = (draft) => {
                if (!draft || typeof draft !== 'object') {
                    return;
                }

                Object.entries(draft).forEach(([name, value]) => {
                    if (name === 'saved_at') {
                        return;
                    }

                    draftableElements()
                        .filter((element) => element.name === name)
                        .forEach((element) => {
                            if (element.type === 'radio' || element.type === 'checkbox') {
                                element.checked = element.value === String(value);
                                return;
                            }

                            element.value = value;
                        });
                });

                brandSelect.dispatchEvent(new Event('change', { bubbles: true }));
                conditionInputs.forEach((input) => {
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
                setScanStatus('Local draft restored on this browser. Review the fields before submitting.', 'success');
            };

            const saveLocalDraft = () => {
                try {
                    localStorage.setItem(localDraftKey, JSON.stringify(collectLocalDraft()));
                    setScanStatus('Local draft saved on this browser. It can be restored even if the page refreshes.', 'success');
                } catch (error) {
                    setScanStatus('Local draft could not be saved in this browser. Use server draft if network is available.', 'warning');
                }
            };

            const restoreLocalDraft = () => {
                try {
                    const draft = JSON.parse(localStorage.getItem(localDraftKey) || 'null');
                    if (!draft) {
                        setScanStatus('No local draft found on this browser for this inspection.', 'warning');
                        return;
                    }

                    applyLocalDraft(draft);
                } catch (error) {
                    setScanStatus('Local draft exists but could not be read. Start a new draft or use server draft.', 'warning');
                }
            };

            let localDraftTimer = null;
            const scheduleLocalDraftSave = () => {
                clearTimeout(localDraftTimer);
                localDraftTimer = setTimeout(() => {
                    try {
                        localStorage.setItem(localDraftKey, JSON.stringify(collectLocalDraft()));
                    } catch (error) {
                        // Explicit save button will surface browser storage failures.
                    }
                }, 600);
            };

            if (refundSelect) {
                refundSelect.addEventListener('change', function () {
                    refundSelect.dataset.userTouched = '1';
                });
            }

            dispositionInputs.forEach((input) => {
                input.addEventListener('change', function () {
                    input.dataset.userTouched = '1';
                });
            });

            conditionInputs.forEach((input) => {
                input.addEventListener('change', function () {
                    syncRecommendedDisposition(profiles[brandSelect.value] || null, true);
                });
            });

            scanButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    void openScanner(button.dataset.scanMode, button.dataset.scanTarget || 'return_id');
                });
            });

            scanClosers.forEach((button) => {
                button.addEventListener('click', function () {
                    void closeScanner();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !scanSheet.classList.contains('d-none')) {
                    void closeScanner();
                }
            });

            if (scanFileButton && scanFileInput) {
                scanFileButton.addEventListener('click', function () {
                    scanFileInput.click();
                });

                scanFileInput.addEventListener('change', function (event) {
                    const file = event.target.files?.[0];
                    event.target.value = '';
                    void scanFromImageFile(file);
                });
            }

            if (saveLocalDraftButton) {
                saveLocalDraftButton.addEventListener('click', saveLocalDraft);
            }

            if (restoreLocalDraftButton) {
                restoreLocalDraftButton.addEventListener('click', restoreLocalDraft);
            }

            form.addEventListener('input', scheduleLocalDraftSave);
            form.addEventListener('change', scheduleLocalDraftSave);

            window.addEventListener('beforeunload', function () {
                void closeScanner();
            });

            brandSelect.addEventListener('change', syncRuleProfile);
            syncRuleProfile();
        });
    </script>
@endpush
