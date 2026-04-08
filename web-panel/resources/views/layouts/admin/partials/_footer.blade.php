<!-- <div class="footer">
    <div class="row justify-content-between align-items-center">
        <div class="col">
            <p class="font-size-sm mb-0">
                @php($shop_name=\App\Models\BusinessSetting::where('key','shop_name')->first()->value)
                @php($footer_text=\App\Models\BusinessSetting::where('key','footer_text')->first()->value)
                &copy; {{ $shop_name }}. <span
                    class="d-none d-sm-inline-block">{{ $footer_text }}</span>
            </p>
        </div>
        <div class="col-auto">
            <div class="d-flex justify-content-end">
                <ul class="list-inline list-separator">
                    <li class="list-inline-item">
                        <a class="list-separator-link" href="{{route('admin.business-settings.shop-setup')}}">{{\App\CPU\translate('settings')}}</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="list-separator-link" href="{{route('admin.settings')}}">{{\App\CPU\translate('profile')}}</a>
                    </li>
                    <li class="list-inline-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                               href="{{route('admin.dashboard')}}">
                                <i class="tio-home-outlined"></i> 
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> -->


<div class="footer">
    <div class="d-flex pb-2 flex-wrap gap-2 justify-content-md-between justify-content-center text-md-start text-center align-items-center">
        <div class="">
            <p class="font-size-sm text-clr mb-0">
                @php($shop_name=\App\Models\BusinessSetting::where('key','shop_name')->first()->value)
                @php($footer_text=\App\Models\BusinessSetting::where('key','footer_text')->first()->value)
                &copy; {{ $shop_name }}. <span
                    class="d-none d-sm-inline-block">{{ $footer_text }}</span>
            </p>
        </div>
        <div class="">
            <div class="d-flex justify-content-end">
                <ul class="list-inline list-separator d-flex justify-content-center align-items-center gap-x-24 flex-wrap">
                    <li class="">
                        <a class="text-black d-flex align-items-center gap-2 hover-primary fs-14" href="{{route('admin.business-settings.shop-setup')}}">
                            <i class="tio-settings"></i>
                            {{\App\CPU\translate('settings')}}
                        </a>
                    </li>
                    <li class="">
                        <a class="text-black d-flex align-items-center gap-2 hover-primary fs-14" href="{{route('admin.settings')}}">
                            <i class="tio-user"></i> 
                            {{\App\CPU\translate('profile')}}
                        </a>
                    </li>
                    <li class="">
                        <a class="text-black d-flex align-items-center gap-2 hover-primary fs-14" href="{{route('admin.dashboard')}}">
                            <i class="tio-home-outlined"></i> 
                            {{\App\CPU\translate('Home')}}
                        </a>
                    </li>
                    <li>
                        <div class="btn py-1 px-3 badge-soft-primary">
                            Version 5.7.0
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>