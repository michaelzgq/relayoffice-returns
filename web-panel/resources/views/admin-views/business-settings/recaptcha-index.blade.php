@php use function App\CPU\translate; @endphp
@extends('layouts.admin.app')
@section('title', translate('recaptcha_setup'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize">{{ translate('recaptcha_setup') }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                @php($config=\App\CPU\Helpers::get_business_settings('recaptcha'))
                <form
                    action="{{env('APP_MODE')!='demo' ? route('admin.business-settings.recaptcha-update') : 'javascript:'}}"
                    method="post"
                    id="store-or-update-data"
                >
                    <div class="card mb-20">
                        <div class="card-body py-3">
                            <div class="row g-2 justify-content-between">
                                <div class="col-xl-8 col-lg-7 col-md-6">
                                    <h3 class="mb-1">{{translate('ReCAPTCHA')}}</h3>
                                    <p class="mb-0 text-clr fs-12">{{translate('If you turn this feature on users need to verify them through the ReCAPTCHA.')}}</p>
                                </div>
                                <div class="col-xl-4 col-lg-5 col-md-6">
                                    <label
                                        class="btn btn-white border-1 bg-white title px-3 fw-medium lh-1 d-flex w-100 justify-content-between align-items-center gap-3 mb-0 rounded">
                                        {{ translate('Status') }}
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input" name="status"
                                                   {{isset($config['status']) && $config['status']==1?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="badge-soft-danger aleart-clear_wrap d-flex justify-content-between align-items-center gap-2 rounded mb-20 p-3">
                        <div class="d-flex align-items-xl-center gap-2 ">
                            <i class="tio-info text-danger fs-16"></i>
                            <div>
                                <h5 class="mb-1 fw-medium text-black">{{ translate('V3 Version is available now. Must setup for ReCAPTCHA V3') }}</h5>
                                <p class="m-0 text-clr fs-12">{{ translate('You must setup for V3 version and active the status. Otherwise the default reCAPTCHA will be displayed automatically') }}</p>
                            </div>
                        </div>
                        <button type="button"
                                class="alert_clear_btn d-center bg-white border-0 rounded-circle text-black cursor-pointer">
                            <i class="tio-clear"></i></button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center gap-1 justify-content-between mb-20">
                                <div>
                                    <h3 class="mb-1">{{translate('Google ReCAPTCHA credentials')}}</h3>
                                    <p class="mb-0 text-clr fs-12">{{translate('Fillup google ReCAPTCHA credentials to setup & active this feature properly.')}}</p>
                                </div>
                                <a href="javascript:void(0)" class="text-primary fs-12" data-toggle="modal"
                                   data-target="#instructionsModal">
                                    {{ translate('How to Get Credential') }}
                                </a>
                            </div>
                            <div class="bg-fafafa rounded p-xl-20 p-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-capitalize text-black">
                                                {{translate('Site Key')}}
                                                <span data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('Content..') }}"><i
                                                        class="tio-info text-muted"></i></span>
                                            </label>
                                            <input type="text" class="form-control" name="site_key"
                                                   value="{{env('APP_MODE')!='demo'?$config['site_key']??"":''}}">
                                            <span class="error-text" data-error="site_key"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-capitalize text-black">
                                                {{translate('Secret Key')}}
                                                <span data-toggle="tooltip" data-placement="right"
                                                      data-original-title="{{ translate('Content..') }}"><i
                                                        class="tio-info text-muted"></i></span>
                                            </label>
                                            <input type="text" class="form-control" name="secret_key"
                                                   value="{{env('APP_MODE')!='demo'?$config['secret_key']??"":''}}">
                                            <span class="error-text" data-error="secret_key"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-20 pt-xl-3 gap-3">
                                <button type="button"
                                        class="btn min-w-90 min-w-lg-120 btn-secondary">{{translate('Reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        class="btn min-w-90 min-w-lg-120 btn-primary demo-form-submit">{{translate('save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-end">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center my-5">
                        <img src="{{ asset('assets/admin/svg/components/instruction.svg') }}">
                    </div>

                    <h5 class="modal-title my-3" id="instructionsModalLabel">{{translate('Instructions')}}</h5>

                    <ol class="d-flex flex-column __gap-5px __instructions">
                        <li>{{translate('To get site key and secret key go to the Credentials page')}}
                            ({{translate('Click')}} <a
                                href="https://www.google.com/recaptcha/admin/create"
                                target="_blank">{{translate('here')}}</a>)
                        </li>
                        <li>{{translate('Add a ')}}
                            <b>{{translate('label')}}</b> {{translate('(Ex: Test Label)')}}
                        </li>
                        <li>
                            {{translate('Select reCAPTCHA v3 as ')}}
                            <b>{{translate('reCAPTCHA Type')}}</b>
                        </li>
                        <li>
                            {{translate('Add')}}
                            <b>{{translate('domain')}}</b>
                            {{translate('(For ex: demo.6amtech.com)')}}
                        </li>
                        <li>
                            {{translate('Press')}}
                            <b>{{translate('Submit')}}</b>
                        </li>
                        <li>{{translate('Copy')}} <b>Site
                                Key</b> {{translate('and')}} <b>Secret
                                Key</b>, {{translate('paste in the input filed below and')}}
                            <b>Save</b>.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>

    <script>
        "use strict";

        $('.demo-form-submit').on('click', function () {
            if ("{{env('APP_MODE')}}" == 'demo') {
                call_demo();
            }
        });

        //Alert Show Hide//
        $(document).ready(function () {
            $(document).on('click', '.alert_clear_btn', function () {
                $(this).closest('.aleart-clear_wrap').removeClass('d-flex').addClass('d-none');
            });
        });

    </script>

@endpush
