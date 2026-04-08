<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Transection;
use App\CPU\Helpers;
use Carbon\Carbon;
use App\Models\Account;
use App\Models\Product;
use Illuminate\Pagination\Paginator;

class DashboardController extends Controller
{
    public function __construct(
        private Transection $transection,
        private Account $account,
        private Product $product
    ){}

    /**
     * @return RedirectResponse
     */
    public function dashboard(): RedirectResponse
    {
        if (Helpers::returns_user_is_inspector()) {
            return redirect()->route('admin.returns.inspect');
        }

        if (Helpers::admin_has_module('returns_ops_board_section')) {
            return redirect()->route('admin.returns.dashboard.index');
        }

        if (Helpers::admin_has_module('returns_queue_section')) {
            return redirect()->route('admin.returns.queue.index');
        }

        if (Helpers::admin_has_module('returns_cases_section')) {
            return redirect()->route('admin.returns.cases.index');
        }

        return redirect()->route('admin.returns.inspect');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function accountStats(Request $request): JsonResponse
    {
        if($request->statistics_type=='overall')
        {
            $totalPayableDebit = $this->transection->where('account_id',2)->where('debit',1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id',2)->where('credit',1)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id',3)->where('debit',1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id',3)->where('credit',1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type','Income')->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type','Refund')->where('account_id',1)->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $account = [
                'totalIncome' => $totalIncome ,
                'totalExpense' => $this->transection->where('tran_type','Expense')->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
        }elseif ($request->statistics_type=='today') {

            $totalPayableDebit = $this->transection->where('account_id',2)->whereDate('date', '=', Carbon::now()->toDateString())->where('debit',1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id',2)->whereDate('date', '=', Carbon::now()->toDateString())->where('credit',1)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id',3)->whereDate('date', '=', Carbon::now()->toDateString())->where('debit',1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id',3)->whereDate('date', '=', Carbon::now()->toDateString())->where('credit',1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type','Income')->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type','Refund')->where('account_id',1)->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $account = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $this->transection->where('tran_type','Expense')->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
        }elseif ($request->statistics_type=='month') {

            $totalPayableDebit = $this->transection->where('account_id',2)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('debit',1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id',2)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id',3)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('debit',1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id',3)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('credit',1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type','Income')->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type','Refund')->where('account_id',1)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $account = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $this->transection->where('tran_type','Expense')->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
        }
        return response()->json([
            'view'=> view('admin-views.partials._dashboard-balance-stats',compact('account'))->render()
        ],200);
    }

}
