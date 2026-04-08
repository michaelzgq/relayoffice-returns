<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\BrandStoreOrUpdateRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Traits\BrandTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Mpdf\Mpdf;
use function App\CPU\translate;

class BrandController extends Controller
{
    use BrandTrait;

    public function __construct(
        private Brand $brand
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
        $resources = $this->queryList($filters)->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'total' => $resources->total(),
            'limit' => $limit,
            'offset' => $offset,
            'sorting_type' => $filters['sorting_type'],
            'search' => $filters['search'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'brands' => $resources->items()
        ];

        return response()->json($data, 200);
    }

    public function postStore(BrandStoreOrUpdateRequest $request, Brand $brand): JsonResponse
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description ?? null,
            'image' => $request->hasFile('image') ? Helpers::upload('brand/', 'png', $request->file('image')) : null,
        ];
        $this->brand->create($data);

        return response()->json([
            'success' => true,
            'message' => translate('Brand saved successfully'),
        ], 200);

    }

    public function postUpdate(BrandStoreOrUpdateRequest $request): JsonResponse
    {
        $resource = $this->brand->find($request->id);
        if (!$resource) {
            return response()->json([
                'success' => false,
                'message' => translate('Brand not found')
            ], 403);
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
            'status' => (int)$request->status ? 1 : 0,
        ];
        $resource->update($data);

        return response()->json([
            'success' => true,
            'message' => translate('Brand updated successfully'),
        ], 200);

    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'sometimes|exists:brands,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $brand = $this->brand->find($request->id);
        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => translate('Brand not found')
            ], 403);
        }

        if ($request->type == 'shift_and_delete') {
            Product::where('brand', $request->id)->update(['brand' => $request->resource_id]);
        }
        Helpers::delete('brand/' . $brand['image']);
        $brand->delete();

        return response()->json(
            ['success' => true, 'message' => translate('Brand deleted successfully'),],
            200
        );
    }

    public function getSearch(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $brands = $this->brand->where('name', 'LIKE', '%' . $request->name . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $brands->total(),
            'limit' => $limit,
            'offset' => $offset,
            'brands' => $brands->items()
        ];
        return response()->json($data, 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $brand = $this->brand->find($request->id);
        $brand->status = !$brand['status'];
        $brand->update();
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
        $resources = $this->queryList($filters)->get();
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
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
