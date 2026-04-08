@extends('layouts.admin.app')

@section('title', \App\CPU\translate('add_new_income'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize"><i
                        class="tio-add-circle-outlined"></i>
                    <span>{{ \App\CPU\translate('add_new_income') }}</span>
                </h1>
            </div>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.account.store-income') }}" method="post" id="store-or-update-data">
                            @csrf
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ \App\CPU\translate('account') }}</label>
                                        <select name="account_id" class="form-control js-select2-custom">
                                            <option value="">---{{ \App\CPU\translate('select') }}---</option>
                                            @foreach ($accounts as $account)
                                                @if ($account['id'] != 2 && $account['id'] != 3)
                                                    <option value="{{ $account['id'] }}">{{ $account['account'] }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="error-text" data-error="account_id"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ \App\CPU\translate('description') }} </label>
                                        <input type="text" name="description" class="form-control"
                                               placeholder="{{ \App\CPU\translate('description') }}">
                                        <span class="error-text" data-error="description"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ \App\CPU\translate('amount') }}</label>
                                        <input type="number" step="0.01" min="1" name="amount"
                                               class="form-control" placeholder="{{ \App\CPU\translate('amount') }}"
                                               >
                                        <span class="error-text" data-error="amount"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ \App\CPU\translate('date') }} </label>
                                        <input type="date" name="date" class="form-control" >
                                        <span class="error-text" data-error="date"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content container-fluid">
        <div class="row align-items-center mb-2">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize"><i class="tio-files"></i>
                    {{ \App\CPU\translate('income_list') }}
                    <span class="badge badge-soft-dark ml-2">{{ $incomes->total() }}</span>
                </h1>
            </div>
        </div>
        @include('layouts.admin.table._card', [
                                                'resources' => $incomes,
                                                'tableFor' => 'new-income',
                                                'searchBoxPlaceholder' => \App\CPU\translate('search_by_account'),
                                                'columns' => ['date', 'account', 'type', 'amount', 'description', 'debit', 'credit', 'balance'],
                                                'tableRows' => view('admin-views.income.partials._table-rows',['incomes' => $incomes])->render(),
                                                'exportRoute' => route('admin.account.export-income')])
    </div>
@endsection

