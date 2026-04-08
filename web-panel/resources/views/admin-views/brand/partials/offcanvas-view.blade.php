<div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
    <div class="pl-3 py-2">
        <h4 class="title mb-0">{{ \App\CPU\translate('Brand') . ' #'. $resource->id }}</h4>
    </div>
    <div class="d-flex gap-3 align-items-center">
        <button class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
            <i class="tio-clear"></i>
        </button>
    </div>
</div>
<div class="offcanvas-filter__body px-4 pb-0 pt-4">
    <div class="mb-80">
        <div class="form-group d-flex justify-content-end gap-2 flex-wrap">
            <button type="button" id="view-delete-resource" class="btn btn-outline-danger border fs-16 {{ $resource->product_count > 0 ? 'delete-resource-after-shifting': 'delete-resource' }} " data-id=" {{ $resource->id }}"
                    data-target="{{ $resource->product_count > 0 ? '#deleteModalWithShift' : '#deleteModal' }}"
                    data-toggle="modal"
            ><i
                    class="fi fi-sr-trash"></i></button>
            <label class="btn btn-white border-1 bg-white title px-3 fw-semibold lh-1 d-flex align-items-center gap-3 mb-0">
                {{ \App\CPU\translate('Status') }}
                <label class="toggle-switch toggle-switch-sm">
                    <input id="view-update-status" type="checkbox" class="toggle-switch-input global-change-status"
                           data-route="{{ route('admin.brand.status', [$resource->id, $resource->status ? 0 : 1]) }}"
                           data-target="#globalChangeStatusModal"
                           data-id="{{ $resource->id }}"
                           data-title="{{ \App\CPU\translate('Are you sure') }}?"
                           data-description="{{ $resource->status == 1 ? \App\CPU\translate('Want to turn off the status') : \App\CPU\translate('Want to turn on the status') }}"
                           data-image="{{ asset('assets/admin/img/info.svg') }}"
                        {{ $resource->status ? 'checked' : '' }}>
                    <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                </label>
            </label>
        </div>
        <div class="bg-fafafa p-3 p-lg-4 rounded-10">
            <div class="d-flex gap-2 align-items-start mb-4">
                    <img width="58" height="58" src="{{ onErrorImage($resource->image,asset('storage/brand/' . $resource->image),asset('assets/admin/img/160x160/img2.jpg'), 'brand') }}" class="aspect-1 object-cover rounded">
                <h4 class="text-truncate font-weight-medium mb-0 mt-1">
                    {{ $resource->name }}
                </h4>
            </div>
            <div class="table-responsive mb-4">
                <table class="table table-borderless text-nowrap mb-0">
                    <tbody>
                    <tr>
                        <td>{{\App\CPU\translate("Total Product")}}</td>
                        <td><span class="mr-2">:</span> {{ $resource->product_count }}</td>
                    </tr>
                    <tr>
                        <td>{{\App\CPU\translate("Created Date")}}</td>
                        <td><span
                                class="mr-2">:</span>{{ (\Carbon\Carbon::parse($resource->created_at))->format('d M, Y | g:i A') }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{\App\CPU\translate("Last Modified Date")}}</td>
                        <td><span
                                class="mr-2">:</span> {{ (\Carbon\Carbon::parse($resource->updated_at))->format('d M, Y | g:i A') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @if($resource->description)
                <div class="p-3 bg-white rounded">
                    <h5 class="mb-1 font-weight-medium">{{ \App\CPU\translate('Description') }}</h5>
                    <p class="mb-0 des_text">
                        {{ $resource->description }}
                        <button type="button"
                                class="btn text-primary p-0 m-0 see_more_btn">{{ \App\CPU\translate('See More') }}</button>
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
        <button type="button"
                class="btn btn-light px-4 flex-grow-1 fw-semibold closeOfcanvus">{{ \App\CPU\translate('Cancel') }}</button>
        <a href="#"
           class="btn btn-primary px-4 flex-grow-1 fw-semibold offcanvas-toggle edit-resource" data-id="{{ $resource->id }}"
           data-target="#offcanvasEdit"
           aria-label="Edit brand">{{ \App\CPU\translate('Edit_Details') }}</a>
    </div>
</div>
