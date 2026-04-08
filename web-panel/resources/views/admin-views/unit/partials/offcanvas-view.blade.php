@php
    /** @var Unit $resource */
    /** @var Collection|Unit[] $resources */
    use App\Models\Unit;
    use Illuminate\Support\Collection;
    use function App\CPU\translate;
    $isLastWithProducts = $resource['product_count'] > 0 && $resources->total() == 1;
@endphp
<div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
    <div class="pl-3 py-2">
        <h4 class="title mb-0">{{ translate('Unit') . ' #'. $resource['id'] }}</h4>
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
            <button type="button" id="{{ !$isLastWithProducts ? 'view-delete-resource' : '' }}"
                    class="btn btn-outline-danger border fs-16 {{ $isLastWithProducts ? 'disabled' : ($resource['product_count'] > 0 ? 'delete-resource-after-shifting' : 'delete-resource') }}"
                    data-id=" {{ $resource['id'] }}"
                    @if ($isLastWithProducts)
                        data-toggle="tooltip"
                    data-original-title="{{ \App\CPU\translate('This unit contains products, so you cannot delete the last unit') }}"
                    @else
                        data-target="{{ $resource['product_count'] > 0 ? '#deleteModalWithShift' : '#deleteModal' }}"
                    data-toggle="modal"
                @endif
            ><i
                    class="fi fi-sr-trash"></i></button>
            <label
                class="btn btn-white border-1 bg-white title px-3 fw-semibold lh-1 d-flex align-items-center gap-3 mb-0">
                {{ translate('Status') }}
                <label class="toggle-switch toggle-switch-sm">
                    <input id="view-update-status" type="checkbox" class="toggle-switch-input global-change-status"
                           data-route="{{ route('admin.unit.status', [$resource['id'], $resource->status ? 0 : 1]) }}"
                           data-target="#globalChangeStatusModal"
                           data-id="{{ $resource['id'] }}"
                           data-title="{{ translate('Are you sure') }}?"
                           data-description="{{ $resource->status == 1 ? translate('Want to turn off the status') : translate('Want to turn on the status') }}"
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
                <h4 class="text-truncate font-weight-medium mb-0 mt-1">
                    {{ $resource->unit_type }}
                </h4>
            </div>
            <div class="table-responsive mb-4">
                <table class="table table-borderless text-nowrap mb-0">
                    <tbody>
                    <tr>
                        <td>{{translate("Total Product")}}</td>
                        <td><span class="mr-2">:</span> {{ $resource->product_count }}</td>
                    </tr>
                    <tr>
                        <td>{{translate("Created Date")}}</td>
                        <td><span
                                class="mr-2">:</span>{{ (\Carbon\Carbon::parse($resource->created_at))->format('d M, Y | g:i A') }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{translate("Last Modified Date")}}</td>
                        <td><span
                                class="mr-2">:</span> {{ (\Carbon\Carbon::parse($resource->updated_at))->format('d M, Y | g:i A') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="offcanvas-filter__footer bg-white py-2 d-flex align-items-center">
    <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
        <button type="button"
                class="btn btn-light px-4 flex-grow-1 fw-semibold closeOfcanvus">{{ translate('Cancel') }}</button>
        <a href="#"
           class="btn btn-primary px-4 flex-grow-1 fw-semibold offcanvas-toggle edit-resource"
           data-id="{{ $resource['id'] }}"
           data-target="#offcanvasEdit"
           aria-label="Edit brand">{{ translate('Edit_Details') }}</a>
    </div>
</div>
