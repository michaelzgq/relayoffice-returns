@extends('layouts.admin.app')
@section('title',\App\CPU\translate('custom_role'))
@push('css_or_js')
    <style>
        .check--item-wrapper {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin: 30px -5px -30px -10px;
        }
        .check-item {
            width: 50%;
            max-width: 248px;
            padding: 0 5px 30px;
        }
        .form--check {
            padding-inline-start: 30px!important;
            cursor: pointer;
            margin-bottom: 0;
            position: relative;
        }
        .form-check-input {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <h1 class="page-header-title mb-2 text-capitalize">
                <i class="tio-edit"></i>
                <span>{{\App\CPU\translate('Employee_Role')}}</span>
            </h1>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.custom-role.update',[$role['id']])}}" method="post" id="store-or-update-data">
                            <div class="lang_form">
                                <div class="form-group">
                                    <label class="input-label " for="name">{{\App\CPU\translate('role_name')}}</label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{$role?->name}}"
                                           placeholder="{{\App\CPU\translate('role_name')}}" >
                                    <span class="error-text" data-error="name"></span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <h5 class="input-label m-0 text-capitalize">{{ \App\CPU\translate('module_permission')}} : </h5>
                                <div class="check-item pb-0 w-auto">
                                    <div class="form-group form-check form--check m-0 ml-2">
                                        <input type="checkbox" name="modules[]" value="account" class="form-check-input"
                                               id="select-all">
                                        <label class="form-check-label ml-2" for="select-all">{{ \App\CPU\translate('Select_All') }}</label>
                                    </div>
                                </div>
                            </div>
                            <span class="error-text justify-content-start" data-error="modules"></span>
                            <hr>
                            <div class="row check--item-wrapper">
                                @foreach($modules as $module)
                                    <div class="check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="modules[]" value="{{ $module }}" class="form-check-input" {{ in_array($module, (array)json_decode($role['modules'])) ? 'checked' : '' }} id="{{ $module }}">
                                            <label class="form-check-label ml-2 ml-sm-3 text-dark" for="{{ $module }}">{{ ucwords(str_replace('_', ' ', $module)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pt-4">
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
    <script src={{ asset('assets/admin/js/global.js') }}></script>

    <script>
        const selectAll = document.getElementById('select-all');
        const items = document.querySelectorAll('.check--item-wrapper .check-item .form-check-input');

        function updateSelectAll() {
            selectAll.checked = [...items].length > 0 && [...items].every(i => i.checked);
        }

        // Initialize "Select All" checkbox state on page load
        updateSelectAll();

        selectAll.addEventListener('change', function() {
            items.forEach(item => {
                item.checked = this.checked;
            });
        });

        // Handle individual checkboxes
        items.forEach(item => {
            item.addEventListener('change', function() {
                selectAll.checked = [...items].every(i => i.checked);
            });
        });
    </script>
@endpush

