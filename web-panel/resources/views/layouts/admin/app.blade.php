<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width">
    <title>@yield('title')</title>
    @php($favIcon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value)
    <link rel="shortcut icon" href="{{ $favIcon ? asset('storage/shop/' . $favIcon) : asset('assets/admin/img/160x160/img2.jpg') }}">

    {{-- Web Fonts --}}
    {{-- <link rel="stylesheet" href="{{asset('assets/admin')}}/css/google-fonts.css"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    {{-- Icon Font --}}
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/flaticon-font/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/flaticon-font/css/uicons-solid-rounded.css">

    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/vendor.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/bootstrap-select.min.css" />
    @stack('css_or_js')

    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/custom-2.css" />
    <link rel="stylesheet" href="{{ asset('assets/admin') }}/css/toastr.css">

    <link rel="stylesheet" href="{{ asset('assets/admin') }}/plugins/intl-tel-input/css/intlTelInput.css">
</head>

<body class="footer-offset">

    <div class="direction-toggle">
        <i class="tio-settings"></i>
        <span></span>
    </div>

    <div class="container">
        <div id="loading" class="d-none">
            <div class="loader-img">
                <img width="200" src="{{ asset('assets/admin/img/loader.gif') }}">
            </div>
        </div>
    </div>
    @include('layouts.admin.partials._header')
    @include('layouts.admin.partials._sidebar')
    <main id="content" role="main" class="main pointer-event">
        @yield('content')
        @include('layouts.admin.partials._footer')

        <div class="modal fade" id="popup-modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h2 class="title-new-order">
                                        <i class="tio-shopping-cart-outlined"></i>
                                        {{ \App\CPU\translate('You_have_new_order,_Check_Please') }}.
                                    </h2>
                                    <hr>
                                    <button id="checkOrderBtn"
                                        class="btn btn-primary">{{ \App\CPU\translate('Ok,_let_me_check') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--global change status modal--}}
        <div class="modal fade" id="globalChangeStatusModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-2">
                        <button type="button" class="text-dark bg-f2f2f2 rounded-circle p-1 close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="tio-clear"></i>
                        </span>
                        </button>
                    </div>
                    <div class="modal-body pt-0">
                        <form method="GET" action="">
                            <div class="text-center">
                                <img width="80" height="80" src="{{ asset('assets/admin/img/info.svg') }}" id="global-status-change-image" alt="" class="mb-4">
                                <h3 class="mb-0" id="global-status-change-title">{{ \App\CPU\translate('Are you sure') }}?</h3>
                                <p class="mt-3" id="global-status-change-description">{{ \App\CPU\translate('Want to change the status.') }}</p>
                            </div>
                            <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                                <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px" data-dismiss="modal">{{ \App\CPU\translate('No') }}</button>
                                <button type="submit" class="btn btn-danger px-4 font-weight-bold min-w-120px">{{ \App\CPU\translate('yes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.admin.modal.delete')

        <span class="image-file-size-data-to-js"
              data-max-upload-size-for-image="{{ readableUploadMaxFileSize('image') }}"
              data-max-upload-size-for-file="{{ readableUploadMaxFileSize('file') }}"
              data-post-max-size="{{ convertToReadableSize(convertToBytes(ini_get('post_max_size'))) }}"
              data-allowed-extensions="{{ IMAGE_ACCEPTED_EXTENSIONS }}"
        ></span>

    </main>
    <script src="{{ asset('assets/admin') }}/js/custom.js"></script>
    @stack('script')
    <script src="{{ asset('assets/admin') }}/js/vendor.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/theme.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/sweet_alert.js"></script>
    <script src="{{ asset('assets/admin') }}/js/toastr.js"></script>
    <script src="{{ asset('assets/admin') }}/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/ck-editor.js"></script>
    <script src="{{ asset('assets/admin') }}/js/single-image-upload.js"></script>
    <script src="{{ asset('assets/admin') }}/js/offcanvas.js"></script>
    <script src="{{ asset('assets/admin/js/single-file-size-type-validation.js') }}"></script>
    <script src="{{ asset('assets/admin/js/validate.js') }}"></script>

    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        </script>
    @endif
    {{-- <!-- Toggle Direction Init --> --}}
    <script>
        "use strict";
        $(document).on('ready', function() {
            let formSubmitSuccessfulMessage = sessionStorage.getItem('formSubmittedSuccessfully');
            if (formSubmitSuccessfulMessage)
            {
                toastr.success(formSubmitSuccessfulMessage, {
                    CloseButton: true,
                    ProgressBar: true
                });
                sessionStorage.removeItem('formSubmittedSuccessfully');
            }

            $('#checkOrderBtn').on('click', function() {
                check_order();
            });

            $(".direction-toggle").on("click", function() {
                setDirection(localStorage.getItem("direction"));
            });

            function setDirection(direction) {
                if (direction == "rtl") {
                    localStorage.setItem("direction", "ltr");
                    $("html").attr('dir', 'ltr');
                    $(".direction-toggle").find('span').text('Toggle RTL')
                } else {
                    localStorage.setItem("direction", "rtl");
                    $("html").attr('dir', 'rtl');
                    $(".direction-toggle").find('span').text('Toggle LTR')
                }
            }

            if (localStorage.getItem("direction") == "rtl") {
                $("html").attr('dir', "rtl");
                $(".direction-toggle").find('span').text('Toggle LTR')
            } else {
                $("html").attr('dir', "ltr");
                $(".direction-toggle").find('span').text('Toggle RTL')
            }

        })

        $(document).ready(function() {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });
    </script>
    {{-- <!-- Type Characters typing count --> --}}
    <script>
        $(document).ready(function() {
            $('input[maxlength], textarea[maxlength]').each(function() {
                var $this = $(this);
                var max = parseInt($this.attr('maxlength'));
                var len = $this.val().length;
                if(len > max) $this.val($this.val().substring(0, max));
                $this.siblings('.counting-box').text(Math.min(len, max) + '/' + max);
            }).on('input', function() {
                var $this = $(this);
                var max = parseInt($this.attr('maxlength'));
                var val = $this.val();
                if(val.length > max) $this.val(val.substring(0, max));
                $this.siblings('.counting-box').text($this.val().length + '/' + max);
            });
        });
    </script>
    <script src="{{ asset('assets/admin') }}/js/app-page.js"></script>

    @stack('script_2')
    <audio id="myAudio">
        <source src="{{ asset('assets/admin/sound/notification.mp3') }}" type="audio/mpeg">
    </audio>

</body>

</html>
