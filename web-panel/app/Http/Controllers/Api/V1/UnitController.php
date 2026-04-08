<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Product;
use App\Models\Unit;
use App\Http\Controllers\Controller;
use App\Traits\UnitTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Unit\UnitStoreRequest;
use App\Http\Requests\Admin\Unit\UnitUpdateRequest;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use function App\CPU\translate;

class UnitController extends Controller
{
    use UnitTrait;
    public function __construct(
        private Unit $unit
    ){}

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

        $data =  [
            'total' => $resources->total(),
            'limit' => $limit,
            'offset' => $offset,
            'sorting_type' => $filters['sorting_type'],
            'search' => $filters['search'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'units' => $resources->items()
        ];
        return response()->json($data, 200);
    }

    public function postStore(UnitStoreRequest $request, Unit $unit): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'unit_type' => 'required|unique:units,unit_type|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $data = [
            'unit_type' => $request->unit_type,
        ];
        $this->unit->create($data);

        return response()->json([
            'success' => true,
            'message' => translate('Unit saved successfully'),
        ], 200);

    }

    public function postUpdate(UnitUpdateRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_type' => 'required|max:255|unique:units,unit_type,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $unit = $this->unit->find($request->id);
        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => translate('Unit not found')
            ], 403);
        }
        $data = [
            'unit_type' => $request->unit_type,
            'status' => (int)$request->status ? 1 : 0,
        ];
        $unit->update($data);

        return response()->json([
            'success' => true,
            'message' => translate('Unit updated successfully'),
        ], 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required_if:type,shift_and_delete|exists:units,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $unit = $this->unit->find($request->id);
        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => translate('Unit not found')
            ], 403);
        }
        try {
            if ($request->type == 'shift_and_delete') {
                Product::where('unit_type', $request->id)->update(['unit_type' => $request->resource_id]);
            }
            $unit->delete();

            return response()->json(
                ['success' => true, 'message' => translate('Unit deleted successfully'),],
                200
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => translate('Unit not deleted')
            ], 403);
        }
    }

    public function getSearch(Request $request): Response|Application|ResponseFactory
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $units = $this->unit->where('unit_type', 'Like', '%' . $request->name . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $units->total(),
            'limit' => $limit,
            'offset' => $offset,
            'units' => $units->items()
        ];
        return response($data, 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $unit = $this->unit->find($request->id);
        $unit->status = !$unit['status'];
        $unit->update();

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
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
