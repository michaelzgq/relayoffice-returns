<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\Admin;
use App\Models\AdminRole;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use function App\CPU\translate;

class SystemController extends Controller
{
    public function __construct(
        private Admin $admin,
        private AdminRole $adminRole
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

        $workspaceAdmins = collect();
        $workspaceRoles = collect();

        if (Helpers::returns_user_is_master_admin()) {
            $workspaceAdmins = $this->admin->newQuery()
                ->with('role')
                ->where('role_id', '!=', 4)
                ->orderByRaw('CASE WHEN role_id = 1 THEN 0 ELSE 1 END')
                ->orderBy('created_at')
                ->get();

            $workspaceRoles = $this->adminRole->newQuery()
                ->whereIn('id', [1, 2, 3])
                ->where('status', 1)
                ->orderBy('id')
                ->get();
        }

        return view('admin-views.settings', compact('workspaceAdmins', 'workspaceRoles'));
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

    public function workspaceAccessStore(Request $request): RedirectResponse
    {
        $guard = $this->ensureWorkspaceAccessManageable();
        if ($guard) {
            return $guard;
        }

        $validated = $request->validate([
            'workspace_f_name' => ['required', 'string', 'max:191'],
            'workspace_l_name' => ['required', 'string', 'max:191'],
            'workspace_email' => ['required', 'email', 'max:191', 'unique:admins,email'],
            'workspace_role_id' => ['required', Rule::in([1, 2, 3])],
            'workspace_password' => ['required', 'string', 'min:8', 'same:workspace_password_confirmation'],
            'workspace_password_confirmation' => ['required'],
        ]);

        $admin = new Admin();
        $admin->f_name = $validated['workspace_f_name'];
        $admin->l_name = $validated['workspace_l_name'];
        $admin->email = strtolower($validated['workspace_email']);
        $admin->password = bcrypt($validated['workspace_password']);
        $admin->role_id = (int) $validated['workspace_role_id'];
        $admin->remember_token = Str::random(10);
        $admin->save();

        Toastr::success('Workspace account created successfully.');
        return back();
    }

    public function workspaceAccessUpdate(Request $request, int $id): RedirectResponse
    {
        $guard = $this->ensureWorkspaceAccessManageable();
        if ($guard) {
            return $guard;
        }

        $admin = $this->admin->newQuery()->where('role_id', '!=', 4)->findOrFail($id);

        $validated = $request->validate([
            'workspace_f_name' => ['required', 'string', 'max:191'],
            'workspace_l_name' => ['required', 'string', 'max:191'],
            'workspace_email' => ['required', 'email', 'max:191', Rule::unique('admins', 'email')->ignore($admin->id)],
            'workspace_role_id' => ['required', Rule::in([1, 2, 3])],
            'workspace_password' => ['nullable', 'string', 'min:8', 'same:workspace_password_confirmation'],
            'workspace_password_confirmation' => ['nullable'],
        ]);

        if ((int) $admin->id === (int) auth('admin')->id() && (int) $validated['workspace_role_id'] !== (int) $admin->role_id) {
            Toastr::info('Use another master admin account if you need to change your own role.');
            return back();
        }

        $admin->f_name = $validated['workspace_f_name'];
        $admin->l_name = $validated['workspace_l_name'];
        $admin->email = strtolower($validated['workspace_email']);
        $admin->role_id = (int) $validated['workspace_role_id'];

        if (!empty($validated['workspace_password'])) {
            $admin->password = bcrypt($validated['workspace_password']);
            $admin->remember_token = Str::random(10);
        }

        $admin->save();

        Toastr::success('Workspace account updated successfully.');
        return back();
    }

    public function workspaceAccessDelete(int $id): RedirectResponse
    {
        $guard = $this->ensureWorkspaceAccessManageable();
        if ($guard) {
            return $guard;
        }

        $admin = $this->admin->newQuery()->where('role_id', '!=', 4)->findOrFail($id);

        if ((int) $admin->id === (int) auth('admin')->id()) {
            Toastr::info('You cannot remove the account you are currently using.');
            return back();
        }

        $admin->delete();

        Toastr::success('Workspace account removed successfully.');
        return back();
    }

    private function ensureWorkspaceAccessManageable(): ?RedirectResponse
    {
        if (Helpers::returns_user_is_guest_demo()) {
            Toastr::info('Guest demo users cannot manage workspace access.');
            return redirect()->route('admin.returns.dashboard.index');
        }

        if (!Helpers::returns_user_is_master_admin()) {
            Toastr::info('Only master admin accounts can manage workspace access.');
            return redirect()->route('admin.settings');
        }

        return null;
    }
}
