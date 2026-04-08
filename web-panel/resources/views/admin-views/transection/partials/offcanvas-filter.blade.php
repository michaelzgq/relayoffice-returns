@php use function App\CPU\translate; @endphp
<div class="overlay" id="overlayFilter"></div>
<div class="offcanvas-filter" id="offcanvasFilterCat" data-overlay="#overlayFilter">
    <div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
        <div class="pl-3 py-2">
            <h4 class="title mb-0">{{ translate('Filter') }}</h4>
            <p class="mb-0">{{ translate('Filter to quickly find what you need') }}.</p>
        </div>
        <div>
            <button type="button" class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
                <i class="tio-clear"></i>
            </button>
        </div>
    </div>
    <form action="{{ url()->current() }}" method="GET">
        <div class="offcanvas-filter__body px-4 pb-0 pt-4">
            <div class="mb-4">
                <label for=""
                       class="input-label font-weight-medium text-capitalize mb-3">{{ translate('Date') }}</label>
                <?php
                $startDateTime = request()->get('start_date');
                $endDateTime = request()->get('end_date');
                ?>
                <button type="button"
                        class="btn btn-white flex-grow-1 d-flex gap-10 align-items-center justify-content-between w-100 dateRange">
                    <span data-placeholder="{{ translate('select_date') }}">{{ translate('select_date') }}</span>
                    <img class="svg" src="{{ asset('assets/admin/img/clock.svg') }}" alt="">
                </button>
            </div>
            <div class="mb-80">
                <h5 class="text-capitalize mb-3">{{ translate('Sorting') }}</h5>
                <div class="row g-2 mb-6">
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="radio" name="sorting_type" id="exampleRadios1" value="latest"
                                           {{ (!request()->has('sorting_type') || request('sorting_type') === 'latest') ? 'checked' : '' }}
                                           class="form-check-input category-checkbox">
                                    <span
                                        class="form-check-label ml-2 text-dark fs-12">{{translate('default_(recent_created)')}}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="radio" name="sorting_type" id="exampleRadios2" value="oldest"
                                           {{ (request()->input('sorting_type') ?? '') === 'oldest' ? 'checked' : '' }}
                                           class="form-check-input category-checkbox">
                                    <span
                                        class="form-check-label ml-2 text-dark fs-12">{{translate('show_older_first')}}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="radio" name="sorting_type" id="exampleRadios3" value="ascending"
                                           {{ (request()->input('sorting_type') ?? '') === 'ascending' ? 'checked' : '' }}
                                           class="form-check-input category-checkbox">
                                    <span class="form-check-label ml-2 text-dark fs-12">{{translate('A_to_Z')}}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="radio" name="sorting_type" id="exampleRadios4" value="descending"
                                           {{ (request()->input('sorting_type') ?? '') === 'descending' ? 'checked' : '' }}
                                           class="form-check-input category-checkbox">
                                    <span class="form-check-label ml-2 text-dark fs-12">{{translate('Z_to_A')}}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-sm-12">
                        <h6 class="pb-2 text-dark">{{translate('Account')}}</h6>
                        <div>
                            <select name="account_id" class="form-control js-select2-custom">
                                <option value="all">{{translate('All Accounts')}}</option>
                                @foreach($accounts as $account)
                                    <option
                                        value="{{ $account->id }}" {{ request()->has('account_id') && request()->query('account_id') == $account->id ? 'selected' : ''  }}>{{ $account->account }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <h6 class="pb-2 text-dark">{{translate('Type')}}</h6>
                        <div>
                            <select id="tran_type" name="transaction_type" class="form-control js-select2-custom">
                                <option value="all">{{translate('All Types')}}</option>
                                <option
                                    value="Expense" {{ request()->input('transaction_type') == 'Expense'?'selected':''}}>{{translate('expense')}}</option>
                                <option
                                    value="Transfer" {{ request()->input('transaction_type') == 'Transfer'?'selected':''}}>{{translate('transfer')}}</option>
                                <option
                                    value="Income" {{ request()->input('transaction_type') == 'Income'?'selected':''}}>{{translate('income')}}</option>
                                <option
                                    value="Payable" {{ request()->input('transaction_type') == 'Payable'?'selected':''}}>{{translate('payable')}}</option>
                                <option
                                    value="Receivable" {{ request()->input('transaction_type') == 'Receivable'?'selected':''}}>{{translate('receivable')}}</option>
                                <option
                                    value="Refund" {{ request()->input('transaction_type') == 'Refund'?'selected':''}}>{{translate('refund')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="start_date" id="start_date_value" value="{{ $startDateTime }}">
        <input type="hidden" name="end_date" id="end_date_value" value="{{ $endDateTime }}">
        @if(request()->has('search'))
            <input type="hidden" name="search" value="{{ request()->input('search') }}">
        @endif
        @if(request()->has('per_page'))
            <input type="hidden" name="per_page" value="{{ request()->input('per_page') }}">
        @endif
        <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
                <a href="{{ url()->current() }}" class="btn btn-light px-4 flex-grow-1 fw-semibold closeOfcanvus">
                    {{ translate('Clear_Filter') }}
                </a>
                <button type="submit"
                        class="btn btn-primary px-4 flex-grow-1 fw-semibold closeOfcanvus">{{ translate('Apply') }}</button>
            </div>
        </div>
    </form>
</div>
