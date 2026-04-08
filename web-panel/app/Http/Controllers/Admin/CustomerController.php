<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreOrUpdateRequest;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Transection;
use App\Traits\CustomerTrait;
use App\Traits\TransactionTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;

class CustomerController extends Controller
{
    use TransactionTrait, CustomerTrait;

    public function __construct(
        private Customer    $customer,
        private Order       $order,
        private Account     $account,
        private Transection $transection
    )
    {
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('admin-views.customer.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(CustomerStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        if (!empty($request->file('image'))) {
            $imageName = Helpers::upload('customer/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $imageName = null;
        }

        $customer = $this->customer;
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->image = $imageName;
        $customer->state = $request->state;
        $customer->city = $request->city;
        $customer->zip_code = $request->zip_code;
        $customer->address = $request->address;
        $customer->balance = $request->balance;
        $customer->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Customer Added successfully'),
                'redirect_url' => route( 'admin.customer.list'),
            ]);
        }

        Toastr::success(translate('Customer Added successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', 'oldest'),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];

        $accounts = $this->account->orderBy('id')->get();
        $resources = $this->queryList($filters)->with('orders')->paginate(Helpers::pagination_limit())->appends($filters);

        return view('admin-views.customer.list', compact('resources', 'accounts'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function view(Request $request, $id): View|Factory|RedirectResponse|Application
    {
        $customer = $this->customer->where('id', $id)->first();
        $perPage = $request->query('per_page') ?? Helpers::pagination_limit();
        if (isset($customer)) {
            $queryParam = [];
            $search = $request['search'];
            if ($request->has('search')) {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('id', 'like', "%{$value}%");
                        }
                    });
                $queryParam = ['search' => $request['search']];
            } else {
                $orders = $this->order->where(['user_id' => $id]);
            }

            $queryParam = array_merge($queryParam, ['per_page' => $perPage]);

            $orders = $orders->latest()->paginate($perPage)->appends($queryParam);
            return view('admin-views.customer.view', compact('customer', 'orders', 'search'));
        }

        Toastr::error('Customer not found!');
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function transactionList(Request $request, $id): View|Factory|RedirectResponse|Application
    {
        $accounts = $this->account->get();
        $customer = $this->customer->where('id', $id)->first();
        $perPage = $request->input('per_page') ?? Helpers::pagination_limit();
        if (isset($customer)) {
            $accId = $request['account_id'];
            $tran_type = $request['tran_type'];
            $orders = $this->order->where(['user_id' => $id])->get();
            $transactions = $this->transection->where(['customer_id' => $id])
                ->when($accId != null, function ($q) use ($request) {
                    return $q->where('account_id', $request['account_id']);
                })
                ->when($tran_type != null, function ($q) use ($request) {
                    return $q->where('tran_type', $request['tran_type']);
                })->latest()->paginate($perPage)
                ->appends(['account_id' => $request['account_id'], 'tran_type' => $request['tran_type'], 'per_page' => $perPage]);

            return view('admin-views.customer.transaction-list', compact('customer', 'transactions', 'orders', 'tran_type', 'accounts', 'accId'));
        }

        Toastr::error(translate('Customer not found'));
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(Request $request): Factory|View|Application
    {
        $customer = $this->customer->where('id', $request->id)->first();
        return view('admin-views.customer.edit', compact('customer'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(CustomerStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $customer = $this->customer->where('id', $request->id)->first();

        if($request->hasFile('image')){
            $image = Helpers::update('customer/', $customer->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        }else if($request->old_image) {
            $image = $customer->image;
        }else{
            Helpers::delete('customer/' . $customer->image);
            $image = null;
        }

        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->image = $image;
        $customer->state = $request->state;
        $customer->city = $request->city;
        $customer->zip_code = $request->zip_code;
        $customer->address = $request->address;
        $customer->balance = $request->balance;
        $customer->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Customer updated successfully'),
                'redirect_url' => route( 'admin.customer.list'),
            ]);
        }

        Toastr::success(translate('Customer updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $customer = $this->customer->find($request->id);
        Helpers::delete('customer/' . $customer['image']);
        $customer->delete();

        Toastr::success(translate('Customer removed successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateBalance(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ]);

        $customer = $this->customer->find($request->customer_id);
        $amount = $request->amount;
        $remainingBalance = $customer->balance + $amount;

        \DB::transaction(function () use ($customer, $request, $amount, $remainingBalance) {
            // Handle receiving account transaction
            $receiveAccount = Account::find($request->account_id);
            $this->createTransaction(
                'Income',
                $receiveAccount,
                $amount,
                $request->description,
                false,
                $request->date,
                $request->customer_id
            );

            if ($customer->balance >= 0) {
                // Create payable transaction
                $payableAccount = Account::find(2);
                $this->createTransaction(
                    'Payable',
                    $payableAccount,
                    $amount,
                    $request->description,
                    false,
                    $request->date,
                    $request->customer_id
                );
            } else {
                $receivableAccount = Account::find(3);

                if ($remainingBalance >= 0) {
                    // Handle clearing of negative balance
                    $this->createTransaction(
                        'Receivable',
                        $receivableAccount,
                        abs($customer->balance),
                        'update customer balance',
                        true,
                        $request->date,
                        $request->customer_id
                    );

                    // Create payable transaction for remaining positive balance
                    if ($remainingBalance > 0) {
                        $payableAccount = Account::find(2);
                        $this->createTransaction(
                            'Payable',
                            $payableAccount,
                            $remainingBalance,
                            $request->description,
                            false,
                            $request->date,
                            $request->customer_id
                        );
                    }
                } else {
                    // Handle partial payment of negative balance
                    $this->createTransaction(
                        'Receivable',
                        $receivableAccount,
                        $amount,
                        'update customer balance',
                        true,
                        $request->date,
                        $request->customer_id
                    );
                }
            }

            $customer->balance += $amount;
            $customer->save();
        });

        Toastr::success(translate('Customer balance updated successfully'));
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
            'sorting_type' => $request->input('sorting_type', 'oldest'),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $resources = $this->queryList($filters)->get();
        $dataRows = $resources->map(function ($resource, $index) use ($visibleColumns) {
            $model = [
                'sl' => $index + 1,
                'image' => $resource->image,
                'name' => $resource->name,
                'phone' => $resource->id != 0 ? $resource->mobile : 'No Phone',
                'orders' => $resource->orders->count(),
                'balance' => $resource->id != 0 ? formatNumberWithSymbol(number: $resource->balance, useDecimal: true) : 'No Balance',
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
            $html = view('admin-views.customer.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'customer_' . date('Y_m_d') . '.pdf';

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

        return (new FastExcel($finalExportRows))->download('customer_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
