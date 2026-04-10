@extends('layouts.admin.app')

@section('title', \App\CPU\translate('return_cases'))

@section('content')
    @php
        $badgeMap = [
            'hold' => 'badge-soft-warning',
            'ready_to_release' => 'badge-soft-info',
            'needs_review' => 'badge-soft-danger',
            'released' => 'badge-soft-success',
        ];
        $canInspect = \App\CPU\Helpers::admin_has_module('returns_inspect_section');
    @endphp
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">{{ $inspectorView ? 'My Cases' : 'Cases' }}</h1>
                <p class="text-muted mb-0">
                    {{ $inspectorView
                        ? 'See only the cases you submitted from inspection.'
                        : 'Track every inspection, evidence gap, and refund decision from one queue.' }}
                </p>
            </div>
            @if($canInspect)
                <div class="col-sm-auto mt-3 mt-sm-0">
                    <a href="{{ route('admin.returns.inspect') }}" class="btn btn-primary">New inspection</a>
                </div>
            @endif
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" class="row g-2">
                    <div class="col-md-4">
                        <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Search by return ID, SKU, serial">
                    </div>
                    @unless($inspectorView)
                        <div class="col-md-2">
                            <select class="form-control" name="brand_id">
                                <option value="">All brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                    <div class="col-md-2">
                        <select class="form-control" name="refund_status">
                            <option value="">All refund states</option>
                            @foreach($refundStatusOptions as $status)
                                <option value="{{ $status }}" {{ request('filter_status', request('refund_status')) === $status ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @unless($inspectorView)
                        <div class="col-md-2">
                            <select class="form-control" name="min_sla_hours">
                                <option value="">Any SLA age</option>
                                <option value="24" {{ request('min_sla_hours') == '24' ? 'selected' : '' }}>24h and older</option>
                                <option value="48" {{ request('min_sla_hours') == '48' ? 'selected' : '' }}>48h and older</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center gap-2">
                            <label class="mb-0 d-flex align-items-center gap-2">
                                <input type="checkbox" name="evidence_missing" value="1" {{ request()->boolean('evidence_missing') ? 'checked' : '' }}>
                                <span>Evidence missing</span>
                            </label>
                        </div>
                    @endunless
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a class="btn btn-light" href="{{ route('admin.returns.cases.index') }}">Reset</a>
                        <button class="btn btn-primary" type="submit">Apply filters</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Return ID</th>
                        <th>Brand</th>
                        <th>Condition</th>
                        <th>Disposition</th>
                        <th>Refund</th>
                        <th>Evidence</th>
                        <th>SLA Age</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $resource->return_id }}</div>
                                <div class="text-muted small">{{ $resource->product_sku ?: 'No SKU' }}</div>
                            </td>
                            <td>{{ $resource->brand?->name ?? 'N/A' }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $resource->condition_code) }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $resource->disposition_code) }}</td>
                            <td>
                                <span class="badge {{ $badgeMap[$resource->refund_status] ?? 'badge-soft-secondary' }}">
                                    {{ str_replace('_', ' ', $resource->refund_status) }}
                                </span>
                            </td>
                            <td>
                                {{ $resource->evidence_photo_count }}/{{ $resource->required_photo_count }}
                                @if(!$resource->evidence_complete)
                                    <div class="text-danger small">Incomplete</div>
                                @endif
                            </td>
                            <td>{{ $resource->sla_age_hours }}h</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.returns.cases.show', $resource->id) }}">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No return cases found.</td>
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
@endsection
