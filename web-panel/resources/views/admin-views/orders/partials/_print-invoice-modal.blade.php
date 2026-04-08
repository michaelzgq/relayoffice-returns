<div class="overlay" id="overlay"></div>
<div class="offcanvas-filter filter-offcanvas" id="print-invoice">
    <div class="offcanvas-filter__header border-bottom px-3 d-flex justify-content-between align-items-center gap-3">
        <h4>{{\App\CPU\translate('Print_Invoice')}}</h4>
        <button type="button" class="btn btn-light icon-btn rounded-circle close-print-invoice" aria-label="Close">
            <i class="fi fi-rr-cross-small fs-16"></i>
        </button>
    </div>
    <form action="{{ url()->current() }}" method="GET">
        <div class="offcanvas-filter__body">
            <div class="row m-auto" id="printableArea">
            </div>
        </div>
        <div class="offcanvas-filter__footer bg-white py-3 d-flex align-items-center h-auto">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 w-100">
                <a id="invoice_close" data-route="{{url()->previous()}}"
                   class="btn btn-light min-w-120px fw-semibold non-printable invoice-close close-print-invoice">{{\App\CPU\translate('back')}}</a>

                <button id="print_invoice" type="button" class="btn btn-primary min-w-120px fw-semibold non-printable print-div" data-name="printableArea">
                    {{\App\CPU\translate('Proceed, If thermal printer is ready.')}}
                </button>
            </div>
        </div>
    </form>
</div>
