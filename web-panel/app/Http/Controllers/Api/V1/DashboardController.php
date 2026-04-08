<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProductsResource;
use App\Traits\ProductTrait;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Transection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    use ProductTrait;
    public function __construct(
        private Transection $transection,
        private Product $product
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getIndex(Request $request)
    {
        if ($request->statistics_type == 'overall') {
            $totalPayableDebit = $this->transection->where('account_id', 2)->where('debit', 1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id', 2)->where('credit', 1)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id', 3)->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id', 3)->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type', 'Income')->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type', 'Refund')->where('account_id', 1)->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $revenueSummary = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $this->transection->where('tran_type', 'Expense')->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'today') {

            $totalPayableDebit = $this->transection->where('account_id', 2)->whereDate('date', '=', Carbon::now()->toDateString())->where('debit', 1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id', 2)->whereDate('date', '=', Carbon::now()->toDateString())->where('credit', 1)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id', 3)->whereDate('date', '=', Carbon::now()->toDateString())->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id', 3)->whereDate('date', '=', Carbon::now()->toDateString())->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type', 'Income')->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type', 'Refund')->where('account_id', 1)->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $revenueSummary = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $this->transection->where('tran_type', 'Expense')->whereDate('date', '=', Carbon::now()->toDateString())->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'month') {

            $totalPayableDebit = $this->transection->where('account_id', 2)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('debit', 1)->sum('amount');
            $totalPayableCredit = $this->transection->where('account_id', 2)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalPayable = $totalPayableCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('account_id', 3)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('account_id', 3)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $totalCollection = $this->transection->where('tran_type', 'Income')->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalCashRefund = $this->transection->where('tran_type', 'Refund')->where('account_id', 1)->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount');
            $totalIncome = $totalCollection - $totalCashRefund;

            $revenueSummary = [
                'totalIncome' => $totalIncome,
                'totalExpense' => $this->transection->where('tran_type', 'Expense')->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year)->sum('amount'),
                'totalPayable' => $totalPayable,
                'totalReceivable' => $totalReceivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function productLimitedStockList(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $products = $this->queryList($request)->paginate($limit, ['*'], 'page', $offset);
        $stockLimitedProducts = ProductsResource::collection($products);

        return response()->json([
            'total' => $stockLimitedProducts->total(),
            'offset' => $offset,
            'limit' => $limit,
            'stock_limited_products' => $stockLimitedProducts->items(),
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getFilter(Request $request)
    {
        if ($request->statistics_type == 'overall') {
            $totalPayableDebit = $this->transection->where('tran_type', 'Payable')->where('debit', 1)->sum('amount');
            $totalPaybleCredit = $this->transection->where('tran_type', 'Payable')->where('credit', 1)->sum('amount');
            $totalPayable = $totalPaybleCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('tran_type', 'Receivable')->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('tran_type', 'Receivable')->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;
            $account = [
                'total_income' => $this->transection->where('tran_type', 'Income')->sum('amount'),
                'total_expense' => $this->transection->where('tran_type', 'Expense')->sum('amount'),
                'total_payable' => $totalPayable,
                'total_receivable' => $totalReceivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Overall Statistics",
                'data' => $account
            ], 200);
        } elseif ($request->statistics_type == 'today') {
            $totalPayableDebit = $this->transection->where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $totalPaybleCredit = $this->transection->where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $totalPayable = $totalPaybleCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;

            $account = [
                'total_income' => $this->transection->where('tran_type', 'Income')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'total_expense' => $this->transection->where('tran_type', 'Expense')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'total_payable' => $totalPayable,
                'total_receivable' => $totalReceivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Today Statistics",
                'data' => $account
            ], 200);
        } elseif ($request->statistics_type == 'month') {

            $totalPayableDebit = $this->transection->where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $totalPaybleCredit = $this->transection->where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $totalPayable = $totalPaybleCredit - $totalPayableDebit;

            $totalReceivableDebit = $this->transection->where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $totalReceivableCredit = $this->transection->where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $totalReceivable = $totalReceivableCredit - $totalReceivableDebit;

            $account = [
                'total_income' => $this->transection->where('tran_type', 'Income')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'total_expense' => $this->transection->where('tran_type', 'Expense')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'total_payable' => $totalPayable,
                'total_receivable' => $totalReceivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Monthly Statistics",
                'data' => $account
            ], 200);
        }
    }

    /**
     * @return JsonResponse
     */
    public function incomeRevenue(): JsonResponse
    {
        $yearWiseExpense = Transection::selectRaw("
        SUM(amount) as total_amount,
        YEAR(date) as year,
        MONTH(date) as month
    ")
            ->where('tran_type', 'Expense')
            ->groupBy('year', 'month') // ✅ Group by both year and month
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        $yearWiseIncome = Transection::selectRaw("
        SUM(CASE WHEN tran_type = 'Income' THEN amount ELSE 0 END) -
        SUM(CASE WHEN tran_type = 'Refund' AND account_id = 1 THEN amount ELSE 0 END) as total_amount,
        YEAR(date) as year,
        MONTH(date) as month
    ")
            ->groupBy('year', 'month')  // Group by both year and month
            ->orderBy('year')
            ->orderBy('month')
            ->get();


        return response()->json([
            'year_wise_expense' => $yearWiseExpense,
            'year_wise_income' => $yearWiseIncome
        ], 200);
    }
}
