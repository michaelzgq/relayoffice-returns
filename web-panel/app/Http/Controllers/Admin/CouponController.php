<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponStoreOrUpdateRequest;
use App\Traits\CouponTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class CouponController extends Controller
{
    use CouponTrait;
    public function __construct(
        private Coupon $coupon
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function addNew(Request $request): Factory|View|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];

        $coupons = $this->queryList($filters)
            ->paginate(Helpers::pagination_limit())
            ->appends($filters);

        return view('admin-views.coupon.index', compact('coupons'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(CouponStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        DB::table('coupons')->insert([
            'title' => $request->title,
            'code' => $request->code,
            'user_limit' => $request->coupon_type !='default'? 1 : $request->user_limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Coupon added successfully'),
                'redirect_url' => route('admin.coupon.add-new'),
            ]);
        }

        Toastr::success(translate('Coupon added successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $coupon = $this->coupon->find($request->id);
        $coupon->status = $request->status;
        $coupon->save();

        Toastr::success(translate('Coupon status updated'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application
    {
        $coupon = $this->coupon->where(['id' => $id])->first();
        return view('admin-views.coupon.edit', compact('coupon'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(CouponStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        DB::table('coupons')->where(['id' => $id])->update([
            'title' => $request->title,
            'code' => $request->code,
            'user_limit' => $request->coupon_type !='default'? 1 : $request->user_limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : $request->discount,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Coupon updated successfully'),
                'redirect_url' => route('admin.coupon.add-new'),
            ]);
        }

        Toastr::success(translate('Coupon updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $coupon = $this->coupon->find($request->id);
        $coupon->delete();

        Toastr::success(translate('Coupon removed'));
        return back();
    }

    public function export(Request $request)
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
                'title' => $resource->title,
                'code' => $resource->code,
                'min_purchase' => formatNumberWithSymbol(number: $resource->min_purchase, useDecimal: true),
                'max_discount' => $resource->discount_type == 'percent' ? formatNumberWithSymbol(number: $resource->max_discount, useDecimal: true) : '-',
                'discount' => formatNumberWithSymbol(number: $resource->discount, useDecimal: true, type: $resource->discount_type),
                'discount_type' => ucfirst($resource->discount_type),
                'start_date' => $resource->start_date,
                'end_date' => $resource->expire_date,
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
            $html = view('admin-views.coupon.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'coupon_' . date('Y_m_d') . '.pdf';

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

        return (new FastExcel($finalExportRows))->download('coupon_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
