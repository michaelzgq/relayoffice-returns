{{-- Order Items Offcancvas --}}
<div class="offcanvas-filter" id="offcanvasOrderItemsMenu">
    <div class="offcanvas-filter__header d-flex justify-content-between align-items-start border-bottom px-2 py-2">
        <div class="pl-3 py-2">
            <h4 class="title">{{ ($countItems ?? 0) . ' ' . Str::plural('Item', $countItems ?? 0) }}</h4>
            <p class="mb-0">Order ID #{{ $orderId ?? 0 }}</p>
        </div>
        <div>
            <button class="btn btn-soft-secondary px-1 py-0 rounded-circle closeOfcanvus">
                <i class="tio-clear"></i>
            </button>
        </div>
    </div>
    <div class="offcanvas-filter__body p-0 pt-3">
        <div class="table-responsive ">
            <table class="table table-hover table-borderless table-nowrap table-align-middle card-table">
                <tbody id="set-rows">
                @if(isset($orderedItems) && count($orderedItems) > 0)
                    @foreach($orderedItems as $orderedItem)
                        <tr>
                            <td class="title">
                                <div class="d-flex gap-2 align-items-center">
                                    <img width="40" height="40" class="aspect-1"
                                         src="{{onErrorImage($orderedItem['image'],asset('storage/product/' . $orderedItem['image']) ,asset('assets/admin/img/400x400/img2.jpg') ,'product/')}}"
                                         alt=" {{ $orderedItem['name'] }}">
                                    <div>
                                        <div class="font-weight-semobild">{{ $orderedItem['name'] }}</div>
                                        <div>{{ $orderedItem['unit_value'] }} {{ $orderedItem['unit_type'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="title table-column-pl-0">
                                <div class="font-weight-semobild">x {{ $orderedItem['quantity'] }}</div>
                            </td>
                            <td class="title">
                                <div class="font-weight-semobild">{{ \App\CPU\Helpers::currency_symbol() . ' '. number_format($orderedItem['total_price'], 2) }}</div>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
