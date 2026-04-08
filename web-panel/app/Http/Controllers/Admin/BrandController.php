<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandStoreOrUpdateRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Traits\BrandTrait;
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

class BrandController extends Controller
{
    use BrandTrait;

    public function __construct(
        private Brand $brand
    )
    {
    }

    public function index(Request $request): View|Factory|Application
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

        return view('admin-views.brand.index', compact('resources'));
    }

    public function store(BrandStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $request->hasFile('image') ? Helpers::upload('brand/', APPLICATION_IMAGE_FORMAT, $request->file('image')) : null,
        ];
        $this->brand->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Brand stored successfully'),
                'redirect_url' => route('admin.brand.add'),
            ]);
        }

        Toastr::success(translate('Brand stored successfully'));
        return back();
    }

    public function update(BrandStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $resource = $this->brand->find($id);
        if (!$resource) {
            Toastr::error(translate('Brand not found'));
            return back();
        }

        if($request->hasFile('image')){
            $image = Helpers::update('brand/', $resource['image'], 'png', $request->file('image'));
        }else if($request->old_image) {
            $image = $resource['image'];
        }else{
            Helpers::delete('brand/' . $resource['image']);
            $image = null;
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $image,
            'status' => $request->status ? 1 : 0,
        ];
        $resource->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Brand updated successfully'),
                'redirect_url' => route('admin.brand.add'),
            ]);
        }

        Toastr::success(translate('Brand updated successfully'));
        return back();
    }

    public function delete(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'resource_id' => 'sometimes|exists:brands,id',
        ], [
            'resource_id.exists' => translate('Brand not found'),
        ]);

        $resource = $this->brand->find($id);

        if (!$resource) {
            Toastr::error(translate('Brand not found'));
            return back();
        }
        if ($request->type == 'shift_and_delete') {
            Product::where('brand', $id)->update(['brand' => $request->resource_id]);
        }

        Helpers::delete('brand/' . $resource['image']);
        $resource->delete();

        Toastr::success(translate('Brand removed'));
        return back();
    }

    public function status(Request $request): RedirectResponse
    {
        $table = $this->brand->find($request->id);
        $table->status = $request->status;
        $table->save();

        Toastr::success(translate('Brand status updated'));
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

            $html = view('admin-views.brand.pdf', compact('resources'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'brands_' . date('Y_m_d') . '.pdf';

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
                    :  ($column === 'name' ? ['Name' => $resource->name, 'ID' => $resource->id] : $columnMap[$column]($resource));
                $data += $value;
            }

            return $data;
        });
        $headerRow = $dataRows->first() ? array_keys($dataRows->first()) : [];
        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('brands_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

    public function renderEditCanvas(Request $request)
    {
        $resource = $this->brand->find($request->id);

        return view('admin-views.brand.partials.offcanvas-edit', compact('resource'));
    }
    public function renderViewCanvas(Request $request)
    {
        $resource = $this->brand
            ->withCount(['products as product_count'])
            ->findOrFail($request->id);


        return view('admin-views.brand.partials.offcanvas-view', compact('resource'));
    }
    public function deleteAfterShiftingModal(Request $request)
    {
        $resource = $this->brand
            ->withCount(['products as product_count'])
            ->findOrFail($request->id);
        $resources = $this->brand
            ->whereNot('id', $resource->id)
            ->get();
        return view('admin-views.brand.partials.modal-delete-after-shifting', compact('resource', 'resources'));
    }
}
