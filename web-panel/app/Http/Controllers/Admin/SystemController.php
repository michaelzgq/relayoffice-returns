<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\Admin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use function App\CPU\translate;

class SystemController extends Controller
{
    public function __construct(
        private Admin $admin
    ){}

    /**
     * @return Application|Factory|View
     */
    public function settings(): View|Factory|Application|RedirectResponse
    {
        if (Helpers::returns_user_is_guest_demo()) {
            Toastr::info('Guest demo users cannot open workspace settings.');
            return redirect()->route('admin.returns.dashboard.index');
        }

        return view('admin-views.settings');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsUpdate(SettingUpdateRequest $request): RedirectResponse
    {
        if (Helpers::returns_user_is_guest_demo()) {
            Toastr::info('Guest demo users cannot update workspace settings.');
            return redirect()->route('admin.returns.dashboard.index');
        }

        $admin = $this->admin->find(auth('admin')->id());
        $admin->f_name = $request->f_name;
        $admin->l_name = $request->l_name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->image = $request->has('image') ? Helpers::update('admin/', $admin->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $admin->image;
        $admin->save();

        Toastr::success(translate('updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsPasswordUpdate(Request $request): RedirectResponse
    {
        if (Helpers::returns_user_is_guest_demo()) {
            Toastr::info('Guest demo users cannot update the shared password.');
            return redirect()->route('admin.returns.dashboard.index');
        }

        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $admin = $this->admin->find(auth('admin')->id());
        $admin->password = bcrypt($request['password']);
        $admin->save();

        Toastr::success(translate('password updated successfully'));
        return back();
    }
}
