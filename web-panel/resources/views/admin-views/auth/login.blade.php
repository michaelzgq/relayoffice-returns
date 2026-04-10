<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    @php($workspaceName = \App\Models\BusinessSetting::where(['key' => 'shop_name'])->first()?->value ?: (config('app.name') === 'Laravel' ? 'Dossentry' : config('app.name')))
    <title>{{ $workspaceName }} | Workspace Login</title>
    @php($favIcon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value)
    <link rel="shortcut icon" href="{{ $favIcon ? asset('storage/shop/' . $favIcon) : asset('assets/admin/img/160x160/img2.jpg') }}">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/google-fonts.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/auth-page.css">

</head>

<body class="bg-one-auth">
<main id="content" role="main" class="main">
                @php($isPublicDemoHost = $isPublicDemoHost ?? \App\CPU\Helpers::dossentry_is_public_demo_host(request()->getHost()))
                @php($isInternalAdminHost = $isInternalAdminHost ?? \App\CPU\Helpers::dossentry_is_internal_admin_host(request()->getHost()))
                @php($internalAdminLoginUrl = $internalAdminLoginUrl ?? \App\CPU\Helpers::dossentry_internal_admin_login_url())
                @php($guestDemoLoginUrl = $guestDemoLoginUrl ?? \App\CPU\Helpers::dossentry_guest_demo_login_url())
                @php($guestDemo = $guestDemo ?? config('dossentry.guest_demo'))
                @php($workspaceNotice = request('notice'))

                @php($shop_logo=\App\Models\BusinessSetting::where(['key'=>'shop_logo'])->first()->value)
    <div class="auth-wrapper">
        <div class="auth-wrapper-left" style="background: url('{{asset('/assets/admin/img/auth-bg.png')}}') no-repeat center left/cover">
            <div class="auth-left-cont">
                <img class="onerror-image"
                    src="{{onErrorImage($shop_logo,asset('storage/shop').'/' . $shop_logo,asset('assets/admin/img/160x160/img2.jpg') ,'shop/')}}"
                    alt="{{\App\CPU\translate('Logo')}}">
                <h2 class="title">
                    <span class="d-block text-primary">{{ $workspaceName }}</span>
                    <strong class="color-EC255A">Brand-ready return evidence and decision workflows.</strong>
                </h2>
            </div>
        </div>
        <div class="auth-wrapper-right">
            <label class="badge badge-soft-danger __login-badge color-EC255A">
                {{\App\CPU\translate('Software version')}}: {{ env('SOFTWARE_VERSION') }}
            </label>

            <div class="auth-wrapper-form">

                <form class="js-validate" action="{{route('admin.auth.login')}}" method="post" id="form-id">
                @csrf
                    <div class="auth-header">
                        <div class="mb-5">
                            <h2 class="title">Enter the workspace</h2>
                            <div>Sign in to review cases, decision queues, and Brand Review links.</div>
                        </div>
                    </div>

                    @if($isPublicDemoHost)
                        <div class="alert alert-soft-info mb-4">
                            <strong>Shared guest demo only.</strong>
                            This public demo workspace only accepts the guest demo account.
                            <div class="mt-2 small">
                                Demo login: <code>{{ $guestDemo['email'] ?? 'guest@dossentry.com' }}</code> /
                                <code>{{ $guestDemo['password'] ?? '12345678' }}</code>
                            </div>
                            <div class="mt-2 small">
                                Internal team members should use the staff workspace:
                                <a href="{{ $internalAdminLoginUrl }}">staff login</a>
                            </div>
                        </div>
                    @elseif($isInternalAdminHost)
                        <div class="alert alert-soft-warning mb-4">
                            <strong>Internal staff workspace.</strong>
                            Shared demo users should sign in at
                            <a href="{{ $guestDemoLoginUrl }}">the guest demo workspace</a>.
                        </div>
                    @endif

                    @if($workspaceNotice === 'internal-workspace-only')
                        <div class="alert alert-soft-warning mb-4">
                            Staff accounts are blocked on the shared demo host. Use the staff workspace instead.
                        </div>
                    @elseif($workspaceNotice === 'guest-demo-only')
                        <div class="alert alert-soft-info mb-4">
                            The guest demo account is only available on the shared demo workspace.
                        </div>
                    @endif

                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize" for="signinSrEmail">{{ \App\CPU\translate('Your email') }}</label>
                        <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                                tabindex="1" placeholder="{{\App\CPU\translate('email@address.com')}}"
                                aria-label="{{\App\CPU\translate('email@address.com')}}"
                                required
                                data-msg="{{\App\CPU\translate('Please_enter_a_valid_email_address.')}}">
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                                {{ \App\CPU\translate('Password') }}
                            </span>
                        </label>
                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                    name="password" id="signupSrPassword"
                                    placeholder="{{\App\CPU\translate('8+ characters required')}}"
                                    aria-label="{{\App\CPU\translate('8+ characters required')}}" required
                                    data-msg="{{\App\CPU\translate('Your password is invalid. Please try again.')}}"
                                    data-hs-toggle-password-options='{
                                                "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                    }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        @php($recaptcha = \App\CPU\Helpers::get_business_settings('recaptcha'))
                        @if(isset($recaptcha) && $recaptcha['status'] == 1)
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                            <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0" >
                            <div class="row d-none" id="reload-captcha">
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-lg" name="default_captcha_value" value=""
                                           placeholder="{{\App\CPU\translate('Enter captcha value')}}" autocomplete="off">
                                </div>
                                <div class="col-6">
                                    <a>
                                        <img src="{{ URL('/admin/auth/code/captcha/1') }}" class="input-field rounded h-54px"
                                             id="default_recaptcha_id" alt="{{ \App\CPU\translate('image') }}">
                                        <i class="tio-refresh icon"></i>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" class="form-control form-control-lg" name="default_captcha_value" value=""
                                           placeholder="{{\App\CPU\translate('Enter captcha value')}}" autocomplete="off">
                                </div>
                                <div class="col-6">
                                    <a>
                                        <img src="{{ URL('/admin/auth/code/captcha/1') }}" class="input-field rounded h-54px"
                                             id="default_recaptcha_id" alt="{{ \App\CPU\translate('image') }}">
                                        <i class="tio-refresh icon"></i>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-lg btn-block btn-primary mt-5" id="signInBtn">{{\App\CPU\translate('sign_in')}}</button>
                </form>

                @if(env('APP_MODE')=='demo' && !$isPublicDemoHost)
                    <div class="auto-fill-data-copy">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div>
                                <span class="d-block"><strong>{{\App\CPU\translate('Email')}} </strong> : {{\App\CPU\translate('admin@admin.com')}}</span>
                                <span class="d-block"><strong>{{\App\CPU\translate('Password')}} </strong> : {{\App\CPU\translate('12345678')}}</span>
                            </div>
                            <div>
                                <button class="btn action-btn btn--primary m-0 copy_cred"><i class="tio-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


            </div>

        </div>
    </div>
