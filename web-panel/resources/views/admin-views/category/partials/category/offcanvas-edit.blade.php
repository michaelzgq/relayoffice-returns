<div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
    <div class="pl-3 py-2">
        <h4 class="title mb-0">{{ \App\CPU\translate('Edit Category') }}</h4>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <button class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
            <i class="tio-clear"></i>
        </button>
    </div>
</div>
<form action="{{ route('admin.category.update', $resource->id) }}" method="post" enctype="multipart/form-data" id="store-or-update-data">
    <input type="hidden" name="type" value="category">
    <div class="offcanvas-filter__body px-4 pb-0 pt-4">
        <div class="mb-80">
            <div class="form-group d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <h5 class="mb-0 flex-grow-1 d-flex gap-1">{{ \App\CPU\translate('Availability') }}<i
                        class="fi fi-sr-info cursor-pointer text-body" data-toggle="tooltip"
                        data-original-title="{{ \App\CPU\translate('If the availability status turned off, this category and all the products under this category will not show in the POS') }}"
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
                    <label for="" class="title d-flex g-2px">{{ \App\CPU\translate('Category Name') }}<span
                            class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $resource->name }}">
                    <span class="error-text" data-error="name"></span>
                </div>
                <div class="form-group mb-0">
                    <label for="" class="title">{{ \App\CPU\translate('Description') }}</label>
                    <textarea name="description" id="" class="form-control"
                              placeholder="{{ \App\CPU\translate('Type_description') }}">{{ $resource->description ?? '' }}</textarea>
                    <span class="error-text" data-error="description"></span>
                </div>
            </div>
            <div class="bg-fafafa p-3 p-lg-4 rounded-10 d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <h4 class="mb-3">{{ \App\CPU\translate('Upload_Image') }}</h4>
                    <label class="upload-file" data-image-id="">
                        <input type="file" name="image" id="customFileEg1" class="upload-file-input"
                               accept="{{IMAGE_ACCEPTED_EXTENSIONS}}"
                               data-max-upload-size="{{ readableUploadMaxFileSize('image') }}">
                        <button type="button" class="remove_btn btn btn-danger">
                            <i class="fi fi-sr-cross"></i>
                        </button>
                        <div class="upload-file-wrapper w-100px">
                            <div class="upload-file-textbox p-3 rounded bg-white border-dashed w-100 h-100">
                                <div
                                    class="d-flex flex-column justify-content-center align-items-center gap-1 h-100">
                                    <i class="fi fi-sr-camera lh-1 fs-16 text-primary"></i>
                                    <p class="fs-10 mb-0">{{ \App\CPU\translate('Add_image') }}</p>
                                </div>
                            </div>
                            <img class="upload-file-img" loading="lazy"
                                 src="{{ onErrorImage($resource->image,asset('storage/category/' . $resource->image),'', 'category') }}"
                                 data-default-src="{{ onErrorImage($resource->image,asset('storage/category/' . $resource->image),'', 'category') }}"
                                 alt="{{\App\CPU\translate('image')}}">
                        </div>
                    </label>
                    <p class="mb-0 title fs-12 mt-4"> {{ getFileFormatSizeTranslatedText(IMAGE_ACCEPTED_EXTENSIONS) }}<span
                            class="fw-bold">(1:1)</span></p>
                    <span class="error-text justify-content-center" data-error="image"></span>
                </div>
            </div>
            <input type="hidden" name="old_image" id="oldImage" value="{{ $resource->image }}">
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
