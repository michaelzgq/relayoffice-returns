@php
    $shopLogo = \App\Models\BusinessSetting::where(['key' => 'shop_logo'])->first()->value;
    $inspectorView = \App\CPU\Helpers::returns_user_is_inspector();
    $showInspect = \App\CPU\Helpers::admin_has_module('returns_inspect_section');
    $showCases = \App\CPU\Helpers::admin_has_module('returns_cases_section');
    $showQueue = \App\CPU\Helpers::admin_has_module('returns_queue_section');
    $showOpsBoard = \App\CPU\Helpers::admin_has_module('returns_ops_board_section');
    $showPlaybooks = \App\CPU\Helpers::admin_has_module('returns_playbooks_section');
@endphp

<div id="sidebarMain" class="d-none">
    <aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper nav-brand-back side-logo">
                    <a class="navbar-brand" href="{{ \App\CPU\Helpers::returns_home_route() }}" aria-label="{{ \App\CPU\translate('Front') }}">
                        <img
                            class="navbar-brand-logo"
                            src="{{ onErrorImage($shopLogo, asset('storage/shop') . '/' . $shopLogo, asset('assets/admin/img/160x160/img2.jpg'), 'shop/') }}"
                            alt="{{ \App\CPU\translate('logo') }}"
                        >
                    </a>
                </div>

                <div class="navbar-vertical-content">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="nav-item">
                            <small class="nav-subtitle">Returns workspace</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @if($showOpsBoard)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') || Request::is('admin/returns/dashboard') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.dashboard.index') }}" title="Ops board">
                                    <i class="fi fi-sr-apps nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Ops Board</span>
                                </a>
                            </li>
                        @endif

                        @if($showInspect)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/returns/inspect') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.inspect') }}" title="Inspect return">
                                    <i class="fi fi-sr-box-open nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Inspect</span>
                                </a>
                            </li>
                        @endif

                        @if($showCases)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/returns/cases*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.cases.index') }}" title="{{ $inspectorView ? 'My cases' : 'Cases' }}">
                                    <i class="fi fi-sr-scroll nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ $inspectorView ? 'My Cases' : 'Cases' }}</span>
                                </a>
                            </li>
                        @endif

                        @if($showQueue)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/returns/queue') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.queue.index') }}" title="Refund queue">
                                    <i class="fi fi-sr-arrow-trend-down nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Queue</span>
                                </a>
                            </li>
                        @endif

                        @if($showOpsBoard && !$inspectorView)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/returns/review-requests*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.review-requests.index') }}" title="Review requests">
                                    <i class="fi fi-sr-envelope nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Review Requests</span>
                                </a>
                            </li>
                        @endif

                        @if($showPlaybooks)
                            <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/returns/rules') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('admin.returns.rules.index') }}" title="Client playbooks">
                                    <i class="fi fi-sr-star nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">Playbooks</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>
