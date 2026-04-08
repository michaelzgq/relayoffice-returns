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

class CategoryController extends Controller
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
        ];
        $resources = $this->queryList($filters)
            ->mainCategory()
            ->paginate($limit, ['*'], 'page', $offset);

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
            'categories' => $resources->items()
        ];

        return response()->json($data, 200);
    }

    public function postStore(CategoryStoreOrUpdateRequest $request, Category $category): JsonResponse
    {
        $exists = $this->category
            ->where('parent_id', $request->parent_id ?? 0)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => translate('Category already exists!')], 403);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $request->hasFile('image') ? Helpers::upload('category/', 'png', $request->file('image')) : null,
            'parent_id' => $request->parent_id == null ? 0 : $request->parent_id,
            'position' => $request->position ?? 0,
        ];

        $this->category->create($data);

        return response()->json([
            'success' => true,
            'message' => translate('Category saved successfully'),
        ], 200);
    }

    public function postUpdate(CategoryStoreOrUpdateRequest $request): JsonResponse
    {
        $exists = $this->category
            ->whereNot('id', $request->id)
            ->where('parent_id', $request->parent_id ?? 0)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => translate('Category already exists!')], 403);
        }

        $category = $this->category->find($request->id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => translate('Category not found')], 403);
        }

        if ($request->hasFile('image')) {
            $image = Helpers::update('category/', $category->image, 'png', $request->file('image'));
        } else if ($request->old_image) {
            $image = $category->image;
        } else {
            Helpers::delete('category/' . $category->image);
            $image = null;
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $image,
            'status' => (int)$request->status ? 1 : 0,
            'parent_id' => $request->parent_id ?? 0,
        ];
        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => translate('Category updated successfully'),
        ], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required_if:type,shift_and_delete|exists:categories,id',
            'description' => 'nullable|max:255',
            'resource_child_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $category = $this->category->find($request->id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => translate('Category not found')], 403);
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
        Category::where('parent_id', $request->id)->orWhere('id', $request->id)->delete();

        return response()->json(['success' => true, 'message' => translate($category->position == 0 ? 'Category' : 'Subcategory' . ' deleted successfully'),], 200);

    }

    public function getSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $categories = $this->category->active()->position()->where('name', 'LIKE', '%' . $request->name . '%')->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'limit' => $limit,
            'offset' => $offset,
            'categories' => $categories->items()
        ];
        return response()->json($data, 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $category = $this->category->find($request->id);
        $category->status = !$category['status'];
        $category->update();
        return response()->json([
            'message' => translate('Status updated successfully'),
        ], 200);
    }

    public function exportPdf(Request $request): Response
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)->mainCategory()->get();
        $resourceIds = $resources->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $productCounts = $this->countProducts($resourceIds);
        foreach ($resources as $resource) {
            $resource->product_count = $productCounts[(string)$resource->id] ?? 0;
        }
        $isSubCategory = false;
        $html = view('admin-views.category.pdf', compact('resources', 'isSubCategory'))->render();
        $mpdf = new Mpdf([
            'tempDir' => storage_path('tmp'),
            'default_font' => 'dejavusans',
            'mode' => 'utf-8',
        ]);
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->WriteHTML($html);
        $filename = 'categories_' . date('Y_m_d') . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
