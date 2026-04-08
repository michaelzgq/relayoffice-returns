<div class="modal fade" id="deleteModalWithShift" tabindex="-1">
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
            <div class="modal-body pt-0 body-brand-delete">
                <form method="POST" action="{{ route('admin.brand.delete', $resource['id']) }}">
                    @csrf
                    @method('DELETE')
                    <div class="text-center">
                        <img width="80" height="80" src="{{ asset('assets/admin/img/delete.png') }}" alt=""
                             class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('Are you sure to delete this brand') }}</h3>
                        <p class="mt-3">
                            {{ trans(key: 'This brand has :productCount '.  \Illuminate\Support\Str::plural('product', $resource['product_count']) . '. Once you delete, you will lost this data permanently.', replace: ['productCount' => $resource['product_count']]) }}
                        </p>
                    </div>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                        <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px"
                                data-dismiss="modal">{{ \App\CPU\translate('No') }}</button>
                        <button type="submit"
                                class="btn btn-danger px-4 font-weight-bold min-w-120px">{{ \App\CPU\translate('Delete') }}</button>
                    </div>
                    <div class="text-center">
                        <p class="pt-5  pb-3 mb-0">
                            {{ \App\CPU\translate('Want to shift them to another brand before deleting?') }}
                        </p>
                        <p class="pb-3 mb-0 text-decoration-underline font-weight-bold color-245BD1 cursor-pointer brand-delete-modal-click-here">
                            {{ \App\CPU\translate('Click here') }}
                        </p>
                    </div>
                </form>
            </div>
            <div class="modal-body pt-0 body-brand-shift-and-delete d-none">
                <form method="POST" action="{{ route('admin.brand.delete', $resource['id']) }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="type" value="shift_and_delete">
                    <div class="text-center">
                        <img width="80" height="80" src="{{ asset('assets/admin/img/delete.png') }}" alt=""
                             class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('Need to Shift Brand to Another Brand') }}</h3>
                        <p class="mt-3">
                            {{ trans(key: 'This brand has :productCount '.  \Illuminate\Support\Str::plural('product', $resource['product_count']) . '. Please shift them to another brand before deleting', replace: ['productCount' => $resource['product_count']]) }}
                        </p>
                        <div class="mt-4 d-flex justify-content-between gap-3 flex-column flex-md-row">
                            <div class="flex-grow-1 text-start">
                                <label for="">{{  \App\CPU\translate('Choose Brand') }}</label>
                                <select name="resource_id" id="resource-select"
                                        class="form-control js-select2-custom">
                                    @foreach($resources as $parentResource)
                                        <option value="{{ $parentResource['id'] }}" >{{ $parentResource['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                        <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px"
                                data-dismiss="modal">{{ \App\CPU\translate('No') }}</button>
                        <button type="submit"
                                class="btn btn-danger px-4 font-weight-bold min-w-120px brand-delete-button">{{ \App\CPU\translate('Shift & Delete') }}</button>
                    </div>
                    <div class="text-center">
                        <p class="mt-4 text-decoration-underline font-weight-bold color-245BD1 cursor-pointer brand-delete-modal-go-back">
                            {{ \App\CPU\translate('Go Back') }}
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
