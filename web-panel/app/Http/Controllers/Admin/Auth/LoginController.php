<?php

namespace App\Http\Controllers\Admin\Auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use function App\CPU\translate;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    /**
     * @param $tmp
     * @return void
     */
    public function captcha($tmp): void
    {
        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if(Session::has('default_captcha_code')) {
            Session::forget('default_captcha_code');
        }
        Session::put('default_captcha_code', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    /**
     * @return Application|Factory|View
     */
    public function login(): View|Factory|Application
    {
        return view('admin-views.auth.login', [
            'isPublicDemoHost' => Helpers::dossentry_is_public_demo_host(),
            'isInternalAdminHost' => Helpers::dossentry_is_internal_admin_host(),
            'internalAdminLoginUrl' => Helpers::dossentry_internal_admin_login_url(),
            'guestDemoLoginUrl' => Helpers::dossentry_guest_demo_login_url(),
            'guestDemo' => config('dossentry.guest_demo'),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $recaptcha = Helpers::get_business_settings('recaptcha');

        if (isset($recaptcha) && $recaptcha['status'] == 1 && !$request?->set_default_captcha) {
            $request->validate([
                'g-recaptcha-response' => [
                    function ($attribute, $value, $fail) {
                        $secret_key = Helpers::get_business_settings('recaptcha')['secret_key'];
                        $response = $value;


                        $gResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret' => $secret_key,
                            'response' => $value,
                            'remoteip' => \request()->ip(),
                        ]);

                        if (!$gResponse->successful()) {
                            $fail(translate('ReCaptcha Failed'));
                        }
                    },
                ],
            ]);
        } else {
            if (strtolower($request->default_captcha_value) != strtolower(Session('default_captcha_code'))) {
                Session::forget('default_captcha_code');
                return back()->withErrors(translate('Captcha Failed'));
            }
        }

        if (Session::has('default_captcha_code')) {
            Session::forget('default_captcha_code');
        }

        $admin = Admin::query()
            ->with('role')
            ->whereRaw('LOWER(email) = ?', [strtolower((string) $request->email)])
            ->first();

        $isGuestDemoAccount = strtolower((string) $admin?->role?->name) === 'guest demo';

        if (Helpers::dossentry_is_public_demo_host() && $admin && !$isGuestDemoAccount) {
            return redirect()->away(
                Helpers::dossentry_internal_admin_login_url() . '?notice=internal-workspace-only'
            )->withErrors([
                translate('Staff accounts are not available in the shared demo workspace.'),
            ]);
        }

        if (Helpers::dossentry_is_internal_admin_host() && $admin && $isGuestDemoAccount) {
            return redirect()->away(
                Helpers::dossentry_guest_demo_login_url() . '?notice=guest-demo-only'
            )->withErrors([
                translate('The guest demo account can only be used on the shared demo workspace.'),
            ]);
        }

        $remember_me = $request->has('remember') ? true : false;
        if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors([translate('Credentials does not match')]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.auth.login');
    }
}
