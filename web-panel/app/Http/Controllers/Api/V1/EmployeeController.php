<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeStoreOrUpdateRequest;
use App\Models\Admin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function list()
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $employees = Admin::with(['role'])->where('role_id', '!=', '1')->latest()->paginate($limit, ['*'], 'page', $offset);
        if ($employees->count() > 0) {
            $employees->each(function ($employee) {
                if (isset($employee->role->modules) && gettype($employee->role->modules) == 'string') {
                    $employee->role->modules = json_decode($employee->role->modules);
                }
            });
        }
        $data = [
            'total' => $employees->total(),
            'limit' => $limit,
            'offset' => $offset,
            'employees' => $employees->items()
        ];
        return response()->json($data, 200);
    }

    public function store(EmployeeStoreOrUpdateRequest $request)
    {
        if (!empty($request->file('image'))) {
            $imageName = \App\CPU\Helpers::upload('admin/', 'png', $request->file('image'));
        } else {
            $imageName = null;
        }

        Admin::insert([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee saved successfully',
        ], 200);
    }

    public function update(EmployeeStoreOrUpdateRequest $request)
    {
        $id = $request->id;

        if ($request->role_id == 1) {
            return response()->json([
                'success' => true,
                'message' => 'access denied',
            ], 200);
        }

        $employee = Admin::where('role_id', '!=', 1)->findOrFail($id);

        if (auth('admin')->id() == $employee['id']) {
            return response()->json([
                'success' => true,
                'message' => 'You_can_not_edit_your_own_info',
            ], 200);
        }

        if ($request['password'] == null) {
            $pass = $employee['password'];
        } else {

            $pass = bcrypt($request['password']);
        }

        if ($request->has('image')) {
            $employee['image'] = \App\CPU\Helpers::update(dir: 'admin/', old_image: $employee->image, format: 'png', image: $request->file('image'));
        }

        Admin::where(['id' => $id])->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => $pass,
            'image' => $employee['image'],
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'employee updated successfully',
        ], 200);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $role = Admin::where('role_id', '!=', '1')->where(['id' => $id])->first();

        if (auth('admin')->id() == $role['id']) {
            return response()->json([
                'success' => true,
                'message' => 'You_can_not_edit_your_own_info',
            ], 200);
        }
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'employee_deleted_successfully',
        ], 200);
    }

    function employee_list_export(Request $request)
    {
        try {
            $key = explode(' ', $request['search']);
            $employees = Admin::zone()->with(['role'])->where('role_id', '!=', '1')
                ->when(isset($key), function ($q) use ($key) {
                    $q->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('f_name', 'like', "%{$value}%");
                            $q->orWhere('l_name', 'like', "%{$value}%");
                            $q->orWhere('phone', 'like', "%{$value}%");
                            $q->orWhere('email', 'like', "%{$value}%");
                        }
                    });
                })
                ->latest()->get();
            $data = [
                'employees' => $employees,
                'search' => $request->search ?? null,
            ];

            if ($request->type == 'excel') {
                return Excel::download(new EmployeeListExport($data), 'Employees.xlsx');
            } else if ($request->type == 'csv') {
                return Excel::download(new EmployeeListExport($data), 'Employees.csv');
            }

        } catch (\Exception $e) {
            Toastr::error("line___{$e->getLine()}", $e->getMessage());
            info(["line___{$e->getLine()}", $e->getMessage()]);
            return back();
        }
    }
}
