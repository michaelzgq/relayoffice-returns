<div class="overlay" id="overlayFilter"></div>
<div class="offcanvas-filter" id="offcanvasFilterCat" data-overlay="#overlayFilter">
    <div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
        <div class="pl-3 py-2">
            <h4 class="title mb-0">{{ \App\CPU\translate('Filter') }}</h4>
            <p class="mb-0">{{ \App\CPU\translate('Filter to quickly find what you need') }}.</p>
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
                <label for="" class="input-label font-weight-medium text-capitalize mb-3">{{ \App\CPU\translate('Date') }}</label>
                <?php
                $startDateTime = request()->get('start_date');
                $endDateTime = request()->get('end_date');
                ?>
                <button type="button" class="btn btn-white flex-grow-1 d-flex gap-10 align-items-center justify-content-between w-100 dateRange">
                    <span data-placeholder="{{ \App\CPU\translate('select_date') }}">{{ \App\CPU\translate('select_date') }}</span>
                    <img class="svg" src="{{ asset('assets/admin/img/clock.svg') }}" alt="">
                </button>
            </div>
            <div class="mb-80">
                <h5 class="text-capitalize mb-3">{{ \App\CPU\translate('Sorting') }}</h5>
                <div class="row g-2 mb-6">
                    <div class="col-sm-6">
                        <label class="form-control cursor-pointer">
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0">
                                    <input type="radio" name="sorting_type" id="exampleRadios1" value="latest"
                                           {{ (!request()->has('sorting_type') || request('sorting_type') === 'latest') ? 'checked' : '' }}
                                           class="form-check-input category-checkbox">
                                    <span class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('default_(recent_created)')}}</span>
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
                                    <span class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('show_older_first')}}</span>
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
                                    <span class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('A_to_Z')}}</span>
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
                                    <span class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('Z_to_A')}}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="start_date" id="start_date_value" value="{{ $startDateTime }}">
        <input type="hidden" name="end_date" id="end_date_value" value="{{ $endDateTime }}">
        @if(request()->has('search'))
            <input type="hidden" name="search" value="{{ request()->input('search') }}">
        @endif
        <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
                <a href="{{ url()->current() }}" class="btn btn-light px-4 flex-grow-1 fw-semibold closeOfcanvus">
                    {{ \App\CPU\translate('Clear_Filter') }}
                </a>
                <button type="submit"
                        class="btn btn-primary px-4 flex-grow-1 fw-semibold closeOfcanvus">{{ \App\CPU\translate('Apply') }}</button>
            </div>
        </div>
    </form>
</div>
