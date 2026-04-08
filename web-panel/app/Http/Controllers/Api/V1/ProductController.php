<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulkImportRequest;
use App\Http\Requests\ProductStoreOrUpdateRequest;
use App\Http\Resources\ProductsResource;
use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Traits\ProductTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CPU\translate;

class ProductController extends Controller
{
    use ProductTrait;
    public function __construct(
        private product $product,
        private BusinessSetting $businessSetting
    ){}


    public function list(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
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
            'stocks' => json_decode(request()->input('stocks', '')),
            'category_ids' => json_decode(request()->input('category_ids', '')),
            'subcategory_ids' => json_decode(request()->input('subcategory_ids', '')),
            'brand_ids' => json_decode(request()->input('brand_ids', '')),
            'supplier_id' => $request->input('supplier_id', null),
        ];
        $queries = $this->queryList($filters)
            ->paginate($limit, ['*'], 'page', $offset);

        $products = ProductsResource::collection($queries);
        $minPrice = $this->product->min('selling_price');
        $maxPrice = $this->product->max('selling_price');
        $data = array_merge(
            [
                'total' => $products->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $products->items(),
                'product_minimum_price' => $minPrice,
                'product_maximum_price' => $maxPrice,
            ],
            array_merge(Arr::except($filters, ['availability']), ['availability' => $request->input('availability', null)])
        );
        return response()->json($data, 200);
    }

    public function store(ProductStoreOrUpdateRequest $request)
    {
        if ($request->discount_type == 'percent') {
            $discount = ($request->selling_price / 100) * $request->discount;
        } else {
            $discount = $request->discount;
        }

        if ($request->selling_price <= $discount) {
            return response()->json(['success' => false,'message' => translate('Discount can not be more than selling price')], 403);
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
            'image' => $request->has('image') ? Helpers::upload('product/', 'png', $request->file('image')) : null,
            'order_count' => 0,
            'supplier_id' => $request->supplier_id,
            'available_time_started_at' => $request->available_time_started_at,
            'available_time_ended_at' => $request->available_time_ended_at,
        ];
        $this->product->create($data);

        return response()->json([
            'success' => true,
            'message' => translate('Product saved successfully'),
        ], 200);
    }

    public function update(ProductStoreOrUpdateRequest $request)
    {
        if ($request->discount_type == 'percent') {
            $discount = ($request->selling_price / 100) * $request->discount;
        } else {
            $discount = $request->discount;
        }

        if ($request->selling_price <= $discount) {
            return response()->json(['success' => false,'message' => translate('Discount can not be more than selling price')], 403);
        }

        $product = $this->product->find($request->id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => translate('Product not found')], 403);
        }

        if($request->hasFile('image')){
            $image = Helpers::update('product/', $product->image, 'png', $request->file('image'));
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

        return response()->json([
            'success' => true,
            'message' => translate('Product updated successfully'),
        ], 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $brand = $this->product->find($request->id);
        $brand->status = !$brand['status'];
        $brand->update();
        return response()->json([
            'message' => translate('Status updated successfully'),
        ], 200);
    }

    public function getSearch(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        if (!empty($search)) {
            $result = $this->product
                ->active()
                ->with(['unit', 'productBelongsToBrand', 'supplier'])
                ->where(function ($query) use ($search) {
                    $query->where('product_code', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');
                })
                ->latest()
                ->paginate($limit, ['*'], 'page', $offset);
            $products = ProductsResource::collection($result);
            $data = [
                'total' => $products->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $products->items(),
            ];
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'products' => [],
            ];
        }
        return response()->json($data, 200);
    }

    public function view(Request $request): JsonResponse
    {
        $product = $this->product->with(['unit', 'supplier', 'productBelongsToBrand', 'orderDetails'])->where('id', $request->id)->first();
        if (!$product) {
            return response()->json(['success' => false, 'message' => translate('Product not found')], 403);
        }

        $product = new ProductsResource($product);
        return response()->json([
            'success' => true,
            'product' => $product,
        ], 200);

    }

    public function delete(Request $request): JsonResponse
    {

        $product = $this->product->findOrFail($request->id);

        if (!empty($product->image) && Storage::disk('public')->exists('product/' . $product->image)) {
            Storage::disk('public')->delete('product/' . $product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Product deleted successfully'),
        ], 200);

    }

    public function bulkImportData(BulkImportRequest $request): JsonResponse
    {
        set_time_limit(300);

        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            return response()->json(['message' => translate('You have uploaded a wrong format file, please upload the right file')], 403);
        }

        if ($collections->isEmpty()) {
            return response()->json(['message' => translate('The uploaded file is empty.')], 403);
        }

        $expectedColumns = [
            'name', 'description', 'unit_type', 'unit_value', 'brand', 'category_id',
            'sub_category_id', 'purchase_price', 'selling_price', 'discount_type',
            'discount', 'tax', 'quantity', 'reorder_level', 'status', 'supplier_id',
        ];
        $headers = array_keys($collections->first());
        $invalidHeaders = array_diff($headers, $expectedColumns);
        if (!empty($invalidHeaders)) {
            return response()->json(['message' => translate('Header mismatched! Please upload the correct format file.')], 403);
        }

        $unitTypes = Unit::pluck('id')->toArray();
        $categories = Category::where('position', 0)->pluck('id')->toArray();
        $subCategories = Category::where('position', 1)->pluck('id')->toArray();
        $brands = Brand::pluck('id')->toArray();
        $suppliers = Supplier::pluck('id')->toArray();
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
            $formatted = [];
            foreach ($errors as $error) {
                if (preg_match('/^(Row \d+): (.+)$/', $error, $matches)) {
                    $formatted[] = [
                        'code' => $matches[1],
                        'message' => $matches[2],
                    ];
                }
            }
            return response()->json(['errors' => $formatted], 403);
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

            return response()->json(['code' => 200, 'message' => count($data) . ' ' . translate(Str::plural('product', count($data)) . ' imported successfully')], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => translate('Failed to import products. Please try again.')], 403);
        }
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

    public function downloadExcelSample(): JsonResponse
    {
        $path = asset('assets/product_bulk_format.xlsx');
        return response()->json(['product_bulk_file' => $path]);
    }

    public function barcodeGenerate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required',
        ], [
            'id.required' => 'Product ID is required',
            'quantity.required' => 'Barcode quantity is required',
        ]);

        if ($request->limit > 270) {
            return response()->json([
                'code' => 403,
                'message' => translate('You can not generate more than 270 barcodes')
            ]);
        }

        $product = $this->product->where('id', $request->id)->first();

        if (!$product) {
            return response()->json([
                'code' => 404,
                'message' => translate('Product not found')
            ]);
        }

        $quantity = $request->quantity ?? 30;

        $barcode = new DNS1D();
        $barcode->setStorPath(storage_path('app/public/barcodes/'));
        $barcodePath = 'barcodes/' . $product->product_code . '.png';
        Storage::disk('public')->put($barcodePath, base64_decode($barcode->getBarcodePNG($product->product_code, 'C128')));

        $imageData = base64_encode(Storage::disk('public')->get($barcodePath));
        $barcodeImage = 'data:image/png;base64,' . $imageData;

        // Render the view to HTML
        $html = view('admin-views.product.barcode-pdf', compact('product', 'quantity', 'barcodeImage'))->render();

        // Setup mPDF
        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        // Write the HTML content
        $mpdf->WriteHTML($html);

        $filename = 'barcodes_' . now()->format('Y_m_d_H_i_s') . '.pdf';

        // Return as streamed response for API
        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    public function codeSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $now = Carbon::now();

        // Step 1: Get active products by code
        $productByCode = $this->product
            ->with(['unit', 'productBelongsToBrand', 'supplier'])
            ->active() // <-- already existing active scope
            ->where('product_code', 'LIKE', '%' . $request->product_code . '%')
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        $filtered = $productByCode->getCollection()->filter(function ($product) use ($now) {
            $startTime = $product->available_time_started_at ? Carbon::parse($product->available_time_started_at) : null;
            $endTime = $product->available_time_ended_at ? Carbon::parse($product->available_time_ended_at) : null;

            if ($startTime && $endTime) {
                return $now->between($startTime, $endTime);
            }

            return true;
        });

        $productByCode->setCollection($filtered->values());

        $products = ProductsResource::collection($productByCode);

        return response()->json($products, 200);
    }


    public function productSort(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $sort = $request['sort'] ? $request['sort'] : 'ASC';
        $sortProducts = $this->product->with(['unit', 'productBelongsToBrand', 'supplier'])->orderBy('selling_price', $sort)->latest()->paginate($limit, ['*'], 'page', $offset);
        $products = ProductsResource::collection($sortProducts);
        return response()->json($products, 200);
    }

    public function popularProductSort(Request $request): JsonResponse
    {
        $sort = $request['sort'] ? $request['sort'] : 'ASC';
        $products = $this->product->with(['unit', 'productBelongsToBrand', 'supplier'])->orderBy('order_count', $sort)->get();
        $products = ProductsResource::collection($products);
        return response()->json($products, 200);
    }

    public function supplierWiseProduct(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $product = $this->product->with(['unit', 'productBelongsToBrand', 'supplier'])->where('supplier_id', $request->supplier_id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $products = ProductsResource::collection($product);
        $data = [
            'total' => $products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $products->items(),
        ];
        return response()->json($data, 200);
    }

    public function quantityUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0',
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $product = $this->product->find($request->id);
        $product->quantity = $request->quantity;
        $product->save();
        return response()->json(['message' => 'Product quantity updated successfully'], 200);

    }

    public function exportPdf(Request $request): Response
    {
        ini_set('max_execution_time', 120);
        ini_set("pcre.backtrack_limit", "10000000");
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
            'stocks' => json_decode(request()->input('stocks', '')),
            'category_ids' => json_decode(request()->input('category_ids', '')),
            'subcategory_ids' => json_decode(request()->input('subcategory_ids', '')),
            'brand_ids' => json_decode(request()->input('brand_ids', '')),
            'supplier_id' => $request->input('supplier_id', null),
        ];
        $categoryNames = Category::whereIn('id', $filters['category_ids'] ?? [])->pluck('name')->toArray();
        $subcategoryNames = Category::whereIn('id', $filters['subcategory_ids'] ?? [])->pluck('name')->toArray();
        $brandNames = Brand::whereIn('id', $filters['brand_ids'] ?? [])->pluck('name')->toArray();
        $supplierName = Supplier::where('id', $filters['supplier_id'] ?? null)->value('name') ?? '';
        $resources = $this->queryList($filters)->get();
        $categoryMap = Category::pluck('name', 'id')->toArray();
        $products = $resources->map(function ($resource) use ($categoryMap) {
            $categoryIds = json_decode($resource->category_ids, true);
            $mainCategory = collect($categoryIds)->firstWhere('position', 1);
            $resource->category_name = $mainCategory && isset($categoryMap[$mainCategory['id']])
                ? $categoryMap[$mainCategory['id']]
                : null;
            return $resource;
        });
        $html = view('admin-views.product.pdf', compact('products', 'categoryNames', 'brandNames', 'subcategoryNames', 'supplierName'))->render();
        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);
        $filename = 'products_' . date('Y_m_d') . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