</main>


<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>

<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>

{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        "use strict";
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
@endif

<script>
    $(document).on('ready', function(){

        $(".direction-toggle").on("click", function () {
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








        $('.tio-refresh').on('click', function() {
            re_captcha();
        });

        function re_captcha() {
            var $url = "{{ URL('/admin/auth/code/captcha') }}";
            var $url = $url + "/" + Math.random();
            document.getElementById('default_recaptcha_id').src = $url;
        }

    })


    @if(isset($recaptcha) && $recaptcha['status'] == 1)
    $(document).ready(function() {
        $('#signInBtn').click(function (e) {

            if( $('#set_default_captcha_value').val() == 1){
                $('#form-id').submit();
                return true;
            }
            e.preventDefault();
            if (typeof grecaptcha === 'undefined') {
                toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');
                $('#reload-captcha').removeClass('d-none');
                $('#set_default_captcha_value').val('1');

                return;
            }

            grecaptcha.ready(function () {
                grecaptcha.execute('{{$recaptcha['site_key']}}', {action: 'submit'}).then(function (token) {
                    $('#g-recaptcha-response').value = token;
                    $('#form-id').submit();
                });
            });

            window.onerror = function(message) {
                var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                if (message.includes('Invalid site key')) {
                    errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                } else if (message.includes('not loaded in api.js')) {
                    errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                }

                $('#reload-captcha').removeClass('d-none');
                $('#set_default_captcha_value').val('1');

                toastr.error(errorMessage)
                return true;
            };
        });
    });

    @endif

</script>
<script src="{{asset('assets/admin')}}/js/auth-page.js"></script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
