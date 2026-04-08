@php use function App\CPU\translate; @endphp
@extends('layouts.admin.app')

@section('title', translate('add_new_transfer'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize"><i
                        class="tio-add-circle-outlined"></i>
                    {{ translate('add_new_transfer') }}
                </h1>
            </div>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.account.store-transfer') }}" method="post"
                              id="store-or-update-data">
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('account_from') }}</label>
                                        <select name="account_from_id" id="accountFromSelect"
                                                class="form-control">
                                            <option value="">---{{ translate('select') }}---</option>
                                            @foreach ($accounts as $account)
                                                @if ($account['id'] != 2 && $account['id'] != 3)
                                                    <option
                                                        value="{{ $account['id'] }}">{{ $account['account'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="error-text" data-error="account_from_id"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('account_to') }} </label>
                                        <select id="account_to_id" name="account_to_id" class="form-control">
                                            <option value="">---{{ translate('select') }}---</option>
                                            @foreach ($accounts as $account)
                                                @if ($account['id'] != 2 && $account['id'] != 3)
                                                    <option value="{{ $account['id'] }}" class="account"
                                                            style="display: none;">{{ $account['account'] }} </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="error-text" data-error="account_to_id"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ translate('description') }} </label>
                                        <input type="text" name="description" class="form-control"
                                               placeholder="{{ translate('description') }}">
                                        <span class="error-text" data-error="description"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{ translate('amount') }}</label>
                                        <input type="number" step="0.01" min="1" name="amount"
                                               class="form-control" placeholder="{{ translate('amount') }}">
                                        <span class="error-text" data-error="amount"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('date') }} </label>
                                        <input type="date" name="date" class="form-control">
                                        <span class="error-text" data-error="date"></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
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
                    {{ translate('transfer_list') }}
                    <span class="badge badge-soft-dark ml-2">{{ $transfers->total() }}</span>
                </h1>
            </div>
        </div>
        @include('layouts.admin.table._card', [
                                                'resources' => $transfers,
                                                'tableFor' => 'new-transfer',
                                                'searchBoxPlaceholder' => translate('search_by_account'),
                                                'columns' => ['date', 'account', 'type', 'amount', 'description', 'debit', 'credit', 'balance'],
                                                'tableRows' => view('admin-views.transfer.partials._table-rows',['transfers' => $transfers])->render(),
                                                'exportRoute' => route('admin.account.export-transfer')])
    </div>
@endsection

@push('script_2')
    <script>
        "use strict";

        $('#accountFromSelect').on('change', function () {
            accountChangeTr($(this).val());
        });
    </script>
@endpush
