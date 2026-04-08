<div class="overlay" id="overlayFilterProduct"></div>
<div class="offcanvas-filter" id="offcanvasFilterProduct" data-overlay="#overlayFilterProduct">
    <div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
        <div class="pl-3 py-2">
            <h4 class="title mb-0">{{ \App\CPU\translate('Filter') }}</h4>
            <p class="mb-0">{{\App\CPU\translate('Filter to quickly find what you need.')}}</p>
        </div>
        <div>
            <button type="button" class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
                <i class="tio-clear"></i>
            </button>
        </div>
    </div>
    <form action="{{ url()->current() }}" method="GET">
        <div class="offcanvas-filter__body px-4 pb-0 pt-4">
            <div class="mb-80">
                <div class="mb-4">
                    <h5 class="text-capitalize mb-3">{{ \App\CPU\translate('Availability') }}</h5>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-control cursor-pointer">
                                <div class="check-item">
                                    <div class="form-group form-check form--check m-0">
                                        <input type="radio" name="availability"
                                               id="exampleRadios1"
                                               value="all"
                                               {{request('availability') == 'all' || request('availability') == ''  ? 'checked' : ''}}
                                               class="form-check-input">
                                        <span
                                            class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('All')}}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control cursor-pointer">
                                <div class="check-item">
                                    <div class="form-group form-check form--check m-0">
                                        <input type="radio" name="availability" id="exampleRadios2"
                                               value="available"
                                               {{request('availability') == 'available' ? 'checked' : ''}}
                                               class="form-check-input" >
                                        <span
                                            class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('Available')}}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control cursor-pointer">
                                <div class="check-item">
                                    <div class="form-group form-check form--check m-0">
                                        <input type="radio" name="availability" id="exampleRadios3"
                                               value="unavailable"
                                               {{request('availability') == 'unavailable' ? 'checked' : ''}}
                                               class="form-check-input">
                                        <span
                                            class="form-check-label ml-2 text-dark fs-12">{{\App\CPU\translate('Unavailable')}}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="text-capitalize mb-3">{{ \App\CPU\translate('Quantity') }}</h5>
                    <div class="row g-2 filter-cat">
                        <div class="col-sm-6 d-flex">
                            <label class="form-control mb-3 cursor-pointer ">
                                <div class="check-item">
                                    <div class="d-flex form-group form-check form--check m-0">
                                        <input type="checkbox" name="stocks[]" id="exampleCheckbox2"
                                               value="low_stock" class="form-check-input"
                                            {{ is_array(request('stocks')) && in_array('low_stock', request('stocks')) ? 'checked' : '' }}>
                                        <span
                                            class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark fs-12"
                                            title="Low Stock">{{\App\CPU\translate('Low Stock')}}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-sm-6 d-flex">
                            <label class="form-control mb-3 cursor-pointer ">
                                <div class="check-item">
                                    <div class="d-flex form-group form-check form--check m-0">
                                        <input type="checkbox" name="stocks[]" id="exampleCheckbox2"
                                               value="out_of_stock" class="form-check-input"
                                            {{ is_array(request('stocks')) && in_array('out_of_stock', request('stocks')) ? 'checked' : '' }}>
                                        <span
                                            class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark fs-12"
                                            title="Out of Stock">{{\App\CPU\translate('Out of Stock')}}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h5 class="text-capitalize mb-3">{{\App\CPU\translate('Price_Range')}}</h5>
                    <div class="range-slider-wrapper">
                        <div class="range-slider">
                            <div class="track rangeTrack"></div>
                            <div class="range-slider_input-wrapper">
                                <input type="range" name="min_price" class="minPrice" min="0"
                                       max="{{ $maxPrice }}" value="{{ request('min_price', $minPrice) }}">
                                <div class="tooltip-thumb tooltipMin"></div>

                            </div>
                            <div class="range-slider_input-wrapper">
                                <input type="range" name="max_price" class="maxPrice" min="0"
                                       max="{{ $maxPrice }}" value="{{ request('max_price', $maxPrice) }}">
                                <div class="tooltip-thumb tooltipMax"></div>

                            </div>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap flex-sm-nowrap mt-4 gap-4">
                            <div class="input-group-wrapper border rounded">
                                <div
                                    class="input_text min-w-80px bg-fafafa fs-10 d-flex justify-content-center align-items-center">
                                    <span>{{\App\CPU\translate('Min_Price')}}:</span>
                                </div>
                                <input type="number" min="0" name=""
                                       class="form-control title minPriceValue fs-12" value="{{ $minPrice }}"
                                       placeholder="Ex: 0">
                            </div>
                            <div class="input-group-wrapper border rounded">
                                <div
                                    class="input_text min-w-80px bg-fafafa fs-10 d-flex justify-content-center align-items-center">
                                    <span>{{\App\CPU\translate('Max_Price')}}:</span>
                                </div>
                                <input type="number" min="0" name=""
                                       class="form-control title maxPriceValue fs-12" value="{{ $maxPrice }}"
                                       placeholder="Ex: 100">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="category_wrapper mb-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="pb-2 text-dark">{{\App\CPU\translate('Category')}}</h6>
                        <div class="check-item">
                            <div class="form-group form-check form--check m-0">
                                <input type="checkbox" id="categorySelectAll" class="form-check-input">
                                <label for="categorySelectAll"
                                       class="form-check-label ml-2 text-dark fs-12">{{ \App\CPU\translate('Select All') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="row filter-cat" id="categoryFilter">
                        @foreach($categories as $category)
                            <div class="col-sm-6 d-flex">
                                <label class="form-control mb-3 cursor-pointer ">
                                    <div class="check-item">
                                        <div class="d-flex form-group form-check form--check m-0">
                                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                                   class="form-check-input category-checkbox"
                                                {{ is_array(request('category_ids')) && in_array($category->id, request('category_ids')) ? 'checked' : '' }}>
                                            <span
                                                class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark fs-12"
                                                title="{{ $category->name }}">{{ $category->name }}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if($categories->count() > 6)
                        <div class="text-center mb-2">
                            <button type="button" class="btn text-primary" id="see_more_category">
                                <strong>{{\App\CPU\translate('See_more')}}
                                    <span>({{$categories->count() - 6}})</span></strong></button>
                        </div>
                    @endif
                </div>

                <!-- Subcategory filter -->
                <div class="category_wrapper mb-3 {{ $subcategories->count() < 1 ? 'd-none' : '' }}"
                     id="subcategory-section">
                    <div class="d-flex justify-content-between">
                        <h6 class="pb-2 text-dark">{{\App\CPU\translate('Sub Category')}}</h6>
                        <div class="check-item">
                            <div class="form-group form-check form--check m-0">
                                <input type="checkbox" id="subCategorySelectAll" class="form-check-input">
                                <label for="subCategorySelectAll"
                                       class="form-check-label text-left ml-2 text-dark fs-12">{{ \App\CPU\translate('Select All') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="row filter-cat" id="subcategoryFilter">
                        <!-- Subcategories will be appended here dynamically -->
                        @foreach ($subcategories as $subcategory)
                            <div class="col-sm-6">
                                <div class="form-control mb-3">
                                    <div class="check-item">
                                        <div class="d-flex form-group form-check form--check m-0">
                                            <input type="checkbox" name="subcategory_ids[]"
                                                   value="{{ $subcategory->id }}"
                                                   class="form-check-input subcategory-checkbox"
                                                {{ is_array(request('subcategory_ids')) && in_array($subcategory->id, request('subcategory_ids')) ? 'checked' : '' }}>
                                            <span
                                                class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark fs-12">{{ $subcategory->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mb-2 {{ $subcategories->count() <= 6 ? 'd-none' : '' }}"
                         id="see_more_subcategory_btn">
                        <button type="button" class="btn text-primary" id="see_more_subcategory">
                            <strong>{{\App\CPU\translate('See_more')}}<span id="more-sub-category-count"> ({{$subcategories->count() - 6}})</span></strong>
                        </button>
                    </div>
                </div>

                @if($brands->count() > 0)
                    <div class="category_wrapper mb-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="pb-2 text-dark">{{\App\CPU\translate('Brand')}}</h6>
                            <div class="check-item">
                                <div class="form-group form-check form--check m-0 ">
                                    <input type="checkbox" id="brandSelectAll" class="form-check-input">
                                    <label for="brandSelectAll"
                                           class="form-check-label ml-2 text-dark fs-12">{{ \App\CPU\translate('Select All') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row filter-cat" id="brandFilter">
                            @foreach($brands as $brand)
                                <div class="col-sm-6">
                                    <label class="form-control mb-3 cursor-pointer">
                                        <div class="check-item">
                                            <div class="d-flex form-group form-check form--check m-0">
                                                <input type="checkbox" name="brand_ids[]" value="{{ $brand->id }}"
                                                       class="form-check-input brand-checkbox"
                                                    {{ is_array(request('brand_ids')) && in_array($brand->id, request('brand_ids')) ? 'checked' : '' }}>
                                                <span
                                                    class="align-content-center form-check-label ml-2 text-dark line-limit-1 fs-12"
                                                    title="{{ $brand->name }}">{{ $brand->name }}</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @if($brands->count() > 6)
                            <div class="text-center mb-2">
                                <button type="button" class="btn text-primary" id="see_more_brand">
                                    <strong>{{\App\CPU\translate('See_more')}}
                                        <span>({{$brands->count() - 6}})</span></strong></button>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="category_wrapper mb-3">
                    <h6 class="pb-2 text-dark">{{\App\CPU\translate('Supplier')}}</h6>
                    <div>
                        <select name="supplier_id" class="form-control js-select2-custom">
                            <option value="all">{{\App\CPU\translate('All Supplier')}}</option>
                            @foreach($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}" {{ request()->has('supplier_id') && request()->query('supplier_id') == $supplier->id ? 'selected' : ''  }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @if(request()->has('search'))
            <input type="hidden" name="search" value="{{ request()->input('search') }}">
        @endif
        <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
                <a href="{{ url()->current() }}" class="btn btn-light px-4 flex-grow-1 fw-semibold closeOfcanvus"
                   id="cancel_filter">{{\App\CPU\translate('Clear_Filter')}}</a>
                <button type="submit"
                        class="btn btn-primary px-4 flex-grow-1 fw-semibold closeOfcanvus">{{\App\CPU\translate('Apply')}}</button>
            </div>
        </div>
    </form>
</div>
<input type="hidden" id="seeMoreCategory" name="see-more-category" value="{{ $categories->count() - 6 }}">
<input type="hidden" id="seeMoreBrand" name="see-more-brand" value="{{ $brands->count() - 6 }}">
<input type="hidden" id="seeMoreSubcategory" name="see-more-subcategory" value="{{ $subcategories->count() - 6 }}">
