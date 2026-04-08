<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountStoreOrUpdateRequest;
use App\Traits\AccountTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Account;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class AccountController extends Controller
{
    use AccountTrait;
    public function __construct(
        private Account $account
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];

        $resources = $this->queryList($filters)
            ->paginate(Helpers::pagination_limit())
            ->appends($filters);

        return view('admin-views.account.list', compact('resources'));
    }

    /**
     * @return Application|Factory|View
     */
    public function add(): Factory|View|Application
    {
        return view('admin-views.account.add');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(AccountStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $account = $this->account;
        $account->account = $request->account;
        $account->description = $request->description;
        $account->balance = $request->balance;
        $account->account_number = $request->account_number;
        $account->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('New Account Added successfully'),
                'redirect_url' => route('admin.account.list'),
            ]);
        }

        Toastr::success(translate('New Account Added successfully'));
        return redirect()->route('admin.account.list');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application
    {
        $account = $this->account->find($id);
        return view('admin-views.account.edit', compact('account'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(AccountStoreOrUpdateRequest $request, $id): RedirectResponse|JsonResponse
    {
        $account = $this->account->find($id);
        $account->account = $request->account;
        $account->account_number = $request->account_number;
        $account->description = $request->description;
        $account->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Account updated successfully'),
                'redirect_url' => route('admin.account.list'),
            ]);
        }

        Toastr::success(translate('Account updated successfully'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $account = $this->account->find($id);
        $account->delete();

        Toastr::success(translate('Account deleted successfully'));
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
                'account_info' =>
                    "Account: {$resource->account}, " .
                    "Description: {$resource->description}, " .
                    "Account Number: {$resource->account_number}",
                'balance_info' =>
                    "Balance: " . formatNumberWithSymbol(number: $resource->balance, useDecimal: true) . ", " .
                    "Total In: " . formatNumberWithSymbol(number: $resource->total_in, useDecimal: true) . ", " .
                    "Total Out: " . formatNumberWithSymbol(number: $resource->total_out, useDecimal: true),
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
            $html = view('admin-views.account.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'account_' . date('Y_m_d') . '.pdf';

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

        return (new FastExcel($finalExportRows))->download('account_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }

}
