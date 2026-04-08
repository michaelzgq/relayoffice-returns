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
            <div class="modal-body pt-0">
                <form method="POST" action="{{ route('admin.unit.delete', $resource['id']) }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="type" value="shift_and_delete">
                    <div class="text-center">
                        <img width="80" height="80" src="{{ asset('assets/admin/img/delete.png') }}" alt=""
                             class="mb-4">
                        <h3 class="mb-0">{{ \App\CPU\translate('Need to Shift Unit to Another Unit') }}</h3>
                        <p class="mt-3">
                            {{ trans(key: 'This unit has :productCount '.  \Illuminate\Support\Str::plural('product', $resource['product_count']) . '. Please shift them to another unit before deleting', replace: ['productCount' => $resource['product_count']]) }}
                        </p>
                        <div class="mt-4 d-flex justify-content-between gap-3 flex-column flex-md-row">
                            <div class="flex-grow-1 text-start">
                                <label for="">{{  \App\CPU\translate('Choose Unit') }}</label>
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
                                class="btn btn-danger px-4 font-weight-bold min-w-120px">{{ \App\CPU\translate('Shift & Delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
