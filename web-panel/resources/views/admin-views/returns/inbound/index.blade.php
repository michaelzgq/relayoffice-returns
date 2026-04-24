@extends('layouts.admin.app')

@section('title', 'Expected Inbound')

@section('content')
    @php
        $readOnly = \App\CPU\Helpers::returns_user_is_guest_demo();
        $badgeMap = [
            'pending' => 'badge-soft-primary',
            'in_review' => 'badge-soft-warning',
            'received' => 'badge-soft-success',
            'exception' => 'badge-soft-danger',
        ];
    @endphp
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm">
                <h1 class="page-header-title mb-0">Expected Inbound</h1>
                <p class="text-muted mb-0">Import the return list before cartons arrive so Dossentry can match expected vs actual and surface exceptions.</p>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h4 class="mb-0">Import CSV</h4>
                    </div>
                    <div class="card-body">
                        @if($readOnly)
                            <div class="alert alert-soft-info mb-0">Guest demo access is read-only. Inbound imports stay disabled.</div>
                        @else
                            <form method="post" action="{{ route('admin.returns.inbound.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="title">Inbound CSV</label>
                                    <input class="form-control" type="file" name="inbound_csv" accept=".csv,.txt" required>
                                    <small class="text-muted d-block mt-2">
                                        Required: return_id plus brand_name or brand_id. Optional: product_sku, serial_number, tracking_number, return_reason, expected_condition.
                                    </small>
                                </div>
                                <button class="btn btn-primary" type="submit">Import expected returns</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header border-0">
                        <h4 class="mb-0">CSV Template</h4>
                    </div>
                    <div class="card-body">
                        <pre class="mb-3 p-3 bg-light rounded small">return_id,brand_name,product_sku,serial_number,tracking_number,return_reason,expected_condition
RMA-1001,Peak Audio,SKU-1001,SN-1001,1Z999,Defective,opened_damaged</pre>
                        <div class="text-muted small">
                            Use this first as a manual Loop export replacement. When Loop or another platform approves the partnership, this endpoint becomes the import shape for the read-only API pull.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="get" action="{{ route('admin.returns.inbound.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="title">Search</label>
                        <input class="form-control" type="text" name="search" value="{{ request('search') }}" placeholder="Return ID, SKU, serial, tracking">
                    </div>
                    <div class="col-md-3">
                        <label class="title">Brand</label>
                        <select class="form-control" name="brand_id">
                            <option value="">All brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ (string) request('brand_id') === (string) $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="title">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All statuses</option>
                            @foreach($statusLabels as $status => $label)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary btn-block" type="submit">Filter</button>
                        <a class="btn btn-light btn-block" href="{{ route('admin.returns.inbound.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Expected Returns</h4>
                <span class="badge badge-primary rounded">{{ $resources->total() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-align-middle mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Return</th>
                        <th>Brand</th>
                        <th>Expected Item</th>
                        <th>Tracking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($resources as $resource)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $resource->return_id }}</div>
                                <div class="text-muted small">{{ $resource->return_reason ?: 'No return reason' }}</div>
                            </td>
                            <td>{{ $resource->brand?->name ?? 'N/A' }}</td>
                            <td>
                                <div>{{ $resource->product_sku ?: 'No SKU expected' }}</div>
                                <div class="text-muted small">{{ $resource->serial_number ? 'Serial ' . $resource->serial_number : 'No serial expected' }}</div>
                                <div class="text-muted small text-capitalize">{{ $resource->expected_condition ? str_replace('_', ' ', $resource->expected_condition) : 'No condition expected' }}</div>
                            </td>
                            <td>{{ $resource->tracking_number ?: 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $badgeMap[$resource->status] ?? 'badge-soft-secondary' }}">
                                    {{ $statusLabels[$resource->status] ?? ucfirst($resource->status) }}
                                </span>
                                @if($resource->matchedReturnCase)
                                    <div class="text-muted small mt-1">Case #{{ $resource->matchedReturnCase->id }}</div>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($resource->matchedReturnCase)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.returns.cases.show', $resource->matchedReturnCase->id) }}">Open case</a>
                                @else
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.returns.inspect', ['expected_id' => $resource->id]) }}">Inspect</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No expected inbound records yet.</td>
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
