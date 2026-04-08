<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Arr;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Traits\ProductTrait;

class StocklimitController extends Controller
{
    use ProductTrait;

    public function __construct(
        private Product $product
    )
    {
    }

    public function stockLimit(Request $request): Factory|View|Application
    {
        $search = $request->input('search', '');
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
            'only_stock_limited' => true,
        ];
        $products = $this->queryList($filters)
            ->paginate(Helpers::pagination_limit())
            ->appends(array_merge(Arr::except($filters, ['only_stock_limited', 'availability']), ['availability' => $request->input('availability', null)]));
        $minPrice = $this->product->whereColumn('quantity', '<=', 'reorder_level')->min('selling_price');
        $maxPrice = $this->product->whereColumn('quantity', '<=', 'reorder_level')->max('selling_price');
        $categories = Category::where('status', 1)->where('position', 0)->latest()->get();
        $subcategories = Category::where('position', 1)->whereIn('parent_id', $filters['category_ids'])->where('status', 1)->get();
        $brands = Brand::latest()->get();
        $suppliers = Supplier::latest()->get();

        return view('admin-views.stock.list', compact('products', 'search', 'minPrice', 'maxPrice', 'categories', 'subcategories', 'brands', 'suppliers'));
    }

    public function renderUpdateQuantityModal(Request $request)
    {
        $product = Product::find($request->id)->toArray();

        return view('admin-views.stock.partials.modal-update-quantity', compact('product'));
    }

    public function updateQuantity(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);
        $data = [
            'quantity' => $request->quantity,
        ];
        $product->update($data);

        Toastr::success(\App\CPU\translate('product_quantity_updated_successfully!'));
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
            'category_id' => $request->input('category_id', 0),
            'category_ids' => $request->input('category_ids', []),
            'subcategory_ids' => $request->input('subcategory_ids', []),
            'brand_ids' => $request->input('brand_ids', []),
            'supplier_id' => $request->input('supplier_id', null),
            'only_stock_limited' => true,
        ];
        $categoryNames     = Category::whereIn('id', $filters['category_ids'] ?? [])->pluck('name')->toArray();
        $subcategoryNames  = Category::whereIn('id', $filters['subcategory_ids'] ?? [])->pluck('name')->toArray();
        $brandNames        = Brand::whereIn('id', $filters['brand_ids'] ?? [])->pluck('name')->toArray();
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

            $html = view('admin-views.stock.pdf', compact('products', 'categoryNames', 'brandNames', 'subcategoryNames', 'supplierName'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);
            return response($mpdf->Output('stock_limit_products_' . date('Y_m_d') . '.pdf', 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="stock_limit_products_' . date('Y_m_d') . '.pdf"'
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

        return (new FastExcel($finalExportRows))->download('limited_stock_products_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
