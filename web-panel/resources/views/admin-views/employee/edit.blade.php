@extends('layouts.admin.app')
@section('title',\App\CPU\translate('Employee_edit'))

@section('content')
<div class="content container-fluid">

    <div class="row align-items-center mb-3">
        <div class="col-sm mb-2 mb-sm-0">
            <h1 class="page-header-title text-capitalize"><i
                    class="tio-edit"></i> {{\App\CPU\translate('update_employee')}}
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 card py-3">
            <form action="{{route('admin.employee.update',[$employee['id']])}}" method="post"  class="js-validate"  enctype="multipart/form-data" id="store-or-update-data">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                {{ \App\CPU\translate('General_Information') }}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="form-label " for="fname">{{\App\CPU\translate('first_name')}}</label>
                                            <input type="text" name="f_name" class="form-control h--45px" id="fname"
                                                    placeholder="{{ \App\CPU\translate('Ex:_John') }} " value="{{$employee['f_name']}}" >
                                            <span class="error-text" data-error="f_name"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="form-label " for="lname">{{\App\CPU\translate('last_name')}}</label>
                                            <input type="text" name="l_name" class="form-control h--45px" id="lname"
                                                    placeholder="{{ \App\CPU\translate('Ex:_Doe') }} " value="{{$employee['l_name']}}">
                                            <span class="error-text" data-error="l_name"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-0">
                                            <label class="form-label " for="role_id">{{\App\CPU\translate('Role')}}</label>
                                            <select class="form-control w-100 h--45px js-select2-custom" name="role_id" id="role_id" >
                                                    <option value="" selected disabled>{{\App\CPU\translate('select_Role')}}</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{$role->id}}" {{$role['id']==$employee['role_id']?'selected':''}}>{{$role->name}}</option>
                                                    @endforeach
                                            </select>
                                            <span class="error-text" data-error="role_id"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label " for="phone">{{\App\CPU\translate('phone')}}</label>
                                        <input type="tel" name="phone" value="{{$employee['phone']}}" class="form-control h--45px" id="phone"
                                               pattern="[+0-9]+"
                                               title="Please enter a valid phone number with only numbers and the plus sign (+)"
                                               placeholder="{{ \App\CPU\translate('Ex:_+88017********') }} " >
                                        <span class="error-text" data-error="phone"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
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
                                                     src="{{onErrorImage($employee['image'],asset('storage/admin').'/' . $employee['image'],'' ,'admin/')}}"
                                                     data-default-src="{{onErrorImage($employee['image'],asset('storage/admin').'/' . $employee['image'],'' ,'admin/')}}"
                                                     alt="{{\App\CPU\translate('Employee thumbnail')}}">
                                            </div>
                                        </label>
                                        <p class="mb-0 title fs-12 mt-4"> {{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}<span
                                                class="fw-bold">(1:1)</span></p>
                                        <span class="error-text" data-error="image"></span>
                                    </div>
                                </div>
                                <input type="hidden" name="old_image" id="oldImage" value="{{ $employee['image'] }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                {{\App\CPU\translate('account_info')}}
                            </span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label " for="email">{{\App\CPU\translate('email')}}</label>
                                    <input type="email" name="email" value="{{$employee['email']}}" class="form-control h--45px" id="email"
                                        placeholder="{{ \App\CPU\translate('Ex:_ex@gmail.com') }} " >
                                    <span class="error-text" data-error="email"></span>
                                </div>
                                <div class="col-md-4">
                                    <div class="js-form-message form-group">
                                        <label class="input-label" for="signupSrPassword">{{\App\CPU\translate('password')}}</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control h--45px" name="password" id="signupSrPassword"
                                                   placeholder="{{\App\CPU\translate('password_length_8+')}}" aria-label="8+ characters required"
                                                   data-msg="Your password is invalid. Please try again."
                                                   data-hs-toggle-password-options='{"target": ".js-toggle-password-target-1",
                                                                                   "defaultClass": "tio-hidden-outlined",
                                                                                   "showClass": "tio-visible-outlined",
                                                                                    "classChangeTarget": ".js-toggle-password-show-icon-1"}'>
                                            <div class="js-toggle-password-target-1 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-password-show-icon-1 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <span class="error-text" data-error="password"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="js-form-message form-group">
                                        <label class="input-label" for="signupSrConfirmPassword">{{\App\CPU\translate('confirm_password')}}</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" class="js-toggle-password form-control h--45px" name="confirm_password"
                                                   id="signupSrConfirmPassword" placeholder="{{\App\CPU\translate('password_length_8+')}}"
                                                   aria-label="8+ characters required" data-msg="Password does not match the confirm password."
                                                   data-hs-toggle-password-options='{"target": ".js-toggle-password-target-2",
                                                                                   "defaultClass": "tio-hidden-outlined",
                                                                                   "showClass": "tio-visible-outlined",
                                                                                   "classChangeTarget": ".js-toggle-password-show-icon-2"}'>
                                            <div class="js-toggle-password-target-2 input-group-append">
                                                <a class="input-group-text" href="javascript:;">
                                                    <i class="js-toggle-password-show-icon-2 tio-visible-outlined"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <span class="error-text" data-error="confirm_password"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn--container d-flex justify-content-end my-2">
                    <button type="submit" class="btn btn-primary">{{\App\CPU\translate('update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>

    <script>
        "use strict";

        $('.js-toggle-password').each(function() {
            new HSTogglePassword(this).init()
        });
    </script>
@endpush
