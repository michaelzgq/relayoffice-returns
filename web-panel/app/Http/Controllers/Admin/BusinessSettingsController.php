<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessSettingsStoreOrUpdateRequest;
use App\Http\Requests\RecaptchaStoreOrUpdateRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class BusinessSettingsController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting
    ){}

    /**
     * @return Application|Factory|View
     */
    public function shopIndex(): View|Factory|Application
    {
        return view('admin-views.business-settings.shop-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function shopSetup(BusinessSettingsStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $shopLogo = $this->businessSetting->where(['key' => 'shop_logo'])?->first()?->value;
        $favIcon = $this->businessSetting->where(['key' => 'fav_icon'])?->first()?->value;
        if($request->hasFile('shop_logo')){
            $newShopLogo = Helpers::update('shop/', $shopLogo, APPLICATION_IMAGE_FORMAT, $request->file('shop_logo'));
        }else if($request->old_shop_logo) {
            $newShopLogo = $shopLogo;
        }else{
            Helpers::delete('shop/' . $shopLogo);
            $newShopLogo = null;
        }

        if($request->hasFile('fav_icon')){
            $newFavIcon = Helpers::update('shop/', $favIcon, APPLICATION_IMAGE_FORMAT, $request->file('fav_icon'));
        }else if($request->old_fav_icon) {
            $newFavIcon = $favIcon;
        }else{
            Helpers::delete('shop/' . $favIcon);
            $newFavIcon = null;
        }

        $data = array_merge($request->validated(), ['shop_logo' => $newShopLogo, 'fav_icon' => $newFavIcon]);

        foreach($data as $key => $value){
            DB::table('business_settings')->updateOrInsert(['key' => $key], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($request->ajax())
        {
            return response()->json([
                'success' => true,
                'success_message' => translate('Settings updated'),
                'redirect_url' => route('admin.business-settings.shop-setup'),
            ]);
        }

        Toastr::success(translate('Settings updated'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function shortcutKey(): View|Factory|Application
    {
        return view('admin-views.business-settings.shortcut-key-index');
    }

    public function recaptchaIndex()
    {
        return view('admin-views.business-settings.recaptcha-index');
    }

    public function recaptchaUpdate(RecaptchaStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {

        DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
            'key' => 'recaptcha',
            'value' => json_encode([
                'status' => isset($request['status'])  ? 1 : 0,
                'site_key' => $request['site_key'],
                'secret_key' => $request['secret_key']
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Updated Successfully'),
                'redirect_url' => route('admin.business-settings.recaptcha-index'),
            ]);
        }

        Toastr::success(translate('Updated Successfully'));
        return back();
    }
}
