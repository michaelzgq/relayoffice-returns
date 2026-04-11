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
        .scan-viewport video {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
    @php($selectedBrandId = (int) old('brand_id', $currentCase?->brand_id))
    @php($initialProfile = $selectedBrandId ? $profiles->get($selectedBrandId) : null)
    @php($canManageRefundGate = \App\CPU\Helpers::admin_has_module('returns_queue_section'))
    @php($inspectorView = \App\CPU\Helpers::returns_user_is_inspector())
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

        <form method="post" action="{{ route('admin.returns.inspect.store') }}" enctype="multipart/form-data">
            @csrf
            @if($currentCase)
                <input type="hidden" name="case_id" value="{{ $currentCase->id }}">
            @endif

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
                                    <input class="form-control form-control-lg" id="return-id-input" type="text" name="return_id" value="{{ old('return_id', $currentCase?->return_id) }}" placeholder="Scan or type return ID" required>
                                    <button class="btn btn-outline-primary scan-action-btn" type="button" data-scan-mode="field" data-scan-target="return_id">Scan</button>
                                </div>
                                <div class="scan-helper">Works with camera scan, USB/Bluetooth barcode scanners, or manual typing.</div>
                            </div>
                            <div class="form-group">
                                <label class="title">Client / Brand</label>
                                <select class="form-control form-control-lg" id="brand-select-input" name="brand_id" required>
                                    <option value="">Choose the client playbook</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ (string) old('brand_id', $currentCase?->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="title">SKU / Barcode <span class="text-danger {{ $initialProfile?->sku_required ? '' : 'd-none' }}" id="sku-required-indicator">*</span></label>
                                <div class="scan-input-row">
                                    <input class="form-control form-control-lg" id="product-sku-input" type="text" name="product_sku" value="{{ old('product_sku', $currentCase?->product_sku) }}" placeholder="Scan or type SKU">
                                    <button class="btn btn-outline-primary scan-action-btn" type="button" data-scan-mode="field" data-scan-target="product_sku">Scan</button>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="title">Serial Number <span class="text-danger {{ $initialProfile?->serial_required ? '' : 'd-none' }}" id="serial-required-indicator">*</span></label>
                                <div class="scan-input-row">
                                    <input class="form-control form-control-lg" id="serial-number-input" type="text" name="serial_number" value="{{ old('serial_number', $currentCase?->serial_number) }}" placeholder="Only if this playbook requires it">
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
                                            <input class="condition-input" type="radio" name="condition_code" value="{{ $condition }}" {{ old('condition_code', $currentCase?->condition_code) === $condition ? 'checked' : '' }} required>
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
                                    {{ $initialProfile?->recommendedDispositionForCondition(old('condition_code', $currentCase?->condition_code)) ? 'Recommended action ready' : 'Choose item condition first' }}
                                </div>
                                <div class="small text-muted mb-0" id="recommendation-copy">
                                    @php($initialRecommendedDisposition = $initialProfile?->recommendedDispositionForCondition(old('condition_code', $currentCase?->condition_code)))
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
                        <video id="scan-video" playsinline muted></video>
                    </div>
                    <div class="scan-manual-help" id="scan-manual-help">Tip: if the browser does not support camera scanning, focus the field and use a USB or Bluetooth scanner instead.</div>
                </div>
                <div class="scan-sheet__footer d-flex justify-content-between align-items-center">
                    <div class="small text-muted">Target <span class="scan-target-pill" id="scan-target-pill">Return label</span></div>
                    <button class="btn btn-light" type="button" data-scan-close>Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

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
            const ruleRecommendedActions = document.getElementById('rule-recommended-actions');
            const ruleRequiredFields = document.getElementById('rule-required-fields');
            const rulePhotoTypes = document.getElementById('rule-photo-types');
            const ruleAllowedValues = document.getElementById('rule-allowed-values');
            const photoRequirementHelp = document.getElementById('photo-requirement-help');
            const recommendationTitle = document.getElementById('recommendation-title');
            const recommendationCopy = document.getElementById('recommendation-copy');
            const conditionInputs = Array.from(form.querySelectorAll('.condition-input'));
            const dispositionInputs = Array.from(form.querySelectorAll('.disposition-input'));
            const scanStatusBanner = document.getElementById('scan-status-banner');
            const scanSheet = document.getElementById('scan-sheet');
            const scanVideo = document.getElementById('scan-video');
            const scanTitle = document.getElementById('scan-sheet-title');
            const scanSubtitle = document.getElementById('scan-sheet-subtitle');
            const scanTargetPill = document.getElementById('scan-target-pill');
            const scanManualHelp = document.getElementById('scan-manual-help');
            const scanButtons = Array.from(document.querySelectorAll('[data-scan-mode]'));
            const scanClosers = Array.from(document.querySelectorAll('[data-scan-close]'));

            const statusLabels = @json(\App\Models\ReturnCase::decisionStatusLabels());
            const humanize = (value) => statusLabels[value] || String(value || '').replaceAll('_', ' ');
            const humanizeList = (values) => (values || []).map((value) => humanize(value)).join(', ');
            const mapToHumanList = (mapping) => Object.entries(mapping || {}).map(([condition, disposition]) => `${humanize(condition)} -> ${humanize(disposition)}`).join(', ');

            const scanState = {
                active: false,
                mode: 'label',
                target: 'return_id',
                stream: null,
                detector: null,
                rafId: null,
                lastRawValue: null,
            };

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
                    ruleRecommendedActions.textContent = 'Recommended warehouse actions: brand rule missing';
                    ruleRequiredFields.textContent = 'Required fields: brand rule missing';
                    rulePhotoTypes.textContent = 'Required photo types: brand rule missing';
                    ruleAllowedValues.textContent = 'Allowed condition / disposition: brand rule missing';
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
                ruleRecommendedActions.textContent = `Recommended warehouse actions: ${mapToHumanList(profile.recommended_dispositions || {}) || 'No default actions'}`;

                const requiredFields = [];
                if (profile.sku_required) requiredFields.push('SKU');
                if (profile.serial_required) requiredFields.push('Serial number');
                if (profile.notes_required) requiredFields.push('Notes');

                ruleRequiredFields.textContent = `Required fields: ${requiredFields.length ? requiredFields.join(', ') : 'No extra fields required'}`;
                rulePhotoTypes.textContent = `Required photo types: ${profile.required_photo_types.length ? humanizeList(profile.required_photo_types) : 'No specific capture types'}`;
                ruleAllowedValues.textContent = `Allowed condition / disposition: ${humanizeList(profile.allowed_conditions)} / ${humanizeList(profile.allowed_dispositions)}`;
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

            const closeScanner = () => {
                scanState.active = false;
                if (scanState.rafId) {
                    cancelAnimationFrame(scanState.rafId);
                    scanState.rafId = null;
                }
                if (scanState.stream) {
                    scanState.stream.getTracks().forEach((track) => track.stop());
                    scanState.stream = null;
                }
                if (scanVideo) {
                    scanVideo.pause();
                    scanVideo.srcObject = null;
                }
                scanSheet.classList.add('d-none');
                scanSheet.classList.remove('is-open');
                scanSheet.setAttribute('aria-hidden', 'true');
            };

            const handleDetectedCode = (rawValue) => {
                if (!rawValue || rawValue === scanState.lastRawValue) {
                    return;
                }

                scanState.lastRawValue = rawValue;
                closeScanner();

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
                            handleDetectedCode(rawValue);
                            return;
                        }
                    }
                } catch (error) {
                    setScanStatus('Camera scanning failed on this browser. Use Chrome or Edge on mobile, or use a hardware scanner in the active field.', 'warning');
                    closeScanner();
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

            const openScanner = async (mode, target = 'return_id') => {
                if (scanState.active) {
                    closeScanner();
                }

                scanState.mode = mode;
                scanState.target = target;
                scanState.lastRawValue = null;

                if (!window.BarcodeDetector || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    focusField(target);
                    setScanStatus('Camera scan is not available in this browser. Focus the field and use a USB/Bluetooth scanner, or open Dossentry in Chrome on mobile.', 'warning');
                    return;
                }

                try {
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
                } catch (error) {
                    focusField(target);
                    setScanStatus('Camera access was blocked. Allow camera access and try again, or use a USB/Bluetooth scanner in the active field.', 'warning');
                    return;
                }

                scanSheet.classList.remove('d-none');
                scanSheet.classList.add('is-open');
                scanSheet.setAttribute('aria-hidden', 'false');

                scanTitle.textContent = mode === 'label' ? 'Scan return label' : 'Scan field value';
                scanSubtitle.textContent = mode === 'label'
                    ? 'Point the camera at the return label. Structured QR codes will auto-fill multiple fields.'
                    : 'Point the camera at the barcode or QR code for this field.';
                scanTargetPill.textContent = mode === 'label' ? 'Return label' : humanize(target);
                scanManualHelp.textContent = mode === 'label'
                    ? 'Best result: QR code or barcode that includes return ID, SKU/barcode, or serial. If your browser does not support camera scan, use a Bluetooth scanner while the cursor is in Return ID.'
                    : 'If camera scan is unavailable, close this sheet and use a USB/Bluetooth scanner while the cursor is in the target field.';

                scanVideo.srcObject = scanState.stream;
                await scanVideo.play();
                scanState.active = true;
                scanLoop();
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
                    openScanner(button.dataset.scanMode, button.dataset.scanTarget || 'return_id');
                });
            });

            scanClosers.forEach((button) => {
                button.addEventListener('click', closeScanner);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && scanState.active) {
                    closeScanner();
                }
            });

            window.addEventListener('beforeunload', closeScanner);

            brandSelect.addEventListener('change', syncRuleProfile);
            syncRuleProfile();
        });
    </script>
@endpush
