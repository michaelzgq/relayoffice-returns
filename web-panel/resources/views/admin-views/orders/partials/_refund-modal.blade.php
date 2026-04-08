@php
    use function App\CPU\translate;
    use App\CPU\Helpers;
@endphp
<div class="modal fade" id="order_refund_modal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable custom-scrollable">
        <div class="modal-content">
            <div class="modal-header p-3 border-bottom">
                <h5 class="modal-title text-center flex-grow-1">{{ translate('Order Refund') }}</h5>
                <button type="button" class="btn btn-soft-secondary px-1 py-0 rounded-circle" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">
                            <i class="tio-clear"></i>
                        </span>
                </button>
            </div>
            <form action="{{ route('admin.order.refund', $order?->id) }}" method="POST" id="">
                @csrf
                <div class="modal-body">
                    <div class="bg-light rounded-10 p-4">
                        <div class="form-group">
                            <label
                                class="input-label font-weight-medium text-capitalize">{{ translate('Refund Amount') }}
                                ({{ Helpers::currency_symbol() }}) <small class="text-danger">*</small></label>

                            <input type="number" name="refund_amount" class="form-control h-44px"
                                   value="{{ $order?->order_amount + $order?->total_tax - ($order?->coupon_discount_amount ?? 0) - ($order?->extra_discount ?? 0) }}"
                                   placeholder="{{ '$' . $order?->order_amount + $order?->total_tax - ($order?->coupon_discount_amount ?? 0) - ($order?->extra_discount ?? 0) }}">
                        </div>

                        <div class="form-group character-count">
                            <label
                                class="input-label font-weight-medium text-capitalize">{{ translate('Refund Cause') }}</label>
                            <textarea class="form-control character-count-field" name="refund_reason" id=""
                                      cols="30" rows="2" maxlength="100" data-max-character="100"
                                      placeholder="Type refund note"></textarea>
                            <p class="text-end text-black-50 mb-0">{{ translate('0/100') }}</p>
                        </div>

                        <div class="form-group border p-3 rounded-10 bg-white">
                            <label
                                class="input-label font-weight-medium text-capitalize">{{ translate('Give Refunded From (Admin Account)') }}</label>

                            <ul class="nav flex-nowrap nav-pills nav--pills swiper-wrapper">
                                <li class="nav-item swiper-slide" role="presentation">
                                    <label class="nav-link active change_payment_method cursor-pointer border rounded" data-toggle="pill"
                                           data-target="#PaymentCash"
                                           aria-selected="true">
                                        <span>{{ \App\CPU\translate('Cash') }}</span>
                                        <input type="radio" name="admin_payment_method" value="1"
                                               class="payment-method-radio d-none "
                                               checked>
                                    </label>
                                </li>
                                @foreach (\App\Models\Account::all() as $account)
                                    @if ($account['id'] != 1 && $account['id'] != 2 && $account['id'] != 3)
                                        <li class="nav-item swiper-slide" role="presentation">
                                            <label class="nav-link change_payment_method cursor-pointer border rounded" data-toggle="pill"
                                                   aria-selected="false">
                                                <span>{{ $account['account'] }}</span>
                                                <input type="radio" name="admin_payment_method" value="{{ $account['id'] }}"
                                                       class="payment-method-radio d-none">
                                            </label>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>

                        <div class="form-group border p-3 rounded-10 bg-white">
                            <label
                                class="input-label font-weight-medium text-capitalize">{{ translate('Give Customer Via') }}</label>
                                <div>
                                    <ul class="nav flex-nowrap nav-pills nav--pills swiper-wrapper">
                                        <li class="nav-item swiper-slide" role="presentation">
                                            <label class="nav-link active change_payment_method cursor-pointer border rounded" data-toggle="pill" data-target="#PaymentCash"
                                                   aria-selected="true">
                                                <span>{{ \App\CPU\translate('Cash') }}</span>
                                                <input type="radio" name="customer_payout_method" value="cash" class="payment-method-radio d-none "
                                                       checked>
                                            </label>
                                        </li>
                                        @if($order->user_id !== 0)
                                            <li class="nav-item swiper-slide wallet-payment-section" role="presentation"
                                                id="wallet-payment-section">
                                                <label class="nav-link change_payment_method cursor-pointer border rounded" data-toggle="pill"
                                                       aria-selected="false">
                                                    <span>{{ \App\CPU\translate('Wallet') }}</span>
                                                    <input type="radio" name="customer_payout_method" value="wallet" class="payment-method-radio d-none">
                                                </label>
                                            </li>
                                        @endif
                                        <li class="nav-item swiper-slide other-section" role="presentation"
                                            id="wallet-payment-section">
                                            <label class="nav-link change_payment_method cursor-pointer border rounded" data-toggle="pill" data-target="#otherPayment"
                                                   aria-selected="false">
                                                <span>{{ \App\CPU\translate('Other') }}</span>
                                                <input type="radio" name="customer_payout_method" value="other" class="payment-method-radio d-none">
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            <div class="tab-content fs-13 text-dark">
                                <div class="tab-pane" id="otherPayment">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">{{ \App\CPU\translate('Payment Method') }}</label>
                                        <input type="text" name="payment_method" class="form-control text-sm" id="" placeholder="Ex: ABC Bank">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">{{ \App\CPU\translate('Payment Info') }}</label>
                                        <textarea class="form-control" name="payment_info" id=""
                                                  cols="30" rows="2" maxlength="100"
                                                  placeholder="Ex: ac no"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <div class="d-flex gap-3 justify-content-end">
                        <button type="reset" class="btn btn-soft-danger px-4 font-weight-bold min-w-94px"
                                data-dismiss="modal">Cancel
                        </button>
                        <button type="submit"
                                class="btn btn-primary px-4 font-weight-bold min-w-94px submit-refund-form">Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
