{{-- Filter Offcancvas --}}
<div class="offcanvas-filter filter-offcanvas" id="offcanvasFilterMenu">
    <div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
        <div class="pl-3 py-2">
            <h4 class="title">Filter</h4>
            <p class="mb-0">Filter to quickly find what you need.</p>
        </div>
        <div>
            <button class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
                <i class="tio-clear"></i>
            </button>
        </div>
    </div>
    <div class="offcanvas-filter__body px-4 pb-0 pt-3">
        <div class="mb-4">
            <label for=""
                   class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('counter number') }}</label>
            <select id='' name="counter_id" class="form-control js-select2-custom change-counter">
                <option value="all">All Counter</option>
                @foreach($counters as $key => $counter)
                    <option value="{{ $counter?->id }}" {{ request()->counter_id == $counter?->id ? 'selected' : '' }}> {{ $counter?->number . ' (' . $counter?->name . ')'  }} </option>
                @endforeach
            </select>

        </div>
        <div class="mb-4">
            <label for=""
                   class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('Customer Info') }}</label>
            <select id='' name="customer_id" class="form-control js-select2-custom customer-change">
                <option value="all" {{ empty(request()->customer_id) ? 'selected' : ''  }}>All Customer</option>
                @foreach($customers as $key => $customer)
                    <option value="{{ $customer?->id }}" {{ !empty(request()->customer_id) && request()->customer_id == $customer->id ? 'selected' : '' }}> {{ $customer?->name }} {{ $customer->id == 0 ? '' :   ' (' . $customer?->mobile . ')'  }} </option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for=""
                   class="input-label font-weight-medium text-capitalize">{{ \App\CPU\translate('Payment Method') }}</label>
            <div class="row g-2 mb-6">
                <div class="col-sm-6">
                    <label class="form-control cursor-pointer">
                        <div class="check-item">
                            <div class="form-group form-check form--check m-0">
                                <input type="checkbox" name="payment_method_id[]" value="0"
                                       class="form-check-input category-checkbox" {{ !empty(request()->payment_method_id) && in_array(0, request()->payment_method_id) ? 'checked' : '' }}>
                                <span class="form-check-label ml-2 text-dark">{{\App\CPU\translate('wallet')}}</span>
                            </div>
                        </div>
                    </label>
                </div>
                @foreach($accounts as $account)
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="checkbox" name="payment_method_id[]" value="{{ $account?->id }}"
                                           class="form-check-input category-checkbox" {{ !empty(request()->payment_method_id) && in_array($account?->id, request()->payment_method_id) ? 'checked' : '' }}>
                                    <span class="form-check-label ml-2 text-dark">{{ $account?->account }}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
            <button type="button"
                    class="btn btn-soft-primary px-4 flex-grow-1 closeOfcanvus btn-clear-filter">{{ \App\CPU\translate('Clear_Filter') }}</button>
            <button type="submit"
                    class="btn btn-primary px-4 flex-grow-1 closeOfcanvus btn-apply">{{ \App\CPU\translate('Apply') }}</button>
        </div>
    </div>
</div>
