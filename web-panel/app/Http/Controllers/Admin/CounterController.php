<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CounterStoreOrUpdateRequest;
use App\Models\Counter;
use App\Models\Order;
use App\Traits\CounterTrait;
use App\Traits\OrderTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class CounterController extends Controller
{
    use CounterTrait, OrderTrait;
    public function __construct(
        private Counter $counter,
        private Order   $order
    )
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', Helpers::pagination_limit());
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'per_page' => $perPage,
        ];

        $counters = $this->queryList($filters)
            ->paginate($perPage)
            ->appends($filters);
        return view('admin-views.counter.index', compact('counters' ));
    }

    public function details(Request $request, $id)
    {
        $counter = $this->counter
            ->withCount('orders')
            ->withSum('orders', 'order_amount')
            ->withSum('orders', 'total_tax')
            ->findOrFail($id);

        $perPage = $request->input('per_page') ?? Helpers::pagination_limit();
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'per_page' => $perPage
        ];
        $orders = $this->getOrderList($filters)->paginate($perPage)->appends($filters);

        return view('admin-views.counter.details', compact('counter', 'orders'));
    }

    public function store(CounterStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $counter = $this->counter;
        $counter->name = $request->input('name');
        $counter->number = $request->input('number');
        $counter->description = $request->input('description');
        $counter->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Counter added successfully'),
                'redirect_url' => route('admin.counter.index'),
            ]);
        }

        Toastr::success(translate('Counter added successfully'));
        return back();
    }

    public function edit($id)
    {
        $counter = $this->counter->findOrFail($id);
        return view('admin-views.counter.edit', compact('counter'));
    }

    public function update(CounterStoreOrUpdateRequest $request, $id)
    {
        $counter = $this->counter->findOrFail($id);
        $counter->name = $request->input('name');
        $counter->number = $request->input('number');
        $counter->description = $request->input('description');
        $counter->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Counter updated successfully'),
                'redirect_url' => route('admin.counter.index'),
            ]);
        }

        Toastr::success(translate('Counter updated successfully'));
        return redirect()->route('admin.counter.index');
    }

    public function delete($id)
    {
        $counter = $this->counter->findOrFail($id);
        $counter->delete();

        Toastr::success(translate('Counter deleted successfully'));
        return back();
    }

    public function changeStatus($id, $status)
    {
        $counter = $this->counter->findOrFail($id);
        $counter->status = $status;
        $counter->save();

        Toastr::success(translate('Counter status updated successfully'));
        return back();
    }

    public function export(Request $request)
    {
        $counter = $this->counter->findOrFail($request->id);
        $orders = $this->order
            ->with(['customer', 'account'])
            ->where('counter_id', $request->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('mobile', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request) {
                $startDate = date('Y-m-d 00:00:00', strtotime($request->input('start_date')));
                $endDate = date('Y-m-d 23:59:59', strtotime($request->input('end_date')));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('id', 'desc')
            ->get();
        $fileName = $counter->name . '-' . $counter->number . '-' . date('Y-m-d') . '.xlsx';
        $data = $orders->map(function ($order) {
            return [
                'Order ID' => $order->id,
                'Order Date' => date('d M Y', strtotime($order->created_at)),
                'Customer Info' => $order?->customer?->name . ' - ' . $order?->customer?->mobile,
                'Total Amount' => \App\CPU\Helpers::currency_symbol() . ' ' . number_format($order->order_amount + $order->total_tax - $order->coupon_discount_amount - ($order->extra_discount ?? 0), 2),
                'Paid By' => $order->payment_id = 0 ? \App\CPU\translate('Customer balance') : ($order->account ? $order->account->account : \App\CPU\translate('account_deleted')),
            ];
        });
        return (new FastExcel($data))->download($fileName);
    }

    public function exportList(Request $request)
    {
        $visibleColumns = array_values(array_filter(
            explode(',', $request->columns ?? ''),
            fn($col) => $col !== 'action' && $col !== ''
        ));
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)->get();
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns) {
            $model = [
                'sl' => $index + 1,
                'name' => $resource->name,
                'number' => $resource->number,
                'description' => $resource->description ?? 'N/A',
                'status' => $resource->status ? translate('active') : translate('inactive'),
            ];
            $data = [];
            foreach ($visibleColumns as $column)
            {
                $data[$column] = $model[$column];
            }

            return $data;
        });
        if ($dataRows->isEmpty()) {
            $headerRow = array_values($visibleColumns);
        } else {
            $headerRow = array_keys($dataRows->first());
        }
        if ($request->export_type === 'pdf') {
            $html = view('admin-views.counter.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'counter_' . date('Y_m_d') . '.pdf';

            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }

        $requestedParameters = [
            ['Filter' => 'Start Date',     'Value' => $filters['start_date'] ?? ''],
            ['Filter' => 'End Date',       'Value' => $filters['end_date'] ?? ''],
            ['Filter' => 'Search',         'Value' => $filters['search'] ?? ''],
            ['Filter' => 'Sorting Type',   'Value' => $filters['sorting_type'] ?? ''],
        ];

        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('counter_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

    public function exportCounterDetails(Request $request, $id) {
        $visibleColumns = array_values(array_filter(
            explode(',', $request->columns ?? ''),
            fn($col) => $col !== 'action' && $col !== ''
        ));
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->getOrderList($filters)->get();
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns) {
            $model = [
                'sl' => $index + 1,
                'order_id' => $resource->id,
                'order_date' => $resource->created_at,
                'customer_info' => $resource?->customer ?  'Customer Name: ' . $resource->customer->name . ($resource->customer->id != 0 ? ', Customer Mobile: ' . $resource->customer->mobile : '') : translate('Customer Deleted'),
                'total_amount' => formatNumberWithSymbol($resource->order_amount + $resource->total_tax - $resource->coupon_discount_amount - ($resource->extra_discount ?? 0), useDecimal: true),
                'paid_by' => ($resource->payment_id != 0) ? ($resource->account ? $resource->account->account : \App\CPU\translate('account_deleted')): \App\CPU\translate('Customer balance')
            ];
            $data = [];
            foreach ($visibleColumns as $column)
            {
                $data[$column] = $model[$column];
            }

            return $data;
        });
        if ($dataRows->isEmpty()) {
            $headerRow = array_values($visibleColumns);
        } else {
            $headerRow = array_keys($dataRows->first());
        }
        if ($request->export_type === 'pdf') {
            $html = view('layouts.admin.pdf._mpdf', ['dataRows' => $dataRows, 'headerRow' => $headerRow, 'pdfFor' => 'counter_details'])->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'counter_details_' . date('Y_m_d') . '.pdf';

            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }

        $requestedParameters = [
            ['Filter' => 'Start Date',     'Value' => $filters['start_date'] ?? ''],
            ['Filter' => 'End Date',       'Value' => $filters['end_date'] ?? ''],
            ['Filter' => 'Search',         'Value' => $filters['search'] ?? ''],
            ['Filter' => 'Sorting Type',   'Value' => $filters['sorting_type'] ?? ''],
        ];

        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('counter_details_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));

    }
}

