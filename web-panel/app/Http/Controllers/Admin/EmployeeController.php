<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeStoreOrUpdateRequest;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Traits\EmployeeTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class EmployeeController extends Controller
{
    use EmployeeTrait;

    /**
     * @return Application|Factory|View
     */
    public function add_new(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        $roles = AdminRole::whereNotIn('id', [1])->get();

        if ($request->has('search')) {
            $key = explode(' ', $search);
            $employees = Admin::with(['role'])->where('role_id', '!=','1')->latest()
                ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });

            $queryParam = ['search' => $request['search']];
        } else {
            $employees = Admin::with(['role'])->where('role_id', '!=','1')->latest();
        }

        $employees = $employees
            ->paginate(\App\CPU\Helpers::pagination_limit())
            ->appends($queryParam);
        return view('admin-views.employee.add-new', compact('roles','employees', 'search'));

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(EmployeeStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        if (!empty($request->file('image'))) {
            $image_name =  \App\CPU\Helpers::upload('admin/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = null;
        }

        Admin::insert([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'image' => $image_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('employee_added_successfully'),
                'redirect_url' => route( 'admin.employee.add-new'),
            ]);
        }

        Toastr::success(\App\CPU\translate('employee_added_successfully'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $employees = $this->queryList($filters)->paginate(Helpers::pagination_limit())->appends($filters);

        return view('admin-views.employee.list', compact('employees'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit($id): View|Factory|RedirectResponse|Application
    {
        $employee = Admin::where('role_id', '!=','1')->where(['id' => $id])->first();
        if (auth('admin')->id()  == $employee['id']){
            Toastr::error(\App\CPU\translate('You_can_not_edit_your_own_info'));
            return redirect()->route('admin.employee.list');
        }
        $roles = AdminRole::whereNotIn('id', [1])->get();
        return view('admin-views.employee.edit', compact('roles', 'employee'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(EmployeeStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {

        $employee = Admin::where('role_id','!=',1)->findOrFail($id);

        if ($request['password'] == null) {
            $password = $employee['password'];
        } else {
            $password = bcrypt($request['password']);
        }

        if($request->hasFile('image')){
            $image = \App\CPU\Helpers::update('admin/', $employee->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        }else if($request->old_image) {
            $image = $employee->image;
        }else{
            Helpers::delete('admin/' . $employee->image);
            $image = null;
        }

       Admin::where(['id' => $id])->update([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'password' => $password,
            'image' => $image,
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('employee_updated_successfully'),
                'redirect_url' => route( 'admin.employee.add-new'),
            ]);
        }

        Toastr::success(\App\CPU\translate('employee_updated_successfully'));
        return redirect()->back();
    }

    public function distroy($id)
    {
        $role=Admin::where('role_id', '!=','1')->where(['id'=>$id])->first();
        if (auth('admin')->id()  == $role['id']){
            Toastr::error(\App\CPU\translate('You_can_not_edit_your_own_info'));
            return redirect()->route('admin.employee.list');
        }
        $role->delete();
        Toastr::info(\App\CPU\translate('employee_deleted_successfully'));
        return back();
    }

    public function employee_list_export(Request $request)
    {
        try{
            $key = explode(' ', $request['search']);
            $employees=Admin::zone()->with(['role'])->where('role_id', '!=','1')
            ->when(isset($key) , function($q) use($key){
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
                'employees'=>$employees,
                'search'=>$request->search??null,
            ];

            if ($request->type == 'excel') {
                return Excel::download(new EmployeeListExport($data), 'Employees.xlsx');
            } else if ($request->type == 'csv') {
                return Excel::download(new EmployeeListExport($data), 'Employees.csv');
            }

        } catch(\Exception $e) {
                Toastr::error("line___{$e->getLine()}",$e->getMessage());
                info(["line___{$e->getLine()}",$e->getMessage()]);
                return back();
            }
    }
    public function export(Request $request)
    {
        $visibleColumns = array_values(array_filter(
            explode(',', $request->columns ?? ''),
            fn($col) => $col !== 'action' && $col !== ''
        ));
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)->get();
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns) {
            $model = [
                'sl' => $index + 1,
                'name' => $resource->f_name ?? '' . ' ' . $resource->l_name ?? '',
                'phone' => $resource->phone,
                'email' => $resource->email,
            ];
            $data = [];
            foreach ($visibleColumns as $column)
            {
                $data[$column] = $model[$column] ?? '';
            }

            return $data;
        });
        if ($dataRows->isEmpty()) {
            $headerRow = array_values($visibleColumns);
        } else {
            $headerRow = array_keys($dataRows->first());
        }
        if ($request->export_type === 'pdf') {
            $html = view('admin-views.employee.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'employee_' . date('Y_m_d') . '.pdf';

            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }

        $requestedParameters = [
            ['Filter' => 'Start Date',     'Value' => $filters['start_date'] ?? ''],
            ['Filter' => 'End Date',       'Value' => $filters['end_date'] ?? ''],
            ['Filter' => 'Search',         'Value' => $filters['search'] ?? ''],
            ['Filter' => 'Sorting Type',   'Value' => $filters['sorting_type'] ?? ''],
        ];

        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('employee_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
