@extends('layouts.admin.app')

@section('title',\App\CPU\translate('profile_settings'))

@section('content')
    @php
        $canManageWorkspaceAccess = \App\CPU\Helpers::returns_user_is_master_admin();
        $workspaceAdmins = $workspaceAdmins ?? collect();
        $workspaceRoles = $workspaceRoles ?? collect();
    @endphp
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{\App\CPU\translate('settings')}}</h1>
                </div>
                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{route('admin.dashboard')}}">
                        <i class="tio-home mr-1"></i> {{\App\CPU\translate('dashboard')}}
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                            aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                            data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                            <span class="d-flex justify-content-between align-items-center">
                              <span class="h5 mb-0">{{\App\CPU\translate('admin_settings')}} </span>

                              <span class="navbar-toggle-default">
                                <i class="tio-menu-hamburger"></i>
                              </span>

                              <span class="navbar-toggle-toggled">
                                <i class="tio-clear"></i>
                              </span>
                            </span>
                    </button>
                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                            <li class="nav-item">
                                <a class="text-black-50 nav-link active" href="javascript:" id="generalSection">
                                    <i class="tio-user-outlined nav-icon"></i> {{\App\CPU\translate('basic_information')}}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="text-black-50 nav-link" href="javascript:" id="passwordSection">
                                    <i class="tio-lock-outlined nav-icon"></i> {{\App\CPU\translate('password')}}
                                </a>
                            </li>

                            @if($canManageWorkspaceAccess)
                                <li class="nav-item">
                                    <a class="text-black-50 nav-link" href="javascript:" id="workspaceAccessSection">
                                        <i class="tio-user-add-outlined nav-icon"></i> Workspace Access
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <form action="{{env('APP_MODE')!='demo'?route('admin.settings'):'javascript:'}}" method="post" enctype="multipart/form-data" id="admin-settings-form">
                @csrf
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper"></div>
                        </div>
                        <label
                            class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                            for="avatarUploader">
                            <img id="viewer"
                                 class="avatar-img"
                                 src="{{auth('admin')->user()->image_fullpath}}"
                                 alt="{{\App\CPU\translate('Image')}}">

                            <input type="file" name="image" class="js-file-attach avatar-uploader-input"
                                   id="customFileEg1"
                                   accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}">
                            <label class="avatar-uploader-trigger" for="customFileEg1">
                                <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                            </label>
                        </label>
                    </div>

                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title d-flex align-items-center g-2px h4"><i class="tio-info"></i> {{\App\CPU\translate('basic_information')}}</h2>
                        </div>

                        <div class="card-body">
                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('full_name')}} <i
                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="top"
                                        title="{{\App\CPU\translate('Display name')}}"></i></label>

                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="f_name" id="firstNameLabel"
                                               placeholder="{{\App\CPU\translate('Your first name')}}" aria-label="{{\App\CPU\translate('Your first name')}}"
                                               value="{{auth('admin')->user()->f_name}}">
                                        <input type="text" class="form-control" name="l_name" id="lastNameLabel"
                                               placeholder="{{\App\CPU\translate('Your last name')}}" aria-label="{{\App\CPU\translate('Your last name')}}"
                                               value="{{auth('admin')->user()->l_name}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('phone')}} <span
                                        class="input-label-secondary">({{\App\CPU\translate('optional')}})</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="js-masked-input form-control" name="phone" id="phoneLabel"
                                           placeholder="{{\App\CPU\translate('+x(xxx)xxx-xx-xx')}}" aria-label="{{\App\CPU\translate('+(xxx)xx-xxx-xxxxx')}}"
                                           value="{{auth('admin')->user()->phone}}"
                                           data-hs-mask-options='{
                                           "template": "+(880)00-000-00000"
                                         }'>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('email')}}</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{auth('admin')->user()->email}}"
                                           placeholder="{{\App\CPU\translate('enter_new_email_address')}}" aria-label="{{\App\CPU\translate('enter_new_email_address')}}">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" id="saveChangesBtn" class="btn btn-primary">{{\App\CPU\translate('save_changes')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="passwordDiv" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h4 class="card-title">{{\App\CPU\translate('change_your_password')}}</h4>
                    </div>

                    <div class="card-body">
                        <form id="changePasswordForm" action="{{env('APP_MODE')!='demo'?route('admin.settings-password'):'javascript:'}}" method="post"
                              enctype="multipart/form-data">
                        @csrf
                            <div class="row form-group">
                                <label for="newPassword" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('new_password')}}</label>

                                <div class="col-sm-9">
                                    <input type="password" class="js-pwstrength form-control" name="password"
                                           id="newPassword" placeholder="Enter new password"
                                           aria-label="{{\App\CPU\translate('enter_new_password')}}"
                                           data-hs-pwstrength-options='{
                                           "ui": {
                                             "container": "#changePasswordForm",
                                             "viewports": {
                                               "progress": "#passwordStrengthProgress",
                                               "verdict": "#passwordStrengthVerdict"
                                             }
                                           }
                                         }' required>

                                    <p id="passwordStrengthVerdict" class="form-text mb-2"></p>

                                    <div id="passwordStrengthProgress"></div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('confirm_password')}}
                                    </label>

                                <div class="col-sm-9">
                                    <div class="mb-3">
                                        <input type="password" class="form-control" name="confirm_password"
                                               id="confirmNewPasswordLabel" placeholder="{{\App\CPU\translate('confirm_your_new_password')}}"
                                               aria-label="{{\App\CPU\translate('confirm_your_new_password')}}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" id="saveChangesPasswordBtn" class="btn btn-primary">{{\App\CPU\translate('save_changes')}} </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($canManageWorkspaceAccess)
                    <div id="workspaceAccessDiv" class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h4 class="card-title">Workspace Access</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-soft-info mb-4">
                                Use this section to create staff accounts after a self-hosted installation. Only master admin accounts can manage workspace access.
                            </div>

                            <form action="{{ route('admin.settings.workspace-access.store') }}" method="post" class="mb-4">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="input-label">First name</label>
                                        <input type="text" class="form-control" name="workspace_f_name" placeholder="Taylor" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="input-label">Last name</label>
                                        <input type="text" class="form-control" name="workspace_l_name" placeholder="Ops" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="input-label">Email</label>
                                        <input type="email" class="form-control" name="workspace_email" placeholder="ops@company.com" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="input-label">Role</label>
                                        <select class="form-control" name="workspace_role_id" required>
                                            @foreach($workspaceRoles as $workspaceRole)
                                                <option value="{{ $workspaceRole->id }}">{{ $workspaceRole->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="input-label">Temporary password</label>
                                        <input type="password" class="form-control" name="workspace_password" placeholder="At least 8 characters" required>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <label class="input-label">Confirm password</label>
                                        <input type="password" class="form-control" name="workspace_password_confirmation" placeholder="Repeat password" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">Create workspace account</button>
                                </div>
                            </form>

                            <div class="mb-3">
                                <h5 class="mb-1">Current accounts</h5>
                                <p class="text-muted mb-0">Reset passwords here or add new staff after the first install.</p>
                            </div>

                            @foreach($workspaceAdmins as $workspaceAdmin)
                                <form action="{{ route('admin.settings.workspace-access.update', $workspaceAdmin->id) }}" method="post" class="border rounded p-3 mb-3">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label class="input-label">First name</label>
                                            <input type="text" class="form-control" name="workspace_f_name" value="{{ $workspaceAdmin->f_name }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="input-label">Last name</label>
                                            <input type="text" class="form-control" name="workspace_l_name" value="{{ $workspaceAdmin->l_name }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="input-label">Email</label>
                                            <input type="email" class="form-control" name="workspace_email" value="{{ $workspaceAdmin->email }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="input-label">Role</label>
                                            <select class="form-control" name="workspace_role_id" {{ auth('admin')->id() === $workspaceAdmin->id ? 'disabled' : '' }}>
                                                @foreach($workspaceRoles as $workspaceRole)
                                                    <option value="{{ $workspaceRole->id }}" {{ (int) $workspaceAdmin->role_id === (int) $workspaceRole->id ? 'selected' : '' }}>
                                                        {{ $workspaceRole->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if(auth('admin')->id() === $workspaceAdmin->id)
                                                <input type="hidden" name="workspace_role_id" value="{{ $workspaceAdmin->role_id }}">
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <label class="input-label">New password (optional)</label>
                                            <input type="password" class="form-control" name="workspace_password" placeholder="Leave blank to keep current password">
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <label class="input-label">Confirm password</label>
                                            <input type="password" class="form-control" name="workspace_password_confirmation" placeholder="Repeat new password">
                                        </div>
                                        <div class="col-md-6 mt-3">
                                            <div class="text-muted small">
                                                Role: <strong>{{ $workspaceAdmin->role?->name ?? 'Unassigned' }}</strong>
                                                @if(auth('admin')->id() === $workspaceAdmin->id)
                                                    · Current session
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3 mt-3 text-right">
                                            <button type="submit" class="btn btn-primary btn-block">Save account</button>
                                        </div>
                                    </div>
                                </form>

                                @if(auth('admin')->id() !== $workspaceAdmin->id)
                                    <form action="{{ route('admin.settings.workspace-access.delete', $workspaceAdmin->id) }}" method="post" class="mb-4" onsubmit="return confirm('Remove this workspace account?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Remove {{ $workspaceAdmin->email }}</button>
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src={{asset("assets/admin/js/settings.js")}}></script>
    <script src={{asset("assets/admin/js/global.js")}}></script>


    <script>
        "use strict";

        $('#saveChangesBtn').on('click', function() {
            if ("{{env('APP_MODE')}}" !== 'demo') {
                form_alert('admin-settings-form', 'Want to update info?');
            } else {
                call_demo();
            }
        });

        $('#saveChangesPasswordBtn').on('click', function() {
            if ("{{env('APP_MODE')}}" !== 'demo') {
                form_alert('changePasswordForm', 'Want to update password?');
            } else {
                call_demo();
            }
        });
    </script>

@endpush
