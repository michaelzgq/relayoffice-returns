<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkImportRequest;
use App\Http\Requests\ProductStoreOrUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Traits\ProductTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CPU\translate;

class ProductController extends Controller
{
    use ProductTrait;
    public function __construct(
        private Unit     $unit,
        private Brand    $brand,
        private Product  $product,
        private Category $category,
        private Supplier $supplier
    )
    {
    }

    public function list(Request $request): View|Factory|Application
    {
        $availability = match ($request->input('availability')) {
            'available' => 1,
            'unavailable' => 0,
            default => null,
        };
        $filters = [
            'search' => $request->input('search', null),
            'availability' => $availability,
            'min_price' => $request->input('min_price', null),
            'max_price' => $request->input('max_price', null),
            'stocks' => $request->input('stocks', []),
            'category_ids' => $request->input('category_ids', []),
            'subcategory_ids' => $request->input('subcategory_ids', []),
            'brand_ids' => $request->input('brand_ids', []),
            'supplier_id' => $request->input('supplier_id', null),
        ];
        $products = $this->queryList($filters)
            ->paginate(Helpers::pagination_limit())
            ->appends(array_merge(Arr::except($filters, ['availability']), ['availability' => $request->input('availability', null)]));
        $minPrice = $this->product->min('selling_price');
        $maxPrice = $this->product->max('selling_price');
        $categories = Category::where('status', 1)->where('position', 0)->latest()->get();
        $subcategories = Category::where('position', 1)->whereIn('parent_id', $filters['category_ids'])->where('status', 1)->get();
        $brands = Brand::latest()->get();
        $suppliers = Supplier::latest()->get();
        return view('admin-views.product.list', compact('products', 'minPrice', 'maxPrice', 'categories', 'subcategories', 'brands', 'suppliers' ));
    }

    public function index(): View|Factory|Application
    {
        $categories = $this->category->where(['position' => 0])->where('status', 1)->get();
        $brands = $this->brand->get();
        $suppliers = $this->supplier->get();
        $units = $this->unit->get();

        return view('admin-views.product.add', compact('categories', 'brands', 'suppliers', 'units'));
    }

    public function getCategories(Request $request): JsonResponse
    {
        $cat = $this->category->where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="" selected>---' . translate('Select') . '---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    public function store(ProductStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'product_code' => $request->product_code,
            'unit_type' => $request->unit_type,
            'unit_value' => $request->unit_value,
            'brand' => $request->brand_id,
            'category_ids' => json_encode($category),
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'image' => $request->has('image') ? Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $request->file('image')) : null,
            'order_count' => 0,
            'supplier_id' => $request->supplier_id,
            'available_time_started_at' => $request->available_time_started_at,
            'available_time_ended_at' => $request->available_time_ended_at,
        ];

