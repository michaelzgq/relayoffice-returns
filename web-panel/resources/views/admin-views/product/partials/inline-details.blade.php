<div class="mb-2">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="">
            <h1 class="h2 mb-2">{{ \App\CPU\translate('Product_Details') }}</h1>
            <p class="mb-0">{{ \App\CPU\translate('Created at ') . $product->created_at->format('d M, Y') }}</p>
        </div>
        <div class="d-flex align-items-stretch flex-wrap gap-2">
            <button type="button"
                    class="btn btn-white border-0 bg-f2f2f2 title px-3 fw-semibold min-w-120px lh-1 d-flex align-items-center gap-3 delete-resource"
                    data-id="{{ $product['id'] }}"
                    data-target="#deleteModal"
                    data-toggle="modal">
                <i class="fi fi-rr-trash"></i>
                {{ \App\CPU\translate('Delete') }}
            </button>
            <label class="btn btn-white border-0 bg-f2f2f2 title px-3 fw-semibold lh-1 d-flex align-items-center gap-3 mb-0">
                {{ \App\CPU\translate('Status') }}
                <label class="toggle-switch toggle-switch-sm">
                    <input type="checkbox" class="toggle-switch-input global-change-status"
                           data-route="{{ route('admin.product.status', [$product['id'], $product->status ? 0 : 1]) }}"
                           data-target="#globalChangeStatusModal"
                           data-id="{{ $product['id'] }}"
                           data-title="{{ \App\CPU\translate('Are you sure') }}?"
                           data-description="{{ $product['status'] == 1 ? \App\CPU\translate('Want to turn off the status') : \App\CPU\translate('Want to turn on the status') }}"
                           data-image="{{ asset('assets/admin/img/info.svg') }}"
                        {{ $product->status ? 'checked' : '' }}>
                    <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                </label>
            </label>

            <a href="{{ route('admin.product.edit', $product->id) }}"
               class="btn btn-primary fw-semibold min-w-120px lh-1 d-flex align-items-center justify-content-center gap-2">
                <i class="fi fi-sr-pencil"></i>
                {{ \App\CPU\translate('Edit') }}
            </a>
        </div>
    </div>
</div>
<div class="mb-3">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link py-2 {{ Route::currentRouteName() === 'admin.product.show' ? 'active' : '' }}" href="{{ route('admin.product.show', $product->id) }}">{{ \App\CPU\translate('Product_info') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-2 {{ Route::currentRouteName() === 'admin.product.barcode-generate' ? 'active' : '' }}" href="{{ route('admin.product.barcode-generate', $product->id) }}">{{ \App\CPU\translate('Generate_Barcode') }}</a>
        </li>
    </ul>
</div>
