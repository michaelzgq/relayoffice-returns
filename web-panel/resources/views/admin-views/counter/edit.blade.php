@extends('layouts.admin.app')

@section('title', \App\CPU\translate('counter edit'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css" />
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="row align-items-center mb-3 ">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize">
                        <span>{{ \App\CPU\translate('counter edit') }}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.counter.update', $counter['id']) }}" method="post" id="store-or-update-data">
                    <div class="bg-fafafa rounded p-xl-20 p-3">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-20">
                                    <label class="input-label text-black fs-14" for="name">{{ \App\CPU\translate('Counter_Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $counter->name }}"  maxlength="255" >
                                    <span class="error-text" data-error="name"></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group mb-20">
                                    <label class="input-label text-black fs-14" for="number">{{ \App\CPU\translate('Counter_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="number" class="form-control" value="{{ $counter->number }}" >
                                    <span class="error-text" data-error="number"></span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12">
                                <div class="form-group mb-0">
                                    <label class="input-label text-black fs-14" for="description">{{ \App\CPU\translate('Short_Description') }}</label>
                                    <textarea class="form-control" name="description" id="" cols="30" rows="3" maxlength="100" >{{ $counter->description }}</textarea>
                                    <p class="counting-box text-end text-black-50 mb-0 mt-1">0/100</p>
                                </div>
                                <span class="error-text" data-error="description"></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-20 gap-3">
                        <button type="reset" class="btn btn-secondary min-w-90 min-w-lg-120" href="javascript:">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit" class="btn btn-primary min-w-90 min-w-lg-120">{{ \App\CPU\translate('update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
