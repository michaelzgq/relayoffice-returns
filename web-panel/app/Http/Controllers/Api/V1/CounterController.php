<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class CounterController extends Controller
{
    public function __construct(
        private Counter $counter
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {

        $limit = $request['limit'] ?? null;
        $offset = $request['offset'] ?? 1;

        if($request->has('search')) {
            $key = explode(' ', $request['search']);
            $counters =  $this->counter->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('number', 'like', "%{$value}%");
                }
            })->orderBy('id', 'DESC');
        }else{
            $counters = $this->counter->orderBy('id', 'DESC');
        }

        // If limit is null, get all data; otherwise, use pagination
        if (is_null($limit)) {
            $counters = $counters->withCount('orders')
                ->withSum('orders', 'order_amount')
                ->withSum('orders', 'total_tax')->get();
            $data = [
                'total' => $counters->count(),
                'limit' => $limit,
                'offset' => $offset,
                'counters' => $counters
            ];
        } else {
            $counters = $counters->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $counters->total(),
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'counters' => $counters->items()
            ];
        }


        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:counters,name,NULL,id,number,' . $request->number,
            'number' => 'required|unique:counters,number,NULL,id,name,' . $request->name,
            'description' => 'required|max:255',
        ],[
            'name' => translate('Counter number has already been taken'),
            'number' => translate('Counter number has already been taken'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $counter = $this->counter;
        $counter->name = $request->input('name');
        $counter->number = $request->input('number');
        $counter->description = $request->input('description'); // This can be null if left empty
        $counter->save();

        return response()->json([
            'counter' => $counter,
            'message' => translate('Counter added successfully'),
        ], 200);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:counters,id',
            'name' => 'required|max:255|unique:counters,name,' . $request->id . ',id,number,' . $request->number,
            'number' => 'required|unique:counters,number,' . $request->id . ',id,name,' . $request->name,
            'description' => 'required|max:255',
        ],[
            'name' => translate('Counter number has already been taken'),
            'number' => translate('Counter number has already been taken'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $counter = $this->counter->findOrFail($request->id);
        $counter->name = $request->input('name');
        $counter->number = $request->input('number');
        $counter->description = $request->input('description'); // This can be null if left empty
        $counter->save();

        return response()->json([
            'counter' => $counter,
            'message' => translate('Counter updated successfully'),
        ], 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $counter = $this->counter->findOrFail($request->id);
        $counter->delete();
        return response()->json(
            ['success' => true, 'message' => translate('Counter deleted successfully')], 200
        );
    }

    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $counter = $this->counter->find($request->id);
        $counter->status = !$counter['status'];
        $counter->update();
        return response()->json([
            'message' => translate('Status updated successfully'),
        ], 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 1);

        $counter = $this->counter
            ->withCount('orders')
            ->withSum('orders', 'order_amount')
            ->withSum('orders', 'total_tax')
            ->findOrFail($request->id);

        $ordersQuery = $counter->orders()->with(['customer', 'account'])->latest();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('id', 'LIKE', "%$search%");
            });
        }

        $orders = $ordersQuery->paginate($limit, ['*'], 'page', $offset);

        $data = [
            'counter' => $counter,
            'total' => $orders->total(),
            'limit' => (int) $limit,
            'offset' => (int) $offset,
            'orders' => $orders->items()
        ];

        return response($data, 200);

    }
}
