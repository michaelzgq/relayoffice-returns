<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomRoleStoreOrUpdateRequest;
use App\Models\AdminRole;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class CustomRoleController extends Controller
{
    public function create()
    {
        $modules = MODULE_PERMISSION;
        $perPage = request()->query('per_page' ?? Helpers::pagination_limit());
        $roles = AdminRole::whereNotIn('id',[1])->latest()->paginate($perPage);
        return view('admin-views.role.role',compact('roles','modules'));
    }

    public function store(CustomRoleStoreOrUpdateRequest $request)
    {
        $role = new AdminRole();
        $role->name = $request->name;
        $role->modules = json_encode($request['modules']);
        $role->status = 1;
        $role->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('role_added_successfully'),
                'redirect_url' => route('admin.custom-role.create'),
            ]);
        }

        Toastr::success(\App\CPU\translate('role_added_successfully'));
        return back();
    }

    public function edit($id)
    {
        if($id == 1)
        {
            return view('errors.404');
        }
        $modules = MODULE_PERMISSION;
        $role = AdminRole::where(['id'=>$id])->first(['id','name','modules']);
        return view('admin-views.role.edit',compact('role','modules'));
    }

    public function update(CustomRoleStoreOrUpdateRequest $request,$id)
    {
        if($id == 1)
        {
            return view('errors.404');
        }

        $role = AdminRole::find($id);
        $role->name = $request->name;
        $role->modules = json_encode($request['modules']);
        $role->status = 1;
        $role->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('role_updated_successfully'),
                'redirect_url' => route('admin.custom-role.create'),
            ]);
        }


        Toastr::success(\App\CPU\translate('role_updated_successfully'));
        return redirect()->route('admin.custom-role.create');
    }
    public function distroy($id)
    {
        if($id == 1)
        {
            Toastr::warning(\App\CPU\translate('could_not_allow_to_delete_super_admin_role'));
            return back();
        }

        $roleExist = Admin::where('role_id', $id)->first();
        if ($roleExist) {
            Toastr::error(\App\CPU\translate('employee_assigned_on_this_role._role_delete_failed'));
        } else {
            $role = AdminRole::find($id);
            if ($role) {
                $role->delete();
                Toastr::success(\App\CPU\translate('role_deleted_sucessfully'));
            } else {
                Toastr::warning(\App\CPU\translate('delete_failed'));
            }
        }

        return back();
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $roles = AdminRole::where('id','!=','1')
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->latest()->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.custom-role.partials._table',compact('roles'))->render(),
            'count'=>$roles->count()
        ]);
    }

    public function employee_role_export(Request $request){
        $withdraw_request = AdminRole::whereNotIn('id',[1])->get();
        if($request->type == 'csv'){
            return (new FastExcel($withdraw_request))->download('CustomRole.csv');
        }
        return (new FastExcel($withdraw_request))->download('CustomRole.xlsx');
    }

    /**
     * @param $id
     * @param $status
     * @param Request $request
     * @return RedirectResponse
     */
    public function status($id, $status, Request $request): RedirectResponse
    {
        $roleExist = Admin::where('role_id', $id)->first();
        if ($roleExist) {
            Toastr::error(\App\CPU\translate('employee_assigned_on_this_role._status_change_failed'));
        } else {
            $action = AdminRole::where('id', $id)->update(['status' => $status]);
            if (!$action) {
                Toastr::error(\App\CPU\translate('status_update_failed'));
            }
            Toastr::success(\App\CPU\translate('status_changed_successfully'));
        }
        return back();
    }
}
