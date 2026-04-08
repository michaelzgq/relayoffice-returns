<div class="modal fade" id="update-quantity" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <button type="button" class="text-dark bg-f2f2f2 rounded-circle p-1 close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">
                            <i class="tio-clear"></i>
                        </span>
                </button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{route('admin.stock.update-quantity', $product['id'])}}" method="post">
                    @csrf
                    <div class="text-center">
                        <h3 class="mb-0">{{ \App\CPU\translate('Update_Quantity') }}</h3>
                        <p class="mt-3">
                            {{ \App\CPU\translate('Enter the product quantity to be updated') }}
                        </p>
                        <div class="bg-fafafa p-4 rounded">
                            <input type="number"
                                   class="form-control text-center"
                                   name="quantity"
                                   min="0"
                                   value="{{ $product['quantity'] }}"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                   required>
                            <input type="hidden" id="product_id" name="id" value="{{ $product['id']??0 }}">
                        </div>
                    </div>
                    <div class="mt-3 alert alert-soft-primary d-flex gap-2 align-items-center">
                        <i class="fi fi-sr-lightbulb-on"></i>
                        <span class="title opacity-lg">
                                {{ \App\CPU\translate('ensure_the_input_value_is_0_or_greater_than_0') }}
                            </span>
                    </div>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                        <button type="button" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px"
                                data-dismiss="modal">{{ \App\CPU\translate('Cancel') }}</button>
                        <button type="submit"
                                class="btn btn-primary px-4 font-weight-bold min-w-120px">{{ \App\CPU\translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
