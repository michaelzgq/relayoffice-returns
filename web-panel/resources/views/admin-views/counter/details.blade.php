@extends('layouts.admin.app')

@section('title',\App\CPU\translate('counter details'))

@push('css_or_js')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom.css"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="">
            <div class="row align-items-center mb-3">
                <div class="col-sm-12 mb-2 mb-sm-0">
                    <h1 class="fs-16">
                        <span>{{\App\CPU\translate('counter details')}}</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="card counter-details mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 mt-3">
                        <div class="d-flex align-items-center">
                            <img src="{{asset('assets/admin/img/counter-user.png')}}" alt="Counter Image">
                            <div class="w-0 flex-grow-1 pl-3">
                                <h4 class="counter-details-title">{{$counter['name']}} - {{ $counter['number'] }}</h4>
                                <p class="counter-details-text">{{$counter['description']}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-6 mt-3">
                                <a class="card card-hover-shadow h-100 bg-blue" href="#">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center">
                                            <h6 class="card-subtitle text-center">{{\App\CPU\translate('Total_order')}}</h6>
                                            <span class="card-title text-center">
                                                {{$counter->orders_count}}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 mt-3">
                                <a class="card card-hover-shadow h-100 bg-green" href="#">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center">
                                            <h6 class="card-subtitle text-center">{{\App\CPU\translate('Total_Earning')}}</h6>
                                            <span class="card-title text-center">
                                                {{ ($counter->orders_sum_order_amount ?? 0) + ($counter->orders_sum_total_tax ?? 0) }} {{ \App\CPU\Helpers::currency_symbol() }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-2 mb-4">
            <h2 class="page-header-title fs-16 text-capitalize">{{\App\CPU\translate('order_list')}}
                <span class="badge bg-primary ml-2 rounded-full text-white">{{ $orders->total() }}</span></h2>
        </div>

        @include('layouts.admin.table._card', [
                                                'resources' => $orders,
                                                'tableFor' => 'counter-orders',
                                                'searchBoxPlaceholder' => \App\CPU\translate('search_by_order_id'),
                                                'columns' => ['sl', 'order_id', 'order_date', 'customer_info', 'total_amount', 'paid_by', 'action'],
                                                'tableRows' => view('admin-views.counter.partials._details-table-rows',['orders' => $orders])->render(),
                                                'exportRoute' => route('admin.counter.export-counter-details', $counter->id)])

    </div>

    <div class="modal fade" id="print-invoice" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-content1">
                <div class="modal-header">
                    <h5 class="modal-title">{{\App\CPU\translate('print')}} {{\App\CPU\translate('invoice')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="text-dark" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <input type="button" class="mt-2 btn btn-primary non-printable print-div"
                                   data-name="printableArea"
                                   value="{{\App\CPU\translate('Proceed, If thermal printer is ready')}}."/>
                            <a href="{{url()->previous()}}"
                               class="mt-2 btn btn-danger non-printable">{{\App\CPU\translate('Back')}}</a>
                        </div>
                        <hr class="non-printable">
                    </div>
                    <div class="row m-auto" id="printableArea">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script>
        "use strict";
        $(".print-invoice").on('click', function () {
            let order_id = $(this).data('id');
            print_invoice(order_id);
        });

        function print_invoice(order_id) {
            $.get({
                url: '{{url('/')}}/admin/pos/invoice/' + order_id,
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#print-invoice').modal('show');
                    $('#printableArea').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function replaceSvgImages() {
            $('img.svg').each(function () {
                let $img = $(this);
                let imgURL = $img.attr('src');

                $.get(imgURL, function (data) {
                    let $svg = $(data).find('svg');

                    if ($img.attr('class')) {
                        $svg.attr('class', $img.attr('class'));
                    }
                    $svg.removeAttr('xmlns:a');

                    $img.replaceWith($svg);
                }, 'xml');
            });
        }

        replaceSvgImages();
    </script>
@endpush
