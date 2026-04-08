@extends('layouts.admin.app')
@section('title',\App\CPU\translate('Employee_Add'))

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize"><i
                        class="tio-add-circle-outlined"></i>{{ \App\CPU\translate('Add_New_Employee') }}
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card p-3">
                    <form action="{{route('admin.employee.add-new')}}" method="post" class="js-validate"
                          enctype="multipart/form-data" id="store-or-update-data">
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
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                           for="fname">{{\App\CPU\translate('first_name')}}</label>
                                                    <input type="text" name="f_name" class="form-control h--45px"
                                                           id="fname"
                                                           placeholder="{{ \App\CPU\translate('Ex:_John') }}"
                                                           value="{{old('f_name')}}">
                                                    <span class="error-text" data-error="f_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                           for="lname">{{\App\CPU\translate('last_name')}}</label>
                                                    <input type="text" name="l_name" class="form-control h--45px"
                                                           id="lname" value="{{old('l_name')}}"
                                                           placeholder="{{ \App\CPU\translate('Ex:_Doe') }}">
                                                    <span class="error-text" data-error="l_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group mb-0">
                                                    <label class="form-label"
                                                           for="role_id">{{\App\CPU\translate('Role')}}</label>
                                                    <select class="w-100 form-control h--45px js-select2-custom"
                                                            name="role_id" id="role_id">
                                                        <option value="" selected
                                                                disabled>{{\App\CPU\translate('select_Role')}}</option>
                                                        @foreach($roles as $role)
                                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error-text" data-error="role_id"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="phone">{{\App\CPU\translate('phone')}}</label>
                                                    <input type="tel" name="phone" value="{{old('phone')}}"
                                                           class="form-control h--45px" id="phone"
                                                           pattern="[+0-9]+"
                                                           title="Please enter a valid phone number with only numbers and the plus sign (+)"
                                                           placeholder="{{ \App\CPU\translate('Ex:_+8801******') }}">
                                                    <span class="error-text" data-error="phone"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="bg-fafafa p-3 p-lg-4 rounded-10 d-flex justify-content-center align-items-center h-100">
                                            <div class="text-center">
                                                <h4 class="mb-3">{{ \App\CPU\translate('Upload_Image') }}</h4>
                                                <label class="upload-file">
                                                    <input type="file" name="image" id="customFileEg1"
                                                           class="upload-file-input"
                                                           accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}"
                                                           data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                                                    <button type="button" class="remove_btn btn btn-danger">
                                                        <i class="fi fi-sr-cross"></i>
                                                    </button>
                                                    <div class="upload-file-wrapper w-100px">
                                                        <div
                                                            class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                            <div
                                                                class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                                <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                                <p class="fs-10 mb-0">{{ \App\CPU\translate('Add_image') }}</p>
                                                            </div>
                                                        </div>
                                                        <img class="upload-file-img" loading="lazy" src=""
                                                             data-default-src="" alt="">
                                                    </div>
                                                </label>
                                                <p class="mb-0 title fs-12 mt-4">{{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}
                                                    <span class="fw-bold">(1:1)</span></p>
                                                <span class="error-text" data-error="image"></span>
                                            </div>
                                        </div>
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
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label" for="email">{{\App\CPU\translate('email')}}</label>
                                        <input type="email" name="email" value="{{old('email')}}"
                                               class="form-control h--45px" id="email"
                                               placeholder="{{ \App\CPU\translate('Ex:_ex@gmail.com') }}">
                                        <span class="error-text" data-error="email"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="js-form-message form-group">
                                            <label class="input-label"
                                                   for="signupSrPassword">{{\App\CPU\translate('password')}}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="js-toggle-password form-control h--45px"
                                                       name="password" id="signupSrPassword"
                                                       placeholder="{{\App\CPU\translate('password_length_8+')}}"
                                                       aria-label="8+ characters required"
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
                                            <label class="input-label"
                                                   for="signupSrConfirmPassword">{{\App\CPU\translate('confirm_password')}}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="js-toggle-password form-control h--45px"
                                                       name="confirm_password"
                                                       id="signupSrConfirmPassword"
                                                       placeholder="{{\App\CPU\translate('password_length_8+')}}"
                                                       aria-label="8+ characters required"
                                                       data-msg="Password does not match the confirm password."
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
                        <div class="btn--container d-flex justify-content-end my-2">
                            <button type="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="col-12 mb-4">
                <div class="row align-items-center mb-3">
                    <div class="col-sm mb-2 mb-sm-0">
                        <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize">{{\App\CPU\translate('employee_list')}}
                            <span class="btn btn-primary fs-10 lh-1 py-1 px-2 ml-2">{{$employees->total()}}</span>
                        </h1>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start flex-wrap flex-grow-1 gap-2">
                            <div class="d-flex flex-wrap flex-grow-1 flex-lg-grow-0 gap-2">
                                <form action="{{ url()->current() }}" method="GET">
                                    @foreach(['sorting_type', 'start_date', 'end_date',] as $filter)
                                        @if(request()->filled($filter))
                                            <input type="hidden" name="{{ $filter }}"
                                                   value="{{ request()->get($filter) }}">
                                        @endif
                                    @endforeach
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{ \App\CPU\translate('search_by_name_or_number_or_email') }}"
                                               aria-label="Search orders" value="{{ request()->input('search') }}">
                                        <button type="submit"
                                                class="btn btn-primary">{{ \App\CPU\translate('search') }}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="dropdown">
                                    <button type="button" id="dropdownMenuButton"
                                            class="btn btn-white text-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 h-100"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="d-none d-sm-block"> {{ \App\CPU\translate('Export') }}</span>
                                        <img src="{{ asset('assets/admin/img/download-new.svg') }}" alt=""
                                             class="svg">
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item mb-2" href="javascript:void(0)"
                                           onclick="exportList(this)" id="csv">
                                            <img class="" src="{{ asset('assets/admin/img/csv.png') }}"
                                                 alt=""/>
                                            CSV
                                        </a>
                                        <a class="dropdown-item mb-2" href="javascript:void(0)"
                                           onclick="exportList(this)" id="xlsx">
                                            <img class="" src="{{ asset('assets/admin/img/excel.png') }}"
                                                 alt=""/>
                                            Excel
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0)"
                                           onclick="exportList(this)" id="pdf">
                                            <img class="" src="{{ asset('assets/admin/img/pdf.png') }}"
                                                 alt=""/>
                                            PDF
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-end position-relative show-filter-count">
                                    <button type="button"
                                            class="offcanvas-toggle btn btn-soft-secondary d-flex align-items-center justify-content-center gap-3 flex-grow-1 h-44px-mobile"
                                            data-target="#offcanvasFilterCat"
                                            aria-label="Toggle filter menu"
                                    >
                                        <i class="fi fi-rr-bars-filter fs-16 lh-1"></i>
                                    </button>
                                </div>
                                <a href="{{ url()->current() }}"
                                   class="btn btn-soft-primary d-flex align-items-center justify-content-center gap-2 flex-grow-1 lh-1"
                                   type="button">
                                    <i class="fi fi-rr-refresh fs-16"></i>
                                </a>

                                <div class="hs-unfold">
                                    <a class="js-hs-unfold-invoker btn btn-soft-danger p-2 h-44px"
                                       href="javascript:void(0)"
                                       data-hs-unfold-options='{
                                    "target": "#showHideDropdown",
                                    "type": "css-animation"
                                }'>
                                        <img width="20" class="svg"
                                             src="{{ asset('assets/admin/img/column.svg') }}" alt=""/>
                                    </a>

                                    <div id="showHideDropdown"
                                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card min-w-340">
                                        <div class="card card-sm">
                                            <div class="card-header">
                                                <div>
                                                    <h5 class="modal-title">{{ \App\CPU\translate('Colum View') }}</h5>
                                                    <p class="fs-12 mb-0">{{ \App\CPU\translate('You can control the column view by turning the toggle on or off.') }}</p>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="overflow-y-auto max-h-100vh-500px max-h-lg-100vh-400px">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('SL') }}</span>
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_sl">
                                                            <input type="checkbox"
                                                                   class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_sl" checked>
                                                            <span class="toggle-switch-label"><span
                                                                    class="toggle-switch-indicator"></span></span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Name') }}</span>
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_name">
                                                            <input type="checkbox"
                                                                   class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_name" checked>
                                                            <span class="toggle-switch-label"><span
                                                                    class="toggle-switch-indicator"></span></span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Phone') }}</span>
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_phone">
                                                            <input type="checkbox"
                                                                   class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_phone" checked>
                                                            <span class="toggle-switch-label"><span
                                                                    class="toggle-switch-indicator"></span></span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Email') }}</span>
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_email">
                                                            <input type="checkbox"
                                                                   class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_email" checked>
                                                            <span class="toggle-switch-label"><span
                                                                    class="toggle-switch-indicator"></span></span>
                                                        </label>
                                                    </div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span
                                                            class="mr-2 fs-13 title text-capitalize">{{ \App\CPU\translate('Action') }}</span>
                                                        <label class="toggle-switch toggle-switch-sm"
                                                               for="toggleColumn_action">
                                                            <input type="checkbox"
                                                                   class="toggle-switch-input update-column-visibility"
                                                                   id="toggleColumn_action" checked>
                                                            <span class="toggle-switch-label"><span
                                                                    class="toggle-switch-indicator"></span></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th data-column="sl">{{ \App\CPU\translate('sl') }}</th>
                                <th data-column="name">{{\App\CPU\translate('Employee Name')}}</th>
                                <th data-column="phone">{{\App\CPU\translate('phone')}}</th>
                                <th data-column="email">{{\App\CPU\translate('email')}}</th>
                                <th data-column="action" class="text-center">{{\App\CPU\translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="set-rows">
                            @foreach($employees as $key=>$employee)
                                <tr>
                                    <td data-column="sl">{{$key+$employees->firstItem()}}</td>
                                    <td data-column="name"
                                        class="text-capitalize">{{$employee['f_name']}} {{$employee['l_name']}}</td>
                                    <td data-column="phone">{{$employee['phone']}}</td>
                                    <td data-column="email">
                                        {{$employee['email']}}
                                    </td>
                                    <td data-column="action" class="text-center">
                                        @if (auth('admin')->id()  != $employee['id'])
                                            <div class="d-flex justify-content-center align-items-center">
                                                <a class="btn btn-outline-theme icon-btn mr-1"
                                                   href="{{route('admin.employee.edit',[$employee['id']])}}"
                                                   title="{{\App\CPU\translate('edit_Employee')}}"><i
                                                        class="tio-edit"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger icon-btn delete-resource"
                                                        data-id="{{ $employee['id'] }}"
                                                        data-target="#deleteModal"
                                                        data-toggle="modal"
                                                        data-title="{{ \App\CPU\translate('Are you sure to delete this employee') }}?"
                                                        data-subtitle="{{ \App\CPU\translate('If once you delete this employee, you will lost this employee data permanently.') }}"
                                                        data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                        data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                                >
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="page-area d-flex justify-content-end">
                        <table>
                            <tfoot class="border-top">
                            {!! $employees->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($employees) === 0)
                        <div class="text-center p-4">
                            <img class="mb-3 w-one-cl"
                                 src="{{ asset('assets/admin') }}/svg/illustrations/sorry.svg"
                                 alt="Image Description">
                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('admin-views.employee.partials.offcanvas-filter')
    <span class="data-to-js"
          data-title="employee-list"
          data-export-route="{{ route('admin.employee.export') }}"
          data-delete-route="{{ route('admin.employee.delete', ':id') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/global.js")}}></script>
    <script src={{ asset('assets/admin/js/custom-daterange.js') }}></script>
    <script>
        "use strict";
        printFilterCount(['search', 'page']);
    </script>
    <script>
        "use strict";

        $('.js-toggle-password').each(function() {
            new HSTogglePassword(this).init()
        });
    </script>
@endpush
