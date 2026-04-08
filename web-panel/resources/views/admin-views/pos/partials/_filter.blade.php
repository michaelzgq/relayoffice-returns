<div class="offcanvas-filter filter-offcanvas" id="offcanvasFilterMenu">
    <div class="offcanvas-filter__header">
        <h4>{{\App\CPU\translate('Filter')}}</h4>
        <p>{{\App\CPU\translate('Filter to quickly find what you need.')}}</p>
    </div>
    <form action="{{ url()->current() }}" method="GET">
        <div class="offcanvas-filter__body">
            <div class="mb-3">
                <h6 class="pb-2">{{\App\CPU\translate('Price_Range')}}</h6>
                <div class="range-slider">
                    <div class="track" id="rangeTrack"></div>
                    <input type="range" name="min_price" id="minPrice" min="{{ $minPrice }}" max="{{$maxPrice}}" value="{{ request('min_price', $minPrice) }}">
                    <input type="range" name="max_price" id="maxPrice" min="{{ $minPrice }}" max="{{ $maxPrice }}" value="{{ request('max_price', $maxPrice) }}">
                </div>
                <div class="price-range-info d-flex justify-content-between mt-2 gap-4">
                    <span class="form-control text-center">{{\App\CPU\translate('Min_Price')}}: <strong id="minPriceValue">{{request('min_price', $minPrice)}}</strong></span>
                    <span class="form-control text-center">{{\App\CPU\translate('Max_Price')}}: <strong id="maxPriceValue">{{request('max_price', $maxPrice)}}</strong></span>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <h6 class="pb-2">{{\App\CPU\translate('Category')}}</h6>
                    <div class="check-item">
                        <div class="form-group form-check form--check m-0">
                            <input type="checkbox" id="categorySelectAll" class="form-check-input">
                            <label for="categorySelectAll" class="form-check-label ml-2 text-dark">{{ \App\CPU\translate('Select All') }}</label>
                        </div>
                    </div>
                </div>

                <div class="row filter-cat" id="categoryFilter">
                    @foreach($categories as $category)
                        <div class="col-sm-6 d-flex">
                            <label class="form-control mb-3 cursor-pointer ">
                                <div class="check-item">
                                    <div class="d-flex form-group form-check form--check m-0">
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="form-check-input category-checkbox"
                                            {{ is_array(request('category_ids')) && in_array($category->id, request('category_ids')) ? 'checked' : '' }}>
                                        <span class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark" title="{{ $category->name }}" >{{ $category->name }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @if($categories->count() > 6)
                    <div class="text-center mb-2">
                        <button type="button" class="btn text-primary" id="see_more_category"><strong>{{\App\CPU\translate('See_more')}} <span>({{$categories->count() - 6}})</span></strong></button>
                    </div>
                @endif
            </div>

            <!-- Subcategory filter -->
            <div class="mb-3 {{ $subcategories->count() < 1 ? 'd-none' : '' }}" id="subcategory-section">
                <div class="d-flex justify-content-between">
                    <h6 class="pb-2">{{\App\CPU\translate('Sub Category')}}</h6>
                    <div class="check-item">
                        <div class="form-group form-check form--check m-0">
                            <input type="checkbox" id="subCategorySelectAll" class="form-check-input">
                            <label for="subCategorySelectAll" class="form-check-label text-left ml-2 text-dark">{{ \App\CPU\translate('Select All') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row filter-cat" id="subcategoryFilter">
                    <!-- Subcategories will be appended here dynamically -->
                    @foreach ($subcategories as $subcategory)
                        <div class="col-sm-6">
                            <label class="form-control mb-3">
                                <div class="check-item">
                                    <div class="d-flex form-group form-check form--check m-0">
                                        <input type="checkbox" name="subcategory_ids[]" value="{{ $subcategory->id }}" class="form-check-input subcategory-checkbox"
                                            {{ is_array(request('subcategory_ids')) && in_array($subcategory->id, request('subcategory_ids')) ? 'checked' : '' }}>
                                        <span class=" align-content-center form-check-label line-limit-1 text-left ml-2 text-dark">{{ $subcategory->name }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mb-2 {{ $subcategories->count() <= 6 ? 'd-none' : '' }}" id="see_more_subcategory_btn">
                    <button type="button" class="btn text-primary" id="see_more_subcategory">
                        <strong>{{\App\CPU\translate('See_more')}}<span id="more-sub-category-count"> ({{$subcategories->count() - 6}})</span></strong>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <h6 class="pb-2">{{\App\CPU\translate('Brand')}}</h6>
                    <div class="check-item">
                        <div class="form-group form-check form--check m-0">
                            <input type="checkbox" id="brandSelectAll" class="form-check-input">
                            <label for="brandSelectAll" class="form-check-label ml-2 text-dark">{{ \App\CPU\translate('Select All') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row filter-cat" id="brandFilter">
                    @foreach($brands as $brand)
                        <div class="col-sm-6">
                            <label class="form-control mb-3">
                                <div class="check-item">
                                    <div class="d-flex form-group form-check form--check m-0">
                                        <input type="checkbox" name="brand_ids[]" value="{{ $brand->id }}" class="form-check-input brand-checkbox"
                                            {{ is_array(request('brand_ids')) && in_array($brand->id, request('brand_ids')) ? 'checked' : '' }}>
                                        <span class="align-content-center form-check-label ml-2 text-dark line-limit-1" title="{{ $brand->name }}">{{ $brand->name }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @if($brands->count() > 6)
                    <div class="text-center mb-2">
                        <button type="button" class="btn text-primary" id="see_more_brand"><strong>{{\App\CPU\translate('See_more')}} <span>({{$brands->count() - 6}})</span></strong></button>
                    </div>
                @endif
            </div>
        </div>
        <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
            <div class="d-flex justify-content-center align-items-center w-100">
                <a href="{{ route('admin.pos.index') }}" class="btn btn-soft-primary mr-2 px-4" id="cancel_filter">{{\App\CPU\translate('Cancel')}}</a>
                <button type="submit" class="btn btn-primary px-4">{{\App\CPU\translate('Apply')}}</button>
            </div>
        </div>
    </form>
</div>
