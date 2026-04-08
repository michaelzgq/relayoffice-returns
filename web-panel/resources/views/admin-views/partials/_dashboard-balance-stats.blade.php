<div class="col-sm-12 col-lg-4">
    <a class="card card-hover-shadow h-100 color-one" href="#">
        <div class="card-body">
            <div class="flex-between align-items-start flex-wrap gap-3">
                <div class="flex-between flex-column h-100 gap-2">
                    <span class="card-title h1 fw-bold text-white">
                        {{ $account['totalIncome']-$account['totalExpense'] ." ".\App\CPU\Helpers::currency_symbol()}}
                    </span>
                    <h6 class="card-subtitle text-capitalize mb-0 text-white">{{\App\CPU\translate('total_revenue')}}</h6>
                </div>
                <div class="text-white">
                    <img class="svg" src="{{asset('assets/admin')}}/img/dashboard/total-revenue.svg" alt="Image Description">
                </div>
            </div>
        </div>
    </a>
</div>
<div class="col-sm-6 col-lg-4">
    <a class="card card-hover-shadow h-100 color-two" href="#">
        <div class="card-body">
            <div class="flex-between align-items-start flex-wrap gap-3">
                <div class="flex-between flex-column h-100 gap-2">
                    <span class="card-title h1 fw-bold text-white">
                        {{ $account['totalIncome'] ." ".\App\CPU\Helpers::currency_symbol()}}
                    </span>
                    <h6 class="card-subtitle text-capitalize mb-0 text-white">{{\App\CPU\translate('total_Income')}}</h6>
                </div>
                <div class="text-white">
                    <img class="svg" src="{{asset('assets/admin')}}/img/dashboard/total-income.svg" alt="Image Description">
                </div>
            </div>
        </div>
    </a>
</div>
<div class="col-sm-6 col-lg-4">
    <a class="card card-hover-shadow h-100 color-three" href="#">
        <div class="card-body">
            <div class="flex-between align-items-start flex-wrap gap-3">
                <div class="flex-between flex-column h-100 gap-2">
                    <span class="card-title h1 fw-bold text-white">
                        {{ $account['totalExpense'] ." ".\App\CPU\Helpers::currency_symbol()}}
                    </span>
                    <h6 class="card-subtitle text-capitalize mb-0 text-white">{{\App\CPU\translate('total_Expense')}}</h6>
                </div>
                <div class="text-white">
                    <img class="svg" src="{{asset('assets/admin')}}/img/dashboard/total-expense.svg" alt="Image Description">
                </div>
            </div>
        </div>
    </a>
</div>

<div class="col-sm-6 col-lg-6">
    <a class="card card-hover-shadow h-100 color-four" href="#">
        <div class="card-body">
            <div class="flex-between align-items-start flex-wrap gap-3">
                <div class="flex-between flex-column h-100 gap-2">
                    <span class="card-title h1 fw-bold title">
                        {{ $account['totalPayable'] ." ".\App\CPU\Helpers::currency_symbol()}}
                    </span>
                    <h6 class="card-subtitle text-capitalize mb-0 title">{{\App\CPU\translate('account_payable')}}</h6>
                </div>
                <div class="text--warning">
                    <img class="svg" src="{{asset('assets/admin')}}/img/dashboard/total-payable.svg" alt="Image Description">
                </div>
            </div>
        </div>
    </a>
</div>
<div class="col-sm-6 col-lg-6">
    <a class="card card-hover-shadow h-100 color-five" href="#">
        <div class="card-body">
            <div class="flex-between align-items-start flex-wrap gap-3">
                <div class="flex-between flex-column h-100 gap-2">
                    <span class="card-title h1 fw-bold title">
                        {{ $account['totalReceivable'] ." ".\App\CPU\Helpers::currency_symbol()}}
                    </span>
                    <h6 class="card-subtitle text-capitalize mb-0 title">{{\App\CPU\translate('account_receivable')}}</h6>
                </div>
                <div class="text--success">
                    <img class="svg" src="{{asset('assets/admin')}}/img/dashboard/total-receivable.svg" alt="Image Description">
                </div>
            </div>
        </div>
    </a>
</div>
