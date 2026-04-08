<div id="headerMain" class="d-none">
    @php
        $canInspect = \App\CPU\Helpers::admin_has_module('returns_inspect_section');
        $canViewCases = \App\CPU\Helpers::admin_has_module('returns_cases_section');
        $canManageQueue = \App\CPU\Helpers::admin_has_module('returns_queue_section');
        $inspectorView = \App\CPU\Helpers::returns_user_is_inspector();
    @endphp
    <header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered header-style">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                @php($shop_logo=\App\Models\BusinessSetting::where(['key'=>'shop_logo'])->first()->value)
                <a class="navbar-brand" href="{{ \App\CPU\Helpers::returns_home_route() }}" aria-label="">
                    <img class="navbar-brand-logo"
                         src="{{onErrorImage($shop_logo,asset('storage/shop').'/' . $shop_logo,asset('assets/admin/img/160x160/img2.jpg') ,'shop/')}}" alt="{{\App\CPU\translate('Logo')}}">
                </a>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <div class="m-2 m-xl-0">
                    <div class="navbar-nav-wrap-content-left">
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="fi fi-sr-angle-double-right navbar-vertical-aside-toggle-full-align text-dark" data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                               data-toggle="tooltip" data-placement="right" title="{{\App\CPU\translate('Expand')}}"></i>
                        </button>
                    </div>

                    <div class="navbar-nav-wrap-content-right">
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle-short-align border-0 bg-transparent fs-16">
                            <i class="fi fi-sr-angle-double-left" data-toggle="tooltip" data-placement="right" data-original-title="Collapse"></i>
                        </button>
                    </div>
                </div>

{{--                <nav aria-label="breadcrumb">--}}
{{--                    <ol class="breadcrumb mb-0">--}}
{{--                      <li class="breadcrumb-item"><a href="#" class="text-muted">{{ request()->segment(2) }}</a></li>--}}
{{--                        @if(request()->segment(3))--}}
{{--                            <li class="breadcrumb-item font-weight-bold active" aria-current="page"> {{  \App\CPU\translate(request()->segment(3)) }} </li>--}}
{{--                        @endif--}}
{{--                        @if(request()->segment(4))--}}
{{--                            <li class="breadcrumb-item font-weight-bold active" aria-current="page"> {{ \App\CPU\translate(request()->segment(4)) }} </li>--}}
{{--                        @endif--}}
{{--                    </ol>--}}
{{--                </nav>--}}
            </div>

            <div class="navbar-nav-wrap-content-right">
                <ul class="navbar-nav align-items-center flex-row">
                    @if($canInspect)
                        <li class="nav-item d-sm-inline-block">
                            <a class="js-hs-unfold-invoker btn btn-soft-primary rounded-full" href="{{ route('admin.returns.inspect') }}">
                                <span>{{ Request::is('admin/returns/inspect') ? 'Inspection' : 'New Inspection' }}</span>
                            </a>
                        </li>
                    @endif

                    @if($canManageQueue)
                        <li class="nav-item d-sm-inline-block">
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn btn-icon btn-soft-secondary rounded-circle"
                                   href="{{ route('admin.returns.queue.index') }}"
                                   title="Refund queue">
                                    <i class="tio-receipt-outlined"></i>
                                </a>
                            </div>
                        </li>
                    @elseif($canViewCases)
                        <li class="nav-item d-sm-inline-block">
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn btn-icon btn-soft-secondary rounded-circle"
                                   href="{{ route('admin.returns.cases.index') }}"
                                   title="{{ $inspectorView ? 'My cases' : 'Cases' }}">
                                    <i class="tio-view-list"></i>
                                </a>
                            </div>
                        </li>
                    @endif
                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper p-0" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         src="{{auth('admin')->user()->image_fullpath}}"
                                         alt="{{\App\CPU\translate('image_description')}}">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account">
                                <div class="dropdown-item-text">
                                    <div class="media align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                 src="{{auth('admin')->user()->image_fullpath}}"
                                                 alt="{{\App\CPU\translate('image_description')}}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('admin')->user()->f_name}}</span>
                                            <span class="card-text">{{auth('admin')->user()->email}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('admin.settings')}}">
                                    <span class="text-truncate pr-2"
                                          title="{{\App\CPU\translate('settings')}}">{{\App\CPU\translate('settings')}}</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:" id="logoutLink">
                                    <span class="text-truncate pr-2" title="Sign out">{{\App\CPU\translate('sign_out')}}</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>

@push('script_2')
<script>
    "use strict";

    $(document).on('click', '#logoutLink', function(e) {
        e.preventDefault();

        Swal.fire({
            title: '{{\App\CPU\translate('Do you want to logout')}}?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: '#11488a',
            cancelButtonColor: '#363636',
            confirmButtonText: `{{\App\CPU\translate('Yes')}}`,
            denyButtonText: `{{\App\CPU\translate('Don\'t Logout')}}'`,
        }).then((result) => {
            if (result.value) {
                window.location.href = '{{route('admin.auth.logout')}}';
            } else {
                Swal.fire('{{\App\CPU\translate('Canceled')}}', '', '{{\App\CPU\translate('info')}}');
            }
        });
    });
</script>
@endpush
