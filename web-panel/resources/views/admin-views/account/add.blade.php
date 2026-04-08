@extends('layouts.admin.app')

@section('title',\App\CPU\translate('add_new_account'))

@section('content')
<div class="content container-fluid">
        <div class="mb-3">
            <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize">
                <i class="tio-add-circle-outlined"></i>
                <span>{{\App\CPU\translate('add_new_account')}}</span>
            </h1>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.account.store')}}" method="post" id="store-or-update-data">
                            <div class="row pl-2" >
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('account_title')}}</label>
                                        <input type="text" name="account" class="form-control" value="{{ old('account') }}"  placeholder="{{\App\CPU\translate('account_title')}}" >
                                        <span class="error-text" data-error="account"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label">{{\App\CPU\translate('description')}} </label>
                                        <input type="text" name="description" class="form-control" value="{{ old('description') }}"  placeholder="{{\App\CPU\translate('description')}}" >
                                        <span class="error-text" data-error="description"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2" >
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('balance')}}</label>
                                        <input type="number" step="0.01" min="0" name="balance" class="form-control" value="{{ old('balance') }}"  placeholder="{{\App\CPU\translate('initial_balance')}}" >
                                        <span class="error-text" data-error="balance"></span>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" >{{\App\CPU\translate('account_number')}}</label>
                                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number') }}"  placeholder="{{\App\CPU\translate('account_number')}}" >
                                        <span class="error-text" data-error="account_number"></span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

