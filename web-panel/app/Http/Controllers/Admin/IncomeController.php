<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseIncomeStoreOrUpdateRequest;
use App\Traits\TransactionTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transection;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class IncomeController extends Controller
{
    use TransactionTrait;
    public function __construct(
        private Transection $transection,
        private Account $account,
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function add(Request $request): View|Factory|Application
    {
        $accounts = $this->account->orderBy('id','desc')->get();
        $perPage = $request->get('per_page', Helpers::pagination_limit());
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'per_page' => $perPage,
        ];

        $incomes = $this->transactionQueryList($filters)
            ->where('tran_type', 'Income')
            ->paginate($perPage)
            ->appends($filters);

        return view('admin-views.income.add',compact('accounts','incomes'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(ExpenseIncomeStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $account = $this->account->find($request->account_id);

        $transection = $this->transection;
        $transection->tran_type = 'Income';
        $transection->account_id = $request->account_id;
        $transection->amount = $request->amount;
        $transection->description = $request->description;
        $transection->debit = 0;
        $transection->credit = 1;
        $transection->balance =  $account->balance + $request->amount;
        $transection->date = $request->date;
        $transection->save();

        $account->total_in = $account->total_in + $request->amount;
        $account->balance = $account->balance + $request->amount;
        $account->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('New Income Added successfully'),
                'redirect_url' => route('admin.account.add-income'),
            ]);
        }


        Toastr::success(translate('New Income Added successfully'));
        return back();
    }

    public function exportIncome(Request $request)
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
        $resources = $this->transactionQueryList($filters)->where('tran_type', 'Income')->get();
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns) {
            $model = [
                'date' => $resource->date,
                'account' => $resource->account->account,
                'type' => $resource->tran_type,
                'amount' => formatNumberWithSymbol(number: $resource->amount, useDecimal: true),
                'description' => $resource->description ?? 'N/A',
                'debit' =>  formatNumberWithSymbol(number: $resource->debit != 0 ? $resource->debit : 0, useDecimal: true),
                'credit' => formatNumberWithSymbol(number: $resource->credit != 0 ? $resource->credit : 0, useDecimal: true),
                'balance' => formatNumberWithSymbol(number: $resource->balance, useDecimal: true),
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
            $html = view('layouts.admin.pdf._mpdf', ['dataRows' => $dataRows, 'headerRow' => $headerRow, 'pdfFor' => 'income'])->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'income_' . date('Y_m_d') . '.pdf';

            return response($mpdf->Output($filename, 'S'), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        }
        if (in_array('image', $visibleColumns)) {
            $dataRows->transform(function ($item) {
                unset($item['image']);
                return $item;
            });

            $headerRow = array_values(array_filter($headerRow, fn($col) => $col !== 'image'));
        }
        $headerRow = array_map(fn($value) => translate($value), $headerRow);

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

        return (new FastExcel($finalExportRows))->download('income_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
