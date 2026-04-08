<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreOrUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Traits\CategoryTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;
use function App\CPU\translate;

class SubCategoryController extends Controller
{
    use CategoryTrait;

    public function __construct(
        private Category $category
    )
    {
    }

    public function getIndex(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'category_id' => $request->input('category_id', null),

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

        $data = [
            'total' => $resources->total(),
            'limit' => $limit,
            'offset' => $offset,
            'sorting_type' => $filters['sorting_type'],
            'search' => $filters['search'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'subCategories' => $resources->items()
        ];

        return response()->json($data, 200);
    }

    public function postStore(CategoryStoreOrUpdateRequest $request, Category $subCategory): JsonResponse
    {
        $exists = $this->category
            ->where('parent_id', $request->parent_id ?? 0)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => translate('Subcategory already exists!')], 403);
        }

        $data = [
            'name' => $request->name,
            'parent_id' => $request->parent_id == null ? 0 : $request->parent_id,
            'position' => 1,
        ];
        $this->category->create($data);

        return response()->json([
            'success' => true,
            'message' => translate('Sub Category saved successfully'),
        ], 200);

    }

    public function postUpdate(CategoryStoreOrUpdateRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'parent_id' => 'required|integer|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $exists = $this->category
            ->where('parent_id', $request->parent_id ?? 0)
            ->where('name', $request->name)
            ->whereNot('id', $request->id)
            ->exists();

        if ($exists)
        {
            return response()->json(['success' => false, 'message' => translate('Subcategory already exists!')], 403);
        }

        $category = $this->category->find($request->id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => translate('Category not found')], 403);
        }
        $data = [
            'name' => $request->name,
            'status' => (int)$request->status ? 1 : 0,
            'parent_id' => $request->parent_id ?? 0,
        ];
        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => translate('Category updated successfully'),
        ], 200);
    }

    public function delete(Request $request)
    {
        $category = $this->category->subCategory()->findOrFail($request->id);
        if ($request->type == 'shift_and_delete') {
            $products = Product::whereJsonContains('category_ids', ['id' => (string)$category->id])->get();
            foreach ($products as $product) {
                $newCategory = [];

                if ($request->category_id) {
                    $newCategory[] = [
                        'id' => (string)$request->category_id,
                        'position' => 1,
                    ];
                }

                if ($request->sub_category_id) {
                    $newCategory[] = [
                        'id' => (string)$request->sub_category_id,
                        'position' => 2,
                    ];
                }

                $product->category_ids = json_encode($newCategory);
                $product->save();
            }
        }
        $category->delete();

        return response()->json(['success' => true, 'message' => translate('Sub Category deleted successfully'),], 200);
    }

    public function getSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $result = $this->category->active()->where('name', 'LIKE', '%' . $request->name . '%')->get();
        if (count($result)) {
            return Response()->json($result, 200);
        } else {
            return response()->json(['message' => translate('Data not found')], 404);
        }
    }

    public function getSubCategoriesByCategoryIds(Request $request): JsonResponse
    {
        $limit = $request->input('limit');
        $offset = $request->input('offset');
        $categoryIds = json_decode($request->input('category_ids', ''));
        $subCategories = $this->category
            ->with(['parent'])
            ->where(['position' => 1])
            ->when(!is_null($categoryIds), fn($query) => $query->whereIn('parent_id', $categoryIds))
            ->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total' => $subCategories->total(),
            'limit' => $limit,
            'offset' => $offset,
            'subCategories' => $subCategories->items()
        ];

        return response()->json($data);
    }

    public function exportPdf(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)->subCategory()->with('parent')->get();
        $resourceIds = $resources->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $productCounts = $this->countProducts($resourceIds);
        foreach ($resources as $resource) {
            $resource->product_count = $productCounts[(string)$resource->id] ?? 0;
        }
        $isSubCategory = true;
        $html = view('admin-views.category.pdf', compact('resources', 'isSubCategory'))->render();
        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);
        $filename = 'subcategories_' . date('Y_m_d') . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
