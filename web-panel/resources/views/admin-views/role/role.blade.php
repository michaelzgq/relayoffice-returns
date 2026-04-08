@extends('layouts.admin.app')

@section('title', \App\CPU\translate('create_role'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css" />

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
            padding-inline-start: 30px !important;
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
        <div class="">
            <div class="row align-items-center mb-3">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize">
                        <i class="tio-add-circle-outlined"></i>
                        <span>{{ \App\CPU\translate('create_role') }}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.custom-role.create') }}" method="post" enctype="multipart/form-data" id="store-or-update-data">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="">{{ \App\CPU\translate('role_name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="{{ \App\CPU\translate('add_role_name') }}">
                                        <input name="position" value="0" class="d-none">
                                        <span class="error-text" data-error="name"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex">
                                <h5 class="input-label m-0 text-capitalize">{{ \App\CPU\translate('module_permission') }} :
                                </h5>
                                <div class="check-item pb-0 w-auto">
                                    <div class="form-group form-check form--check m-0 ml-2">
                                        <input type="checkbox" name="modules[]" value="account" class="form-check-input"
                                            id="select-all">
                                        <label class="form-check-label ml-2"
                                            for="select-all">{{ \App\CPU\translate('Select_All') }}</label>
                                    </div>
                                </div>
                            </div>
                            <span class="error-text justify-content-start" data-error="modules"></span>
                            <div class="check--item-wrapper">
                                @foreach ($modules as $module)
                                    <div class="check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="modules[]" value="{{ $module }}"
                                                class="form-check-input" id="{{ $module }}">
                                            <label class="form-check-label ml-2 ml-sm-3  text-dark"
                                                for="{{ $module }}">{{ ucwords(str_replace('_', ' ', $module)) }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pt-4">
                                <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <div class="w-100">
                            <div class="row">
                                <div class="col-12 col-sm-4 col-md-6 col-lg-7 col-xl-8">
                                    <h5>{{ \App\CPU\translate('role_table') }}
                                        <span class="badge badge-soft-dark">{{ $roles->total() }}</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table class="table table-thead-bordered border-bottom table-nowrap table-align-middle card-table title">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="w-50px">{{ \App\CPU\translate('sl') }}</th>
                                    <th scope="col" class="w-50px">{{ \App\CPU\translate('Employee_Role_List') }}</th>
                                    <th scope="col" class="w-200px">{{ \App\CPU\translate('modules') }}</th>
                                    <th scope="col" class="w-50px">{{ \App\CPU\translate('status') }}</th>
                                    <th scope="col" class="text-center w-50px">{{ \App\CPU\translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="set-rows">
                                @forelse($roles as $k => $role)
                                    <tr>
                                        <td scope="row">{{ $k + $roles->firstItem() }}</td>
                                        <td>{{ Str::limit($role['name'], 25, '...') }}</td>
                                        <td class="text-capitalize">
                                            <div class="min-w-340 d-flex flex-wrap gap-2">
                                                @if ($role['modules'] != null)
                                                    @foreach ((array) json_decode($role['modules']) as $key => $m)
                                                        <span class="badge badge-soft-success">
                                                            {{ str_replace('_', ' ', $m) }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox" class="toggle-switch-input change-status"
                                                    data-route="{{ route('admin.custom-role.status', [$role['id'], $role->status ? 0 : 1]) }}"
                                                    class="toggle-switch-input" {{ $role->status ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-3">
                                                <a href="{{ route('admin.custom-role.edit', [$role['id']]) }}"
                                                        class="btn btn-outline-primary icon-btn offcanvas-toggle"
                                                        aria-label="Edit">
                                                    <i class="fi fi-sr-pencil"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-outline-danger icon-btn delete-resource"
                                                        data-id="{{ $role['id'] }}"
                                                        data-target="#deleteModal"
                                                        data-toggle="modal"
                                                        data-title="{{ \App\CPU\translate('Are you sure to delete this role') }}?"
                                                        data-subtitle="{{ \App\CPU\translate('If once you delete this role, you will lost this role data permanently.') }}"
                                                        data-cancel-text="{{ \App\CPU\translate('No') }}"
                                                        data-confirm-text="{{ \App\CPU\translate('Delete') }}"
                                                >
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">
                                            <img class="mb-3 img-one-in" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{\App\CPU\translate('Image Description')}}">
                                            <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {!! $roles->links('layouts/admin/pagination/_pagination', ['perPage' => request()->get('per_page')]) !!}
                </div>
            </div>
        </div>
    </div>
    <span class="data-to-js"
          data-delete-route="{{ route('admin.custom-role.delete', ':id') }}"
    ></span>
@endsection

@push('script_2')
    <script src={{ asset('assets/admin/js/global.js') }}></script>

    <script>
        const selectAll = document.getElementById('select-all');
        const items = document.querySelectorAll('.check--item-wrapper .check-item .form-check-input');

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
