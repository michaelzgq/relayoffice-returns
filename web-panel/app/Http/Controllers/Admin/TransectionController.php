<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\TransactionTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Transection;
use App\Models\Account;
use App\CPU\Helpers;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CPU\translate;

class TransectionController extends Controller
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
    public function list(Request $request): View|Factory|Application
    {
        $accounts = $this->account->orderBy('id','desc')->get();
        $perPage = $request->get('per_page', Helpers::pagination_limit());
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
            'account_id' => $request->input('account_id', null),
            'transaction_type' => $request->input('transaction_type', null),
            'per_page' => $perPage,
        ];

        $transections = $this->transactionQueryList($filters)
            ->paginate($perPage)
            ->appends($filters);


        return view('admin-views.transection.list',compact('accounts','transections'));
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
            'account_id' => $request->input('account_id', null),
            'transaction_type' => $request->input('transaction_type', null),
        ];

        $resources = $this->transactionQueryList($filters)->get();
        $account = $this->account->where('id', $filters['account_id'])->first()?->account;
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
            $html = view('admin-views.transection.pdf', ['dataRows' => $dataRows, 'headerRow' => $headerRow, 'pdfFor' => 'transaction', 'account' => $account])->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'transaction_' . date('Y_m_d') . '.pdf';

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
            ['Filter' => 'Account',   'Value' => $account],
            ['Filter' => 'Transaction Type',   'Value' => $filters['transaction_type'] ?? ''],
        ];

        $finalExportRows = collect($requestedParameters)
            ->concat([['Filter' => '', 'Value' => '']])
            ->concat([array_combine($headerRow, $headerRow)])
            ->concat($dataRows);

        return (new FastExcel($finalExportRows))->download('transaction_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
