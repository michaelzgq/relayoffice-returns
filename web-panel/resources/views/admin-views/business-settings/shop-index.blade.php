@php use App\CPU\Helpers;use App\Models\Currency;use function App\CPU\translate; @endphp
@extends('layouts.admin.app')
@section('title', translate('shop_setup'))
@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title d-flex align-items-center g-2px text-capitalize">{{ translate('shop_setup') }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('admin.business-settings.update-setup') }}" method="post"
                      enctype="multipart/form-data" id="store-or-update-data">
                    <div class="card mb-20">
                        <div class="card-header">
                            <div>
                                <h3 class="mb-1">{{ translate('Basic Information') }}</h3>
                                <p class="fs-12 mb-0">{{ translate('Here you setup your all shop information.') }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card-custom-xl">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                @php($shopName = \App\Models\BusinessSetting::where('key', 'shop_name')->first()->value)
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="exampleFormControlInput1">{{ translate('shop_name') }}
                                                        <span class="text-danger">*</span> </label>
                                                    <input type="text" name="shop_name" value="{{ $shopName }}"
                                                           class="form-control"
                                                           placeholder="{{ translate('shop_name') }}">
                                                    <span class="error-text" data-error="shop_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                @php($shopEmail = \App\Models\BusinessSetting::where('key', 'shop_email')->first()->value)
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="exampleFormControlInput1">{{ translate('email') }}
                                                        <span class="text-danger">*</span> </label>
                                                    <input type="email" name="shop_email" value="{{ $shopEmail }}"
                                                           class="form-control"
                                                           placeholder="{{ translate('shop_email') }}"
                                                    >
                                                    <span class="error-text" data-error="shop_email"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                @php($shopPhone = \App\Models\BusinessSetting::where('key', 'shop_phone')->first()->value)
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="exampleFormControlInput1">{{ translate('phone') }}
                                                        <span class="text-danger">*</span> </label>
                                                    <input type="tel" name="shop_phone" value="{{ $shopPhone }}"
                                                           class="form-control"
                                                           placeholder="{{ translate('shop_phone') }}"
                                                    >
                                                    <span class="error-text" data-error="shop_phone"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="country">{{ translate('country') }}</label>
                                                    <select id="country" name="country"
                                                            class="form-control  js-select2-custom">
                                                        @foreach(COUNTRY_LIST as $code => $country)
                                                            <option value="{{ $code }}"
                                                                    @if (Helpers::get_business_settings('country') == $code) selected @endif>{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="error-text" data-error="country"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                @php($shopAddress = \App\Models\BusinessSetting::where('key', 'shop_address')->first()->value)
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="exampleFormControlInput1">{{ translate('shop_address') }}
                                                        <span class="text-danger">*</span> </label>
                                                    <input type="text" name="shop_address" value="{{ $shopAddress }}"
                                                           class="form-control"
                                                           placeholder="{{ translate('shop_address') }}"
                                                    >
                                                    <span class="error-text" data-error="shop_address"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                @php($paginationLimit = \App\Models\BusinessSetting::where('key', 'pagination_limit')->first()->value)
                                                <div class="form-group mb-20">
                                                    <label class="input-label mb-10px text-blck text-capitalize"
                                                           for="exampleFormControlInput1">{{ translate('pagination_limit') }}
                                                        <span class="text-danger">*</span> </label>
                                                    <input min="1" type="number" name="pagination_limit" value="{{ $paginationLimit }}"
                                                           class="form-control"
                                                           placeholder="{{ translate('pagination_limit') }}">
                                                    <span class="error-text" data-error="pagination_limit"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="bg-fafafa rounded p-20 mb-20">
                                        <div class="py-3">
                                            @php($shopLogo = \App\Models\BusinessSetting::where('key', 'shop_logo')->first()->value)
                                            <!-- New -->
                                            <div class="form-group m-0">
                                                <div class="mb-20">
                                                    <h5 class="mb-1">{{ translate('Upload_Image') }} <span class="text-danger">*</span></h5>
                                                    <p class="mb-0 fs-12">{{ translate('Upload your Shop Logo') }}</p>
                                                </div>
                                                <div class="text-center">
                                                    <label class="upload-file">
                                                        <input type="file" name="shop_logo" id="customFileEg1"
                                                               class="upload-file-input"
                                                               accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}"
                                                               data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                                                        @if(!empty($shopLogo))
                                                            <button type="button" class="edit_btn btn btn-primary">
                                                                <i class="fi fi-sr-pencil"></i>
                                                            </button>
                                                        @endif
                                                        <div class="upload-file-wrapper border-cus w-300">
                                                            <div
                                                                class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                                <div
                                                                    class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                                    <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                                    <p class="fs-10 mb-0">{{ translate('Add_image') }}</p>
                                                                </div>
                                                            </div>
                                                            <img class="upload-file-img fit_contain" id="shopLogoViewer"
                                                                 loading="lazy"
                                                                 src="{{onErrorImage($shopLogo,asset('storage/shop').'/' . $shopLogo, '' ,'shop/')}}"
                                                                 data-default-src="{{onErrorImage($shopLogo,asset('storage/shop').'/' . $shopLogo, '' ,'shop/')}}"
                                                                 alt="">
                                                        </div>
                                                    </label>
                                                </div>
                                                <p class="mb-0 text-center fs-12 mt-20">{{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}
                                                    <span
                                                        class="fw-bold">(3:1)</span></p>
                                                <span class="error-text" data-error="shop_logo"></span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="old_shop_logo" id="oldShopLogo"
                                               value="{{ $shopLogo }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="bg-fafafa rounded p-20">
                                        <div class="py-3">
                                            @php($favIcon = \App\Models\BusinessSetting::where('key', 'fav_icon')->first()->value)
                                            <!-- New -->
                                            <div class="form-group m-0">
                                                <div class="mb-20">
                                                    <h5 class="mb-1">{{ translate('Favicon') }} <span class="text-danger">*</span></h5>
                                                    <p class="mb-0 fs-12">{{ translate('Upload your website favicon') }}</p>
                                                </div>
                                                <div class="text-center">
                                                    <label class="upload-file">
                                                        <input type="file" name="fav_icon" id="customFileEg2"
                                                               class="upload-file-input"
                                                               accept="{{ IMAGE_ACCEPTED_EXTENSIONS }}"
                                                               data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                                                        @if(!empty($favIcon))
                                                            <button type="button" class="edit_btn btn btn-primary">
                                                                <i class="fi fi-sr-pencil"></i>
                                                            </button>
                                                        @endif

                                                        <div class="upload-file-wrapper border-cus w-100px">
                                                            <div
                                                                class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                                                <div
                                                                    class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                                                    <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                                                    <p class="fs-10 mb-0">{{ translate('Add_image') }}</p>
                                                                </div>
                                                            </div>
                                                            <img class="upload-file-img p-1 fit_contain"
                                                                 id="favIconViewer"
                                                                 loading="lazy"
                                                                 src="{{onErrorImage($favIcon, asset('storage/shop').'/' . $favIcon, '' ,'shop/')}}"
                                                                 data-default-src="{{onErrorImage($favIcon, asset('storage/shop').'/' . $favIcon, '' ,'shop/')}}"
                                                                 alt="">
                                                        </div>
                                                    </label>
                                                </div>
                                                <p class="mb-0 text-center fs-12 mt-20">{{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}
                                                    <span
                                                        class="fw-bold">(1:1)</span></p>
                                                <span class="error-text" data-error="fav_icon"></span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="old_fav_icon" id="oldFavIcon" value="{{ $favIcon }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <h3 class="mb-1">{{ translate('General Settings') }}</h3>
                                <p class="fs-12 mb-0">{{ translate('Here you setup your all shop general settings') }}</p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-20">
                                    <div class="card-custom-xl">
                                        <div class="mb-20">
                                            <h4 class="mb-1">{{ translate('Time Setup') }}</h4>
                                            <p class="fs-12 mb-0">{{ translate('Setup your shop time zone and format from here') }}</p>
                                        </div>
                                        <div class="bg-fafafa rounded p-xl-20 p-3">
                                            <div class="row">
                                                <div class="col-sm-12 mb-3 mb-lg-2">
                                                    <div class="form-group">
                                                        <label class="input-label mb-10px text-blck text-capitalize"
                                                               for="exampleFormControlInput1">{{ translate('time_zone') }}
                                                            <span class="text-danger">*</span></label>
                                                        <select name="time_zone" id="time_zone"
                                                                data-maximum-selection-length="3"
                                                                class="form-control js-select2-custom">
                                                            @foreach(getTimeZones() as $timeZone)
                                                                <option value="{{ $timeZone['id'] }}"
                                                                        @if (Helpers::get_business_settings('time_zone') == $timeZone['id']) selected @endif>{{ $timeZone['text'] }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="error-text" data-error="time_zone"></span>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <div
                                                            class="input-label mb-10px text-blck text-capitalize d-block">{{ translate('Time Format') }}
                                                            <span class="text-danger">*</span></div>
                                                        <div
                                                            class="d-flex rounded border py-2 px-3 bg-white align-items-center m-0">
                                                            <div class="form-group w-100 form-check form--check m-0">
                                                                <input type="radio" name="time_format" id="12Hour"
                                                                       value="12Hour"
                                                                       {{ empty(Helpers::get_business_settings('time_format')) || Helpers::get_business_settings('time_format') == '12Hour' ? 'checked' : '' }}
                                                                       class="form-check-input">
                                                                <label
                                                                    class="form-check-label mb-0 ml-1 text-dark fs-12"
                                                                    for="12Hour">{{ translate('12 hours') }}</label>
                                                            </div>
                                                            <div class="form-group w-100 form-check form--check m-0">
                                                                <input type="radio" name="time_format" id="24Hour"
                                                                       value="24Hour"
                                                                       {{ Helpers::get_business_settings('time_format') == '24Hour' ? 'checked' : '' }}
                                                                       class="form-check-input">
                                                                <label
                                                                    class="form-check-label mb-0 ml-1 text-dark fs-12"
                                                                    for="24Hour">{{ translate('24 hours') }}</label>
                                                            </div>
                                                        </div>
                                                        <span class="error-text" data-error="time_format"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-20">
                                    <div class="card-custom-xl">
                                        <div class="mb-20">
                                            <h4 class="mb-1">{{ translate('Currency Setup') }}</h4>
                                            <p class="fs-12 mb-0">{{ translate('Setup your shop time zone and format from here') }}</p>
                                        </div>
                                        <div class="bg-fafafa rounded p-xl-20 p-3">
                                            <div class="row">
                                                <div class="col-sm-12 mb-3 mb-lg-2">
                                                    @php($currencyCode = \App\Models\BusinessSetting::where('key', 'currency')->first()->value)
                                                    <div class="form-group">
                                                        <label class="input-label mb-10px text-blck text-capitalize"
                                                               for="exampleFormControlInput1">{{ translate('currency') }}
                                                            <span class="text-danger">*</span></label>
                                                        <select name="currency" class="form-control js-select2-custom">
                                                            @foreach (Currency::orderBy('currency_code')->get() as $currency)
                                                                <option value="{{ $currency['currency_code'] }}"
                                                                    {{ $currencyCode == $currency['currency_code'] ? 'selected' : '' }}>
                                                                    {{ $currency['currency_code'] }}
                                                                    ({{ $currency['currency_symbol'] }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span class="error-text" data-error="currency"></span>
                                                    </div>
                                                    @php($currencySymbol = Currency::firstWhere('currency_code', Helpers::get_business_settings('currency'))?->currency_symbol ?? '$')
                                                    <div class="form-group mb-0">
                                                        <label class="input-label mb-10px text-blck text-capitalize"
                                                               for="">{{ translate('Currency symbol position') }}
                                                            <span class="text-danger">*</span></label>
                                                        <select name="currency_symbol_position"
                                                                class="form-control js-select2-custom">
                                                            <option
                                                                value="left" {{ empty(Helpers::get_business_settings('currency_symbol_position')) || Helpers::get_business_settings('currency_symbol_position') == 'left' ? 'selected' : '' }}>
                                                                ({{ $currencySymbol }}) {{ translate('left') }}</option>
                                                            <option
                                                                value="right" {{ Helpers::get_business_settings('currency_symbol_position') == 'right' ? 'selected' : '' }}>{{ translate('right') }}
                                                                ({{ $currencySymbol }})
                                                            </option>
                                                        </select>
                                                        <span class="error-text"
                                                              data-error="currency_symbol_position"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-20">
                                    <div class="card-custom-xl">
                                        <div class="mb-20">
                                            <h4 class="mb-1">{{ translate('VAT Setup') }}</h4>
                                            <p class="fs-12 mb-0">{{ translate('Setup your shop VAT setup  from here') }}</p>
                                        </div>
                                        <div class="bg-fafafa rounded p-xl-20 p-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    @php($vatRegNo = \App\Models\BusinessSetting::where(['key' => 'vat_reg_no'])->first()->value)
                                                    <div class="form-group">
                                                        <label
                                                            class="input-label mb-10px text-blck text-capitalize">{{ translate('vat_reg_no') }}
                                                            <span class="text-danger">*</span></label>
                                                        <input class="form-control" type="text" name="vat_reg_no"
                                                               placeholder="{{ translate('vat_reg_no') }}"
                                                               value="{{ $vatRegNo }}">
                                                        <span class="error-text" data-error="vat_reg_no"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-20">
                                    <div class="card-custom-xl">
                                        <div class="mb-20">
                                            <h4 class="mb-1">{{ translate('Footer Content') }}</h4>
                                            <p class="fs-12 mb-0">{{ translate('Setup your shop footer content from here') }}</p>
                                        </div>
                                        <div class="bg-fafafa rounded p-xl-20 p-3">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    @php($footerText = \App\Models\BusinessSetting::where('key', 'footer_text')->first()->value)
                                                    <div class="form-group mb-0">
                                                        <label class="input-label mb-10px text-blck text-capitalize"
                                                               for="exampleFormControlInput1">{{ translate('footer_text') }}
                                                            <span class="text-danger">*</span></label>
                                                        <input type="text" name="footer_text" maxlength="100"
                                                               value="{{ $footerText }}"
                                                               class="form-control"
                                                               placeholder="{{ translate('footer_text') }}"
                                                               required>
                                                        <p class="counting-box text-end text-black-50 mb-0 mt-1">
                                                            0/100</p>
                                                    </div>
                                                    <span class="error-text" data-error="footer_text"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-20 gap-3">
                        <button type="reset" class="btn btn-secondary min-w-90 min-w-lg-120"
                                href="javascript:">{{ translate('reset') }}</button>
                        <button type="submit"
                                class="btn btn-primary min-w-90 min-w-lg-120">{{ translate('submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src={{ asset('assets/admin/js/global.js') }}></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDiDGVR1GnClPIXcsOOvAniamtAmS-MHjY&libraries=places&v=3.51"></script>


    <script>
        $(document).ready(function () {
            function initAutocomplete() {
                console.log('sdkf')
                var myLatLng = {

                    lat: 23.811842872190343,
                    lng: 90.356331
                };
                const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                    center: {
                        lat: 23.811842872190343,
                        lng: 90.356331
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                });

                marker.setMap(map);
                var geocoder = geocoder = new google.maps.Geocoder();
                google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {
                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];


                    geocoder.geocode({
                        'latLng': latlng
                    }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                document.getElementById('address').innerHtml = results[1].formatted_address;
                            }
                        }
                    });
                });

                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });
                let markers = [];

                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    markers.forEach((marker) => {
                        marker.setMap(null);
                    });
                    markers = [];

                    const bounds = new google.maps.LatLngBounds();
                    places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var mrkr = new google.maps.Marker({
                            map,
                            title: place.name,
                            position: place.geometry.location,
                        });
                        google.maps.event.addListener(mrkr, "click", function (event) {
                            document.getElementById('latitude').value = this.position.lat();
                            document.getElementById('longitude').value = this.position.lng();
                        });

                        markers.push(mrkr);

                        if (place.geometry.viewport) {

                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            };
            initAutocomplete();
        });
    </script>


    <script>
        "use strict";
        $(document).on('ready', function () {
            @php($country = \App\Models\BusinessSetting::where('key', 'country')->first()->value)
            $("#country option[value='{{ $country }}']").attr('selected', 'selected').change();
        });
        @php($time_zone = \App\Models\BusinessSetting::where('key', 'time_zone')->first())
        @php($time_zone = $time_zone->value ?? null)
        $('[name=time_zone]').val("{{ $time_zone }}");

        function enforceMinMax(el) {
            if (el.value != "") {
                if (parseInt(el.value) < parseInt(el.min)) {
                    el.value = el.min;
                }
                if (parseInt(el.value) > parseInt(el.max)) {
                    el.value = el.max;
                }
            }
        }

        $(function () {
            $("#pagination_limit").keydown(function () {
                // Save old value.
                if (!$(this).val() || parseInt($(this).val()) >= 0)
                    $(this).data("old", $(this).val());
            });
            $("#pagination_limit").keyup(function () {
                // Check correct, else revert back to old value.
                if (!$(this).val() || parseInt($(this).val()) >= 0)
                    ;
                else
                    $(this).val($(this).data("old"));
            });
        });

        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#' + viewer).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });

        $("#customFileEg2").change(function () {
            readURL(this, 'viewer2');
        });

        $(document).ready(function(){
            $('.edit_btn').on('click', function(e){
                e.preventDefault();
                e.stopPropagation();

                $(this).closest('.upload-file').find('.upload-file-input').trigger('click')
            });
        });
    </script>
@endpush
