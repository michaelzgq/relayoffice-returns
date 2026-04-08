<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreOrUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Traits\CategoryTrait;
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


class CategoryController extends Controller
{
    use CategoryTrait;
    public function __construct(
        private Category $category
    ){}
    public function index(Request $request): View|Factory|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)
            ->mainCategory()
            ->paginate(Helpers::pagination_limit())
            ->appends($filters);

        $resourceIds = $resources->getCollection()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $productCounts = $this->countProducts($resourceIds);
        foreach ($resources as $resource) {
            $resource->product_count = $productCounts[(string)$resource->id] ?? 0;
        }

        return view('admin-views.category.index',compact('resources'));
    }

    public function subIndex(Request $request): Factory|View|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)
            ->subcategory()
            ->with('parent')
            ->paginate(Helpers::pagination_limit())
            ->appends($filters);

        $resourceIds = $resources->getCollection()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $productCounts = $this->countProducts($resourceIds);
        foreach ($resources as $resource) {
            $resource->product_count = $productCounts[(string)$resource->id] ?? 0;
        }

        return view('admin-views.category.sub-index',compact('resources'));
    }

    public function store(CategoryStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $request->hasFile('image') ? Helpers::upload('category/', APPLICATION_IMAGE_FORMAT, $request->file('image')) : null,
            'parent_id' => $request->parent_id ?? 0,
            'position' => $request->position ?? 0,
        ];

        $this->category->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate(($request->type == 'category' ? 'Category' : 'Sub Category') . ' stored successfully'),
                'redirect_url' => route($request->type == 'category' ? 'admin.category.add' : 'admin.category.add-sub-category'),
                ]);
        }

        Toastr::success(translate(($request->type == 'category' ? 'Category' : 'Sub category') . ' stored successfully'));
        return back();
    }

    public function status(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success(translate( $category->parent_id == 0 ? 'Category' : 'Sub Category' . ' status updated'));

        return back();
    }

    public function update(CategoryStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $category = $this->category->find($id);

        if (!$category) {
            Toastr::error(translate('Category not found'));
            return back();
        }

        if($request->hasFile('image')){
            $image = Helpers::update('category/', $category->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        }else if($request->old_image) {
            $image = $category->image;
        }else{
            Helpers::delete('category/' . $category->image);
            $image = null;
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $image,
            'status' => $request->status ? 1 : 0,
            'parent_id' => $request->parent_id ?? 0,
        ];
        $category->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Category updated successfully'),
                'redirect_url' => route('admin.category.add'),
            ]);
        }

        Toastr::success(translate('Category updated successfully'));

        return back();
    }

    public function updateSub(CategoryStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $category = $this->category->find($id);

        if (!$category) {
            Toastr::error(translate('Sub Category not found'));
            return back();
        }

        $data = [
            'name' => $request->name,
            'status' => $request->status ? 1 : 0,
            'parent_id' => $request->parent_id,
        ];
        $category->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Sub Category updated successfully'),
                'redirect_url' => route('admin.category.add-sub-category'),
            ]);
        }

        Toastr::success(translate('Sub Category updated successfully'));

        return back();
    }

    public function delete(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'resource_id' => 'required_if:type,shift_and_delete|exists:categories,id',
            'resource_child_id' => 'nullable|integer',
        ],[
            'resource_id.required_if' => translate('Please select a category to shift products to before deleting'),
            'resource_id.unique' => translate('Category with this ID does not exist'),
            'resource_child_id.integer' => translate('Subcategory must be an integer'),
        ]);

        $category = $this->category->find($id);
        if (!$category) {
            Toastr::error(translate('Category not found'));
            return back();
        }

        if ($request->type == 'shift_and_delete') {
            $categoryIds = $category->position == 0
                ? [['id' => $request->resource_id, 'position' => 1]]
                : ($request->resource_child_id != 0 ? [
                    ['id' => $request->resource_id, 'position' => 1],
                    ['id' => $request->resource_child_id, 'position' => 2]
                ] : [['id' => $request->resource_id, 'position' => 1]]);

            Product::whereJsonContains('category_ids', ['id' => (string)$category->id])
                ->update(['category_ids' => json_encode($categoryIds)]);

            if ($category->position == 0) {
                Helpers::delete('category/' . $category['image']);
            }
        }
        Category::where('parent_id', $id)->orWhere('id', $id)->delete();

        Toastr::success(translate('Category removed'));

        return back();
    }

    public function getSubCategories(Request $request)
    {
        $subcategories = Category::where('parent_id', $request->parent_id)->whereNot('id', $request->id)->get();
        return response()->json($subcategories);
    }

    public function exportCategory(Request $request)
    {
        $visibleColumns = array_values(array_filter(
            explode(',', $request->columns ?? ''),
            fn($col) => $col !== 'action' && $col !== ''
        ));
        $isSubCategory = in_array('parent_name', $visibleColumns);
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)
            ->{$isSubCategory ? 'subCategory' : 'mainCategory'}()
            ->when($isSubCategory, fn($query) => $query->with('parent'))
            ->get();
        $resourceIds = $resources->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $productCounts = $this->countProducts($resourceIds);
        foreach ($resources as $resource) {
            $resource->product_count = $productCounts[(string)$resource->id] ?? 0;
        }
        if ($request->export_type === 'pdf') {

            $html = view('admin-views.category.pdf', compact('resources', 'isSubCategory'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = ($isSubCategory ? 'sub_categories_' : 'categories_') . date('Y_m_d') . '.pdf';

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

        return (new FastExcel($finalExportRows))->download(($isSubCategory ? 'subcategories_' : 'categories_') . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

    public function renderEditCanvas(Request $request)
    {
        $resource = $this->category->find($request->id);

        return view('admin-views.category.partials.category.offcanvas-edit', compact('resource'));
    }
    public function renderViewCanvas(Request $request)
    {
        $resource = $this->category
            ->findOrFail($request->id);
        $resource->product_count = $this->countProducts([(string)$resource->id])[(string)$resource->id] ?? 0;
        $resources = $this->category->mainCategory()->get();

        return view('admin-views.category.partials.category.offcanvas-view', compact('resource', 'resources'));
    }
    public function deleteAfterShiftingModal(Request $request)
    {
        $resource = $this->category->with('parent')->where('id', $request->id)->firstOrFail();
        $resource->product_count = $this->countProducts([(string)$resource->id])[(string)$resource->id] ?? 0;
        $resources = $this->category
            ->whereNot('id', $resource->id)
            ->mainCategory()
            ->get();
        return view('admin-views.category.partials.category.modal-delete-after-shifting', compact('resource', 'resources'));
    }

    public function renderSubCategoryEditCanvas(Request $request)
    {
        $resource = $this->category->with('parent')->where('id', $request->id)->first();
        $resources = $this->category
            ->mainCategory()
            ->get();

        return view('admin-views.category.partials.sub-category.offcanvas-edit', compact('resource', 'resources'));
    }
    public function renderSubCategoryViewCanvas(Request $request)
    {
        $resource = $this->category->with('parent')->where('id', $request->id)->first();
        $resource->product_count = $this->countProducts([(string)$resource->id])[(string)$resource->id] ?? 0;

        return view('admin-views.category.partials.sub-category.offcanvas-view', compact('resource'));
    }
    public function deleteSubCategoryAfterShiftingModal(Request $request)
    {
        $resource = $this->category->with('parent')->where('id', $request->id)->firstOrFail();
        $resource->product_count = $this->countProducts([(string)$resource->id])[(string)$resource->id] ?? 0;
        $resources = $this->category
            ->mainCategory()
            ->get();

        return view('admin-views.category.partials.sub-category.modal-delete-after-shifting', compact('resource', 'resources'));
    }

}
