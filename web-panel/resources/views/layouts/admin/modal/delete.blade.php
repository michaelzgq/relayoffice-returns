<div class="modal fade" id="deleteModal" tabindex="-1">
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
                <form method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="type" value="delete">
                    <div class="text-center">
                        <img width="80" height="80" src="{{ asset('assets/admin/img/delete.png') }}" alt=""
                             class="mb-4 delete-image">
                        <h3 class="mb-0 title">{{ \App\CPU\translate('Are you sure to delete') }}?</h3>
                        <p class="mt-3 subtitle">{{ \App\CPU\translate('If once you delete this, you will lost this data permanently.') }}</p>
                    </div>
                    <div class="d-flex gap-3 justify-content-center flex-wrap mt-5">
                        <button type="reset" class="btn btn-soft-dark px-4 font-weight-bold min-w-120px cancel-text"
                                data-dismiss="modal">{{ \App\CPU\translate('No') }}</button>
                        <button type="submit"
                                class="btn btn-danger px-4 font-weight-bold min-w-120px confirm-text">{{ \App\CPU\translate('Delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
