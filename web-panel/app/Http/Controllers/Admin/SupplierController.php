<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierStoreOrUpdateRequest;
use App\Models\Account;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transection;
use App\Traits\SupplierTrait;
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

class SupplierController extends Controller
{
    use SupplierTrait;
    public function __construct(
        private Supplier $supplier,
        private Product $product,
        private Transection $transection,
        private Account $account,
    ){}

    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('admin-views.supplier.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(SupplierStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('supplier/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = null;
        }

        $supplier = $this->supplier;
        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;
        $supplier->image = $image_name;
        $supplier->state = $request->state;
        $supplier->city = $request->city;
        $supplier->zip_code = $request->zip_code;
        $supplier->address = $request->address;
        $supplier->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Supplier added successfully'),
                'redirect_url' => route( 'admin.supplier.list'),
            ]);
        }

        Toastr::success(translate('Supplier Added successfully'));
        return redirect()->route('admin.supplier.list');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        $filters = [
            'search' => $request->input('search', null),
            'sorting_type' => $request->input('sorting_type', null),
            'start_date' => $request->input('start_date', null),
            'end_date' => $request->input('end_date', null),
        ];
        $suppliers = $this->queryList($filters)->paginate(Helpers::pagination_limit())->appends($filters);

        return view('admin-views.supplier.list',compact('suppliers'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View
     */
    public function view(Request $request, $id): Factory|View|Application
    {
        $supplier = $this->supplier->find($id);
        return view('admin-views.supplier.view',compact('supplier'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View
     */
    public function productList(Request $request, $id): Factory|View|Application
    {
        $supplier = $this->supplier->find($id);
        $queryParam = [];
        $search = $request['search'];
        $sortOrderQty= $request['sort_orderQty'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where('supplier_id',$id)->
                where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%")
                            ->orWhere('product_code', 'like', "%{$value}%");
                    }
            });
            $queryParam = ['search' => $request['search']];
        } else {
            $query = $this->product->where('supplier_id',$id)
                ->when($request->sort_orderQty=='quantity_asc', function($q) use ($request){
                    return $q->orderBy('quantity', 'asc');
                })
                ->when($request->sort_orderQty=='quantity_desc', function($q) use ($request){
                    return $q->orderBy('quantity', 'desc');
                })
                ->when($request->sort_orderQty=='order_asc', function($q) use ($request){
                    return $q->orderBy('order_count', 'asc');
                })
                ->when($request->sort_orderQty=='order_desc', function($q) use ($request){
                    return $q->orderBy('order_count', 'desc');
                })
                ->when($request->sort_orderQty=='default', function($q) use ($request){
                    return $q->orderBy('id');
                });
        }

        $products = $query->latest()->paginate($request->input('per_page') ?? Helpers::pagination_limit())->appends(['search'=>$search,'sort_orderQty'=>$request->sort_orderQty, 'per_page' => $request->input('per_page')]);

        return view('admin-views.supplier.product-list',compact('supplier','products','search','sortOrderQty'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View
     */
    public function transactionList(Request $request, $id): View|Factory|Application
    {
        $supplier = $this->supplier->find($id);
        $accounts = $this->account->orderBy('id')->get();

        $from = $request->from;
        $to = $request->to;

        $query = $this->transection->where('supplier_id',$id)
            ->when($from!=null, function($q) use ($request){
                return $q->whereBetween('date', [$request['from'], $request['to']]);
            });

        $transections = $query->latest()->paginate($request->input('per_page') ?? Helpers::pagination_limit())->appends(['from'=>$request['from'],'to'=>$request['to'], 'per_page' => $request->input('per_page')]);
        return view ('admin-views.supplier.transaction-list',compact('supplier','accounts','transections','from','to'));
    }

    public function addNewPurchase(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'purchased_amount'=> 'required',
            'paid_amount' => 'required',
            'due_amount' => 'required',
            'payment_account_id' => 'required',
        ]);

        $paymentAccount = $this->account->find($request->payment_account_id);

        if($paymentAccount->balance < $request->paid_amount)
        {
            Toastr::warning(\App\CPU\translate('you_do_not_have_sufficent_balance'));
            return back();
        }
        if($request->paid_amount > 0)
        {
            $paymentTransaction = new Transection;
            $paymentTransaction->tran_type = 'Expense';
            $paymentTransaction->account_id = $paymentAccount->id;
            $paymentTransaction->amount = $request->paid_amount;
            $paymentTransaction->description = 'Supplier payment';
            $paymentTransaction->debit = 1;
            $paymentTransaction->credit = 0;
            $paymentTransaction->balance = $paymentAccount->balance - $request->paid_amount;
            $paymentTransaction->date = date("Y/m/d");
            $paymentTransaction->supplier_id = $request->supplier_id;
            $paymentTransaction->save();

            $paymentAccount->total_out = $paymentAccount->total_out + $request->paid_amount;
            $paymentAccount->balance = $paymentAccount->balance - $request->paid_amount;
            $paymentAccount->save();
        }

        if($request->due_amount > 0)
        {
            $payableAccount = $this->account->find(2);
            $payableTransaction = new Transection;
            $payableTransaction->tran_type = 'Payable';
            $payableTransaction->account_id = $payableAccount->id;
            $payableTransaction->amount = $request->due_amount;
            $payableTransaction->description = 'Supplier payment';
            $payableTransaction->debit = 0;
            $payableTransaction->credit = 1;
            $payableTransaction->balance = $payableAccount->balance + $request->due_amount;
            $payableTransaction->date = date("Y/m/d");
            $payableTransaction->supplier_id = $request->supplier_id;
            $payableTransaction->save();

            $payableAccount->total_in = $payableAccount->total_in + $request->due_amount;
            $payableAccount->balance = $payableAccount->balance + $request->due_amount;
            $payableAccount->save();

            $supplier = $this->supplier->find($request->supplier_id);
            $supplier->due_amount = $supplier->due_amount + $request->due_amount;
            $supplier->save();
        }

        Toastr::success(translate('Supplier new payment added successfully'));
        return back();

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function payDue(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'required',
            'total_due_amount'=> 'required',
            'pay_amount' => 'required',
            'remaining_due_amount' => 'required',
            'payment_account_id' => 'required',
        ]);

        $paymentAccount = $this->account->find($request->payment_account_id);
        if($paymentAccount->balance < $request->pay_amount)
        {
            Toastr::warning(\App\CPU\translate('you_do_not_have_sufficent_balance!'));
            return back();
        }

        if($request->pay_amount > 0 )
        {
            $paymentTransaction = new Transection;
            $paymentTransaction->tran_type = 'Expense';
            $paymentTransaction->account_id = $paymentAccount->id;
            $paymentTransaction->amount = $request->pay_amount;
            $paymentTransaction->description = 'Supplier due payment';
            $paymentTransaction->debit = 1;
            $paymentTransaction->credit = 0;
            $paymentTransaction->balance = $paymentAccount->balance - $request->pay_amount;
            $paymentTransaction->date = date("Y/m/d");
            $paymentTransaction->supplier_id = $request->supplier_id;
            $paymentTransaction->save();

            $paymentAccount->total_out = $paymentAccount->total_out + $request->pay_amount;
            $paymentAccount->balance = $paymentAccount->balance - $request->pay_amount;
            $paymentAccount->save();

            $payableAccount = $this->account->find(2);
            $payableTransaction = new Transection;
            $payableTransaction->tran_type = 'Payable';
            $payableTransaction->account_id = $payableAccount->id;
            $payableTransaction->amount = $request->pay_amount;
            $payableTransaction->description = 'Supplier due payment';
            $payableTransaction->debit = 1;
            $payableTransaction->credit = 0;
            $payableTransaction->balance = $payableAccount->balance - $request->pay_amount;
            $payableTransaction->date = date("Y/m/d");
            $payableTransaction->supplier_id = $request->supplier_id;
            $payableTransaction->save();

            $payableAccount->total_out = $payableAccount->total_out + $request->pay_amount;
            $payableAccount->balance = $payableAccount->balance - $request->pay_amount;
            $payableAccount->save();
        }

        $supplier = $this->supplier->find($request->supplier_id);
        $supplier->due_amount = $supplier->due_amount - $request->pay_amount;
        $supplier->save();

        Toastr::success(translate('Supplier payment due successfully'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $supplier = $this->supplier->find($id);
        return view('admin-views.supplier.edit', compact('supplier'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(SupplierStoreOrUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $supplier = $this->supplier->where('id',$request->id)->first();

        if($request->hasFile('image')){
            $image = Helpers::update('supplier/', $supplier->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        }else if($request->old_image) {
            $image = $supplier->image;
        }else{
            Helpers::delete('supplier/' . $supplier->image);
            $image = null;
        }
        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;
        $supplier->image = $image;
        $supplier->state = $request->state;
        $supplier->city = $request->city;
        $supplier->zip_code = $request->zip_code;
        $supplier->address = $request->address;
        $supplier->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'success_message' => translate('Supplier updated successfully'),
                'redirect_url' => route( 'admin.supplier.list'),
            ]);
        }

        Toastr::success(translate('Supplier updated successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $supplier = $this->supplier->find($request->id);
        Helpers::delete('supplier/' . $supplier['image']);
        $supplier->delete();

        Toastr::success(translate('Supplier removed successfully'));
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
                'name' => $resource->name,
                'email' => $resource->email,
                'phone' => $resource->mobile,
                'products' => $resource->products->count(),
            ];
            $data = [];
            foreach ($visibleColumns as $column)
            {
                $data[$column] = $model[$column] ?? '';
            }

            return $data;
        });
        if ($dataRows->isEmpty()) {
            $headerRow = array_values($visibleColumns);
        } else {
            $headerRow = array_keys($dataRows->first());
        }
        if ($request->export_type === 'pdf') {
            $html = view('admin-views.supplier.pdf', compact('dataRows', 'headerRow'))->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('tmp'),
                'default_font' => 'dejavusans',
                'mode' => 'utf-8',
            ]);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->WriteHTML($html);

            $filename = 'supplier_' . date('Y_m_d') . '.pdf';

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

        return (new FastExcel($finalExportRows))->download('supplier_' . date('Y_m_d') . '.' . ($request->export_type ?? 'csv'));
    }
}
