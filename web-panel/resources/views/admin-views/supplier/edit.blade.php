@extends('layouts.admin.app')

@section('title',\App\CPU\translate('update_supplier'))

@section('content')
<div class="content container-fluid">
    <div class="row align-items-center mb-3">
        <div class="col-sm mb-2 mb-sm-0">
            <h1 class="page-header-title text-capitalize"><i
                    class="tio-edit"></i> {{\App\CPU\translate('update_supplier')}}
            </h1>
        </div>
    </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.supplier.update',[$supplier->id])}}" method="post" enctype="multipart/form-data" id="store-or-update-data">
                                <div class="row pl-2" >
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label" >{{\App\CPU\translate('supplier_name')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $supplier->name }}"  placeholder="{{\App\CPU\translate('supplier_name')}}" >
                                            <span class="error-text" data-error="name"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">{{\App\CPU\translate('mobile_no')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="tel" id="mobile" name="mobile" class="form-control" value="{{ $supplier->mobile }}"
                                                   placeholder="{{\App\CPU\translate('mobile_no')}}"
                                                   pattern="[+0-9]+"
                                                   title="Please enter a valid phone number with only numbers and the plus sign (+)"
                                                   >
                                            <span class="error-text" data-error="mobile"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pl-2" >
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label" >{{\App\CPU\translate('email')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="{{ $supplier->email }}"  placeholder="{{\App\CPU\translate('Ex_:_ex@example.com')}}" >
                                            <span class="error-text" data-error="email"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label" >{{\App\CPU\translate('state')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text" name="state" class="form-control" value="{{ $supplier->state }}"  placeholder="{{\App\CPU\translate('state')}}" >
                                            <span class="error-text" data-error="state"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pl-2" >
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">{{\App\CPU\translate('city')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text"  name="city" class="form-control" value="{{ $supplier->city }}"  placeholder="{{\App\CPU\translate('city')}}" >
                                            <span class="error-text" data-error="city"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">{{\App\CPU\translate('zip_code')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text"  name="zip_code" class="form-control" value="{{ $supplier->zip_code }}"  placeholder="{{\App\CPU\translate('zip_code')}}" >
                                            <span class="error-text" data-error="zip_code"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pl-2" >
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="input-label">{{\App\CPU\translate('address')}} <span
                                                    class="input-label-secondary text-danger">*</span></label>
                                            <input type="text"  name="address" class="form-control" value="{{ $supplier->address }}"  placeholder="{{\App\CPU\translate('address')}}" >
                                            <span class="error-text" data-error="address"></span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="bg-fafafa p-3 p-lg-4 rounded-10 d-flex justify-content-center align-items-center h-100">
                                            <div class="text-center">
                                                <h4 class="mb-3">{{ \App\CPU\translate('Upload_Image') }}</h4>
                                                <label class="upload-file" data-image-id="">
                                                    <input type="file" name="image" id="customFileEg1" class="upload-file-input"
                                                           accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}" data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                                                    <button type="button" class="remove_btn btn btn-danger">
                                                        <i class="fi fi-sr-cross"></i>
                                                    </button>
                                                    <div class="upload-file-wrapper w-100px">
                                                        <div class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                            <div
                                                                class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                                <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                                <p class="fs-10 mb-0">{{ \App\CPU\translate('Add_image') }}</p>
                                                            </div>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy"
                                                             src="{{onErrorImage($supplier['image'],asset('storage/supplier').'/' . $supplier['image'],'' ,'supplier/')}}"
                                                             data-default-src="{{onErrorImage($supplier['image'],asset('storage/supplier').'/' . $supplier['image'],'' ,'supplier/')}}"
                                                             alt="{{\App\CPU\translate('supplier thumbnail')}}">
                                                    </div>
                                                </label>
                                                <p class="mb-0 title fs-12 mt-4"> {{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}<span
                                                        class="fw-bold">(1:1)</span></p>
                                                <span class="error-text" data-error="image"></span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="old_image" id="oldImage" value="{{ $supplier['image'] }}">
                                    </div>
                                </div>
                            <div class="btn--container d-flex justify-content-end my-2">
                                <button type="submit" class="btn btn-primary">{{\App\CPU\translate('update')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>
@endpush