        $this->product->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Product Added Successfully'),
                'redirect_url' => route('admin.product.add'),
            ]);
        }

        Toastr::success(translate('Product Added Successfully'));
        return redirect()->route('admin.product.list');
    }

    public function edit($id): Factory|View|Application|RedirectResponse
    {
        $product = $this->product->find($id);
        if (!$product) {
            Toastr::error(translate('Product not found'));

            return redirect()->route('admin.product.list');
        }
        $product_category = json_decode($product->category_ids);
        $categories = $this->category->mainCategory()->get();
        $brands = $this->brand->get();
        $suppliers = $this->supplier->get();
        $units = $this->unit->get();

        return view('admin-views.product.edit', compact('product', 'categories', 'brands', 'product_category', 'suppliers', 'units'));
    }

    public function show($id)
    {
        $product = $this->product->with(['unit', 'supplier', 'productBelongsToBrand', 'orderDetails'])->where('id', $id)->first();
        if (!$product) {
            Toastr::error(translate('Product not found'));
            return redirect()->route('admin.product.list');
        }
        $product_category = json_decode($product->category_ids);

        return view('admin-views.product.details', compact('product', 'product_category'));
    }

    public function update(ProductStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $product = $this->product->find($id);

        if($request->hasFile('image')){
            $image = Helpers::update('product/', $product->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        }else if($request->old_image) {
            $image = $product->image;
        }else{
            Helpers::delete('product/' . $product->image);
            $image = null;
        }

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'product_code' => $request->product_code,
            'unit_type' => $request->unit_type,
            'unit_value' => $request->unit_value,
            'brand' => $request->brand_id,
            'category_ids' => json_encode($category),
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'discount_type' => $request->discount_type,
            'discount' => $request->discount ?? 0,
            'tax' => $request->tax ?? 0,
            'quantity' => $request->quantity,
            'reorder_level' => $request->reorder_level,
            'image' => $image,
            'order_count' => $product->order_count,
            'supplier_id' => $request->supplier_id,
            'available_time_started_at' => $request->available_time_started_at,
            'available_time_ended_at' => $request->available_time_ended_at,
        ];
        $product->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Product Updated Successfully'),
                'redirect_url' => route('admin.product.list'),
            ]);
        }
        Toastr::success(translate('Product Updated successfully'));
        return back();
    }

    public function delete(Request $request, Product $product): RedirectResponse
    {
        if (Storage::disk('public')->exists('product/' . $product->image)) {
            Storage::disk('public')->delete('product/' . $product->image);
        }
        $product->delete();

        Toastr::success(translate('Product removed successfully'));

        return redirect()->route('admin.product.list');
    }

    public function status(Request $request): RedirectResponse
    {
        $table = $this->product->find($request->id);
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
            'search' => $request->input('search', ''),
            'availability' => $request->input('availability') === 'available' ? 1
                : ($request->input('availability') === 'unavailable' ? 0 : null),
            'min_price' => $request->input('min_price', null),
            'max_price' => $request->input('max_price', null),
            'stocks' => $request->input('stocks', []),
            'category_ids' => $request->input('category_ids', []),
            'subcategory_ids' => $request->input('subcategory_ids', []),
            'brand_ids' => $request->input('brand_ids', []),
            'supplier_id' => $request->input('supplier_id', null),
        ];
        $categoryNames = Category::whereIn('id', $filters['category_ids'] ?? [])->pluck('name')->toArray();
        $subcategoryNames = Category::whereIn('id', $filters['subcategory_ids'] ?? [])->pluck('name')->toArray();
        $brandNames = Brand::whereIn('id', $filters['brand_ids'] ?? [])->pluck('name')->toArray();
        $supplierName = Supplier::where('id', $filters['supplier_id'] ?? null)->value('name') ?? '';
        $stocks            = $filters['stocks'] ?? [];
        $resources = $this->queryList($filters)->get();
        if ($request->export_type === 'pdf') {
            $categoryMap = Category::pluck('name', 'id')->toArray();

            $products = $resources->map(function ($resource) use ($categoryMap) {
                $categoryIds = json_decode($resource->category_ids, true);
                $mainCategory = collect($categoryIds)->firstWhere('position', 1);
                $resource->category_name = $mainCategory && isset($categoryMap[$mainCategory['id']])
                    ? $categoryMap[$mainCategory['id']]
                    : null;
                return $resource;
            });

            $html = view('admin-views.product.pdf', compact('products', 'categoryNames', 'subcategoryNames', 'brandNames', 'supplierName'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('products_' . date('Y_m_d') . '.pdf', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="products_' . date('Y_m_d') . '.pdf"'
            ]);
        }
        $columnMap = getExportColumnMap();

        $requestedParameters = [
            ['Filter' => 'Search',           'Value' => $filters['search'] ?? ''],
            ['Filter' => 'Availability',     'Value' => $request->input('availability', '')],
            ['Filter' => 'Minimum Price',    'Value' => $filters['min_price'] ?? ''],
            ['Filter' => 'Maximum Price',    'Value' => $filters['max_price'] ?? ''],
            ['Filter' => 'Stocks',           'Value' => implode(', ', $stocks)],
            ['Filter' => 'Categories',       'Value' => implode(', ', $categoryNames)],
            ['Filter' => 'Subcategories',    'Value' => implode(', ', $subcategoryNames)],
            ['Filter' => 'Brands',           'Value' => implode(', ', $brandNames)],
            ['Filter' => 'Supplier',         'Value' => $filters['supplier_id'] ?? ''],
        ];

        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns, $columnMap) {
            $data = [];

            foreach ($visibleColumns as $column) {
                $value = $column === 'sl'
                    ? $columnMap[$column]($index + 1)
                    : ($column === 'name'
                        ? ['Name' => $resource->name, 'ID' => $resource->product_code]
                        : $columnMap[$column]($resource));
                $data += $value;
            }

            return $data;
        });

        $headerRow = $dataRows->first() ? array_keys($dataRows->first()) : [];

        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('products_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

    public function barcodeGenerate(Request $request, $id): View|Factory|RedirectResponse|Application
    {
        if ($request->limit > 270) {
            Toastr::warning(translate('You can not generate more than 270 barcode'));
            return back();
        }

        $product = $this->product->where('id', $id)->first();
        $limit = $request->limit ?? 4;
        return view('admin-views.product.barcode-generate', compact('product', 'limit'));
    }

    public function barcode($id): Factory|View|Application
    {
        $product = $this->product->where('id', $id)->first();
        $limit = 28;
        return view('admin-views.product.barcode', compact('product', 'limit'));
    }

    public function bulkImportIndex(): Factory|View|Application
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulkImportData(BulkImportRequest $request): RedirectResponse|JsonResponse
    {
        set_time_limit(300);

        try {
            $collections = collect((new FastExcel)->import($request->file('products_file')));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => 'products_file', 'message' => translate('The uploaded is corrupted or unreadable.')]]
                ]));
            }

            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file'));
            return back();
        }

        if ($collections->isEmpty()) {
            if ($request->ajax()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => 'products_file', 'message' => translate('The uploaded file is empty.')]]
                ]));
            }

            Toastr::error(translate('The uploaded file is empty.'));
            return back();
        }


        $expectedColumns = [
            'name', 'description', 'unit_type', 'unit_value', 'brand', 'category_id',
            'sub_category_id', 'purchase_price', 'selling_price', 'discount_type',
            'discount', 'tax', 'quantity', 'reorder_level', 'status', 'supplier_id',
        ];
        $headers = array_keys($collections->first());
        $invalidHeaders = array_diff($headers, $expectedColumns);
        if (!empty($invalidHeaders)) {
            if ($request->ajax()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => 'products_file', 'message' => translate('Header mismatched! Please upload the correct format file.')]]
                ]));
            }

            Toastr::error(translate('Header mismatched! Please upload the correct format file.'));
            return back();
        }

        $unitTypes = $this->unit->pluck('id')->toArray();
        $categories = $this->category->where('position', 0)->pluck('id')->toArray();
        $subCategories = $this->category->where('position', 1)->pluck('id')->toArray();
        $brands = $this->brand->pluck('id')->toArray();
        $suppliers = $this->supplier->pluck('id')->toArray();
        $nonNegativeNumericFields = ['unit_type','unit_value', 'category_id', 'purchase_price', 'selling_price', 'discount', 'tax', 'quantity',  'reorder_level', 'status'];
        $inListFields = [
            'unit_type' => $unitTypes,
            'category_id' => $categories,
            'sub_category_id' => $subCategories,
            'brand' => $brands,
            'supplier_id' => $suppliers,
        ];
        $data = [];
        $errors = [];
        foreach ($collections as $index => $row) {
            $rowNumber = $index + 2;

            foreach ($expectedColumns as $column) {
                if (!in_array($column, ['brand', 'sub_category_id', 'supplier_id', 'description']) && (!array_key_exists($column, $row) || $row[$column] === '')) {
                    $errors[] = "Row {$rowNumber}: '{$column}' is required.";
                }
            }

            foreach ($nonNegativeNumericFields as $field) {
                if (isset($row[$field]) && !is_numeric($row[$field])) {
                    $label = ucwords(str_replace('_', ' ', $field));
                    $errors[] = "Row {$rowNumber}: {$label} must be a number.";
                }

                if (isset($row[$field]) && is_numeric($row[$field]) && $row[$field] < 0) {
                    $label = ucwords(str_replace('_', ' ', $field));
                    $errors[] = "Row {$rowNumber}: {$label} must be a non-negative number.";
                }
            }

            foreach ($inListFields as $field => $list) {
                if (($field === 'brand' || $field === 'sub_category_id' || $field === 'supplier_id') && ($row[$field] === null || $row[$field] === '')) {
                    continue;
                }
                if (!in_array($row[$field], $list)) {
                    $label = ucwords(str_replace('_', ' ', $field));
                    $errors[] = "Row {$rowNumber}: Invalid {$label}.";
                }
            }

            if (!in_array($row['discount_type'], ['percent', 'amount'])) {
                $errors[] = "Row {$rowNumber}: Discount type must be 'percent' or 'amount'.";
            }

            if ($row['discount_type'] === 'percent' && ($row['discount'] < 0 || $row['discount'] > 100)) {
                $errors[] = "Row {$rowNumber}: Discount must be between 0 and 100 percent.";
            } elseif ($row['discount_type'] === 'amount' && $row['discount'] < 0) {
                $errors[] = "Row {$rowNumber}: Discount must be a positive amount.";
            }

            if ($row['tax'] < 0 || $row['tax'] > 100) {
                $errors[] = "Row {$rowNumber}: Tax must be between 0 and 100 percent.";
            }

            if ($row['reorder_level'] < 1) {
                $errors[] = "Row {$rowNumber}: Reorder level must be at least 1.";
            }

            $product = ['discount_type' => $row['discount_type'], 'discount' => $row['discount']];
            $finalPrice = Helpers::discount_calculate($product, $row['selling_price']);
            if ($finalPrice >= $row['selling_price']) {
                $errors[] = "Row {$rowNumber}: Discount must be less than selling price.";
            }

            if (!array_filter($errors, fn($e) => str_contains($e, "Row {$rowNumber}"))) {
                $category = [];
                if ($row['category_id'] != null) {
                    $category[] = [
                        'id' => (string)$row['category_id'],
                        'position' => 1,
                    ];
                }
                if ($row['sub_category_id'] != null) {
                    $category[] = [
                        'id' => (string)$row['sub_category_id'],
                        'position' => 2,
                    ];
                }
                $data[] = [
                    'name' => $row['name'],
                    'description' => $row['description'] === '' ? null : $row['description'],
                    'image' => null,
                    'unit_type' => $row['unit_type'],
                    'unit_value' => $row['unit_value'],
                    'brand' => $row['brand'] === '' ? null : $row['brand'],
                    'category_ids' => json_encode($category),
                    'purchase_price' => $row['purchase_price'],
                    'selling_price' => $row['selling_price'],
                    'discount_type' => $row['discount_type'],
                    'discount' => $row['discount'],
                    'tax' => $row['tax'],
                    'quantity' => $row['quantity'],
                    'reorder_level' => $row['reorder_level'],
                    'status' => $row['status'] ?? 1,
                    'supplier_id' => $row['supplier_id'] === '' ? null : $row['supplier_id'],
                    'order_count' => 0,
                ];
            }
        }
        if (!empty($errors)) {
            if ($request->ajax()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => 'products_file', 'message' => $errors[0]]]
                ]));
            }

            Toastr::error($errors[0]);
            return back();
        }

        $productCodes = $this->product->pluck('product_code')->flip()->all();
        DB::beginTransaction();
        try {
            foreach ($data as &$item) {
                do {
                    $productCode = random_int(10000, 99999);
                } while (isset($productCodes[$productCode]));

                $productCodes[$productCode] = true;

                $item['product_code'] = $productCode;
                $item['created_at'] = now();
                $item['updated_at'] = now();
            }
            $this->product->insert($data);
            DB::commit();
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'success_message' => count($data) . ' ' . translate(Str::plural('product', count($data)) . ' imported successfully'),
                    'redirect_url' => route('admin.product.bulk-import'),
                ]);
            }

            Toastr::success(count($data) . ' ' . translate(Str::plural('product', count($data)) . ' imported successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [['code' => 'products_file', 'message' => translate('Failed to import products. Please try again.')]]
                ]));
            }
            Toastr::error(translate('Failed to import products. Please try again.'));
            Log::error('Product import failed: ' . $e->getMessage(), ['exception' => $e]);
        }

        return back();
    }

    public function bulkExportData(Request $request): StreamedResponse|string
    {
        $products = $this->product->all();
        $storage = [];
        foreach ($products as $item) {
            $categoryId = 0;
            $subCategoryId = null;

            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $categoryId = (int)$category['id'];
                } else if ($category['position'] == 2) {
                    $subCategoryId = (int)$category['id'];
                }
            }

            $baseData = [
                'name' => $item['name'],
                'description' => $item['description'],
                'unit_type' => $item['unit_type'],
                'unit_value' => $item['unit_value'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'brand' => $item['brand'] !== null ? (int)$item['brand'] : null,
                'purchase_price' => $item['purchase_price'],
                'selling_price' => $item['selling_price'],
                'discount_type' => $item['discount_type'],
                'discount' => $item['discount'],
                'tax' => $item['tax'],
                'quantity' => $item['quantity'],
                'reorder_level' => $item['reorder_level'],
                'status' => $item['status'],
                'supplier_id' => $item['supplier_id'],
            ];

            if (!isset($request->without_product_code)) {
                $baseData = array_merge(['product_code' => $item['product_code']], $baseData);
            }

            $storage[] = $baseData;
        }

        return (new FastExcel($storage))->download('products.xlsx');
    }

}
