<div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
    <div class="pl-3 py-2">
        <h4 class="title mb-0">{{ \App\CPU\translate('Edit Sub Category') }}</h4>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <button class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
            <i class="tio-clear"></i>
        </button>
    </div>
</div>
<form action="{{ route('admin.category.update-sub', $resource->id) }}" method="post" enctype="multipart/form-data" id="store-or-update-data">
    <input type="hidden" name="type" value="sub-category">
    <div class="offcanvas-filter__body px-4 pb-0 pt-4">
        <div class="mb-80">
            <div class="form-group d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <h5 class="mb-0 flex-grow-1 d-flex gap-1">{{ \App\CPU\translate('Availability') }}<i
                        class="fi fi-sr-info cursor-pointer text-body" data-toggle="tooltip"
                        data-original-title="{{ \App\CPU\translate('If the availability status turned off, this subcategory and all the products under this subcategory will not show in the POS.') }}"
                    ></i></h5>
                    <label
                        class="border rounded px-3 py-2 d-flex gap-3 justify-content-between align-items-center flex-grow-1 mb-0 user-select-none cursor-pointer">
                        <h5 class="mb-0">{{ \App\CPU\translate('Status') }}</h5>
                        <label class="toggle-switch toggle-switch-sm">
                            <input type="checkbox" name="status" class="toggle-switch-input"
                                   id="" {{ $resource->status ? 'checked' : '' }}>
                            <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                        </label>
                    </label>
            </div>
            <div class="bg-fafafa p-3 p-lg-4 rounded-10 mb-4">
                <div class="form-group">
                    <label for="" class="title d-flex g-2px">{{ \App\CPU\translate('Sub Category Name') }}<span
                            class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $resource->name }}">
                    <span class="error-text" data-error="name"></span>
                </div>
                <div class="form-group mb-0">
                    <div class="mt-4 d-flex justify-content-between gap-3 flex-column flex-md-row">
                        <div class="flex-grow-1 text-start">
                            <label for="">{{ \App\CPU\translate('Main Category') }}<span
                                    class="text-danger">*</span><i
                                    class="fi fi-sr-info cursor-pointer ml-1 text-body" data-toggle="tooltip"
                                    title="{{ \App\CPU\translate('Main Category') }}"></i></label>
                            <select name="parent_id"
                                    class="form-control js-select2-custom">
                                @foreach($resources as $parentResource)
                                    <option value="{{ $parentResource['id'] }}" {{ $parentResource->id == $resource?->parent?->id ? 'selected' : '' }}>{{ $parentResource['name'] }}</option>
                                @endforeach
                            </select>
                            <span class="error-text" data-error="parent_id"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
            <button type="reset"
                    class="btn btn-light px-4 flex-grow-1 fw-semibold">{{ \App\CPU\translate('Reset') }}</button>
            <button type="submit"
                    class="btn btn-primary px-4 flex-grow-1 fw-semibold">{{ \App\CPU\translate('Update') }}</button>
        </div>
    </div>
</form>
