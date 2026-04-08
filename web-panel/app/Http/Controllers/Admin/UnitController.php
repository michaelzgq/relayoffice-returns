<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitStoreOrUpdateRequest;
use App\Models\Product;
use App\Traits\UnitTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class UnitController extends Controller
{
    use UnitTrait;
    public function __construct(
        private Unit $unit
    ){}

    public function index(Request $request): Factory|View|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)
            ->paginate(Helpers::pagination_limit())
            ->appends($filters);

        return view('admin-views.unit.index',compact('resources'));
    }

    public function store(UnitStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $data = [
            'unit_type' => $request->name,
        ];
        $this->unit->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Unit stored successfully'),
                'redirect_url' => route('admin.unit.index'),
            ]);
        }

        Toastr::success(translate('Unit stored successfully'));
        return back();
    }

    public function update(UnitStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $unit = $this->unit->find($id);
        if (!$unit) {
            Toastr::error(translate('Unit not found'));
            return back();
        }
        $data = [
            'unit_type' => $request->name,
            'status' => $request->filled('status') ? 1 : 0,
        ];
        $unit->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Unit updated successfully'),
                'redirect_url' => route('admin.unit.index'),
            ]);
        }

        Toastr::success(translate('Unit updated successfully'));
        return back();
    }

    public function delete(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'resource_id' => 'required_if:type,shift_and_delete|exists:units,id',
        ], [
            'resource_id.required_if' => translate('Please select a unit to shift products to before deleting'),
            'resource_id.exists' => translate('Unit not found'),
        ]);

        $resource = $this->unit->find($id);

        if (!$resource) {
            Toastr::error(translate('Unit not found'));
            return back();
        }

        if ($request->type == 'shift_and_delete') {
            Product::where('unit_type', $id)->update(['unit_type' => $request->resource_id]);
        }
        $resource->delete();

        Toastr::success(translate('Unit Type removed'));
        return back();
    }

    public function status(Request $request): RedirectResponse
    {
        $table = $this->unit->find($request->id);
        $table->status = $request->status;
        $table->save();

        Toastr::success(translate('Unit status updated'));
        return back();
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', 120);
        ini_set("pcre.backtrack_limit", "10000000");
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
        if ($request->export_type === 'pdf') {

            $html = view('admin-views.unit.pdf', compact('resources'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'units_' . date('Y_m_d') . '.pdf';

            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }
        $columnMap = getExportColumnMap();
        $requestedParameters = [
            ['Filter' => 'Start Date',     'Value' => $filters['start_date'] ?? ''],
            ['Filter' => 'End Date',       'Value' => $filters['end_date'] ?? ''],
            ['Filter' => 'Search',         'Value' => $filters['search'] ?? ''],
            ['Filter' => 'Sorting Type',   'Value' => $filters['sorting_type'] ?? ''],
        ];
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns, $columnMap) {
            $data = [];

            foreach ($visibleColumns as $column) {
                $value = $column === 'sl'
                    ? $columnMap[$column]($index + 1)
                    : ($column === 'name' ? ['Name' => $resource->unit_type, 'ID' => $resource->id] : $columnMap[$column]($resource));
                $data += $value;
            }

            return $data;
        });
        $headerRow = $dataRows->first() ? array_keys($dataRows->first()) : [];
        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('units_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

    public function renderEditCanvas(Request $request)
    {
        $resource = $this->unit->find($request->id);

        return view('admin-views.unit.partials.offcanvas-edit', compact('resource'));
    }

    public function renderViewCanvas(Request $request)
    {
        $resource = $this->unit
            ->withCount(['products as product_count'])
            ->findOrFail($request->id);
        $resources = $this->unit->get();
        return view('admin-views.unit.partials.offcanvas-view', compact('resource', 'resources'));
    }

    public function deleteAfterShiftingModal(Request $request)
    {
        $resource = $this->unit
            ->withCount(['products as product_count'])
            ->findOrFail($request->id);
        $resources = $this->unit
            ->whereNot('id', $resource->id)
            ->get();

        $resources->map(fn($resource) => $resource->name = $resource->unit_type);

        return view('admin-views.unit.partials.modal-delete-after-shifting', compact('resource', 'resources'));
    }
}
