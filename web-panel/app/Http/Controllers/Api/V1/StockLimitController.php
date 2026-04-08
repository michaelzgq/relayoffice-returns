<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Mpdf\Mpdf;
use function App\CPU\translate;

class StockLimitController extends Controller
{
    use ProductTrait;

    public function __construct(private Product $product){}
    public function index(Request $request)
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
            'only_stock_limited' => true,
        ];
        $queries = $this->queryList($filters)
            ->paginate($limit, ['*'], 'page', $offset);
        $products = ProductsResource::collection($queries);
        $minPrice = $this->product->whereColumn('quantity', '<=', 'reorder_level')->min('selling_price');
        $maxPrice = $this->product->whereColumn('quantity', '<=', 'reorder_level')->max('selling_price');
        $data = array_merge(
            [
                'total' => $products->total(),
                'limit' => $limit,
                'offset' => $offset,
                'products' => $products->items(),
                'product_minimum_price' => $minPrice,
                'product_maximum_price' => $maxPrice,
            ],
            array_merge(Arr::except($filters, ['only_stock_limited', 'availability']), ['availability' => $request->input('availability', null)])
        );

        return response()->json($data, 200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
            'id' => 'required|numeric',
        ]);
        $product = $this->product->find($request->id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => translate('Product not found'),
            ], 403);
        }
        $data = [
            'quantity' => $request->quantity,
        ];
        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => translate('Product Stock updated successfully'),
        ]);
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
            'only_stock_limited' => true,
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
        $html = view('admin-views.stock.pdf', compact('products', 'categoryNames', 'brandNames', 'subcategoryNames', 'supplierName'))->render();
        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);
        $filename = 'stock_limit_products_' . date('Y_m_d') . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
