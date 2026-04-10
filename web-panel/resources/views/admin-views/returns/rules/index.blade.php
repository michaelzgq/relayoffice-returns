@extends('layouts.admin.app')

@section('title', 'Client Playbooks')

@section('content')
    @php
        $readOnly = \App\CPU\Helpers::returns_user_is_guest_demo();
        $selectedConditions = old('allowed_conditions', $editingProfile?->allowed_conditions ?? []);
        $selectedDispositions = old('allowed_dispositions', $editingProfile?->allowed_dispositions ?? []);
        $recommendedDispositions = old('recommended_dispositions', $editingProfile?->recommended_dispositions ?? []);
        $selectedPhotoTypes = old('required_photo_types', $editingProfile?->required_photo_types ?? []);
    @endphp
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">Client Playbooks</h1>
                <p class="text-muted mb-0">Set the inspection checklist and default decision state each client brand expects your warehouse team to follow.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="mb-0">{{ $readOnly ? 'Playbook Snapshot' : ($editingProfile ? 'Edit Playbook' : 'New Playbook') }}</h4>
                    </div>
                    <div class="card-body">
                        @if($readOnly)
                            <div class="alert alert-soft-info mb-0">
                                Guest demo access is read-only. Use this page to inspect how each client playbook structures evidence, conditions, and default decision guidance without changing warehouse rules.
                            </div>
                        @else
                            <form method="post" action="{{ $editingProfile ? route('admin.returns.rules.update', $editingProfile->id) : route('admin.returns.rules.store') }}">
                                @csrf
                                <div class="form-group">
                                    <label class="title">Brand <span class="text-danger">*</span></label>
                                    <select class="form-control" name="brand_id" required>
                                        <option value="">Select brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ (string) old('brand_id', $editingProfile?->brand_id) === (string) $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="title">Playbook Name</label>
                                    <input class="form-control" type="text" name="profile_name" value="{{ old('profile_name', $editingProfile?->profile_name) }}" placeholder="Apparel returns v1" required>
                                </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="title">Required Photo Count</label>
                                        <input class="form-control" type="number" min="0" max="10" name="required_photo_count" value="{{ old('required_photo_count', $editingProfile?->required_photo_count ?? 3) }}" required>
                                        <small class="text-muted d-block mt-2">Guardrail: required photo count cannot exceed the number of selected photo template slots below.</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="title">Default Decision State</label>
                                        <select class="form-control" name="default_refund_status">
                                            @foreach($refundStatusOptions as $status)
                                                <option value="{{ $status }}" {{ old('default_refund_status', $editingProfile?->default_refund_status ?? 'hold') === $status ? 'selected' : '' }}>
                                                    {{ \App\Models\ReturnCase::decisionStatusLabel($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="notes_required" value="1" {{ old('notes_required', $editingProfile?->notes_required ?? true) ? 'checked' : '' }}>
                                        <span>Notes required</span>
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="active" value="1" {{ old('active', $editingProfile?->active ?? true) ? 'checked' : '' }}>
                                        <span>Active</span>
                                    </label>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="sku_required" value="1" {{ old('sku_required', $editingProfile?->sku_required ?? false) ? 'checked' : '' }}>
                                        <span>SKU required</span>
                                    </label>
                                </div>
                                <div class="col-sm-6 mt-2">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" name="serial_required" value="1" {{ old('serial_required', $editingProfile?->serial_required ?? false) ? 'checked' : '' }}>
                                        <span>Serial required</span>
                                    </label>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h5>Allowed Conditions</h5>
                                <div class="row">
                                    @foreach($conditionOptions as $condition)
                                        <div class="col-sm-6 mb-2">
                                            <label class="d-flex align-items-center gap-2">
                                                <input type="checkbox" name="allowed_conditions[]" value="{{ $condition }}" {{ in_array($condition, $selectedConditions, true) ? 'checked' : '' }}>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $condition) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <h5>Allowed Dispositions</h5>
                                <div class="row">
                                    @foreach($dispositionOptions as $disposition)
                                        <div class="col-sm-6 mb-2">
                                            <label class="d-flex align-items-center gap-2">
                                                <input type="checkbox" name="allowed_dispositions[]" value="{{ $disposition }}" {{ in_array($disposition, $selectedDispositions, true) ? 'checked' : '' }}>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $disposition) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Recommended Warehouse Action</h5>
                                <p class="text-muted small mb-3">Optional. If set, inspectors can submit faster because the playbook auto-selects the default warehouse action for each condition.</p>
                                <div class="row">
                                    @foreach($conditionOptions as $condition)
                                        <div class="col-sm-6 mb-3">
                                            <label class="title d-block">{{ str_replace('_', ' ', ucfirst($condition)) }}</label>
                                            <select class="form-control" name="recommended_dispositions[{{ $condition }}]">
                                                <option value="">No default action</option>
                                                @foreach($dispositionOptions as $disposition)
                                                    <option value="{{ $disposition }}" {{ ($recommendedDispositions[$condition] ?? '') === $disposition ? 'selected' : '' }}>
                                                        {{ str_replace('_', ' ', ucfirst($disposition)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Required Photo Types</h5>
                                <p class="text-muted small mb-3">These slots define the evidence checklist operators must complete in inspection and export.</p>
                                <div class="row">
                                    @foreach($photoTypeOptions as $photoType)
                                        <div class="col-sm-6 mb-2">
                                            <label class="d-flex align-items-center gap-2">
                                                <input type="checkbox" name="required_photo_types[]" value="{{ $photoType }}" {{ in_array($photoType, $selectedPhotoTypes, true) ? 'checked' : '' }}>
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $photoType) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    @if($editingProfile)
                                        <a class="btn btn-light" href="{{ route('admin.returns.rules.index') }}">Cancel edit</a>
                                    @endif
                                    <button class="btn btn-primary" type="submit">{{ $editingProfile ? 'Update playbook' : 'Save playbook' }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Playbooks</h4>
                        <span class="badge badge-primary rounded">{{ $resources->total() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-align-middle mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Brand</th>
                                <th>Evidence</th>
                                <th>Decision Default</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($resources as $profile)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $profile->brand?->name }}</div>
                                        <div class="text-muted small">{{ $profile->profile_name }}</div>
                                        <div class="text-muted small mt-1">
                                            {{ collect($profile->required_photo_types ?? [])->map(fn ($item) => str_replace('_', ' ', $item))->implode(', ') ?: 'No evidence template' }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            Recommended actions:
                                            {{ collect($profile->recommended_dispositions ?? [])->map(fn ($item, $condition) => str_replace('_', ' ', $condition) . ' -> ' . str_replace('_', ' ', $item))->implode(', ') ?: 'No default actions' }}
                                        </div>
                                    </td>
                                    <td>{{ $profile->required_photo_count }} photos</td>
                                    <td class="text-capitalize">{{ \App\Models\ReturnCase::decisionStatusLabel($profile->default_refund_status) }}</td>
                                    <td>
                                        <span class="badge {{ $profile->active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $profile->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @unless($readOnly)
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.returns.rules.index', ['edit' => $profile->id]) }}">Edit</a>
                                        @endunless
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No rule profiles yet.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer border-0">
                        {{ $resources->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
