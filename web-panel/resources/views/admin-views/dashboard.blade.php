@extends('layouts.admin.app')

@section('title',\App\CPU\translate('dashboard'))

@section('content')
<div class="content container-fluid">
    @if (\App\CPU\Helpers::module_permission_check('dashboard_section'))

    <div class="card mb-3 bg-white">
        <div class="card-body">
            <div class="row gx-2 gx-lg-3 align-items-center mb-3">
                <div class="col-12">

                    <label class="badge badge-soft-danger __login-badge color-EC255A float-right mb-2 dashboard-software_version">
                        {{\App\CPU\translate('Software version')}}: {{ env('SOFTWARE_VERSION') }}
                    </label>

                </div>
                <div class="col-md-9">
                    <h2 class="card-header-title mb-0">
                        {{-- <i class="font-one-dash tio-chart-bar-4"></i> --}}
                        <span>{{\App\CPU\translate('business_statistics')}}</span>
                    </h2>
                </div>
                <div class="col-md-3 float-right">
                    <select class="custom-select" name="statistics_type" id="statisticsTypeSelect">
                        <option
                            value="overall" >
                            {{\App\CPU\translate('overall_statistics')}}
                        </option>
                        <option
                            value="today" >
                            {{\App\CPU\translate("today's_statistics")}}
                        </option>
                        <option
                            value="month" >
                            {{\App\CPU\translate("this_month's_statistics")}}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row g-3" id="account_stats">
                @include('admin-views.partials._dashboard-balance-stats',['account'=>$account])
            </div>
        </div>
    </div>
    <div class="row gx-2 gx-lg-3 mb-3 mb-lg-4">
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3 flex-wrap flex-lg-nowrap align-items-center mb-4">
                            <div class="flex-grow-1">
                                <h2 class="card-header-title mb-0">
                                    <span>{{\App\CPU\translate('earning_statistics_for_business_analytics')}}</span>
                                </h2>
                            </div>

                            <div class="mr-lg-5">
                                <div class="center-div d-flex gap-4 flex-wrap align-items-center">
                                        <span class="h5 text-body mb-0">
                                            <i class="legend-indicator bg--success"></i>
                                            {{ \App\CPU\translate('income') }}
                                        </span>
                                        <span class="h5 text-body mb-0">
                                            <i class="legend-indicator bg--warning"></i>
                                            {{ \App\CPU\translate('expense') }}
                                        </span>
                                </div>
                            </div>
                            <div class="">
                                <select class="custom-select" name="statistics_type" id="chart_statistic">
                                    <option value="yearly" >{{\App\CPU\translate('yearly_statistics')}}</option>
                                    <option value="monthly" >{{\App\CPU\translate('monthly_statistics')}}</option>
                                </select>
                            </div>

                    </div>
                    <div class="chartjs-custom" id="lastMonthStatistic">
                        <canvas id="updatingData_monthly"
                                class="h-one-dash"
                                data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                            "labels": [<?php for ($i=1;$i<=$month;$i++) {
                                if($month ==$i )
                                {
                                    echo $i;
                                }else{
                                    echo $i.',';
                                }

                            } ?>],
                            "datasets": [
                            {
                            "data": [<?php foreach ($lastMonthIncome as $key => $value) {
                                if($totalDay ==$key )
                                {
                                    echo $value;
                                }else{
                                    echo $value.',';
                                }

                            } ?>],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "green",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "green",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#377dff"
                            },
                            {
                            "data": [<?php foreach ($lastMonthExpense as $key => $value) {
                                if($totalDay ==$key )
                                {
                                    echo $value;
                                }else{
                                    echo $value.',';
                                }


                            } ?>],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#ec9a3c",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#ec9a3c",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#00c9db"
                            }
                            ]
                        },
                        "options": {
                            "scales": {
                                 "yAxes": [{
                                    "gridLines": {
                                    "color": "#e7eaf3",
                                    "drawBorder": false,
                                    "zeroLineColor": "#e7eaf3",
                                    "borderDash": [4, 4]
                                    },
                                    "ticks": {
                                    "beginAtZero": true,
{{--                                    "stepSize": {{ ($account['totalIncome']/10)+1000 }},--}}
                                    "fontSize": 12,
                                    "fontColor": "#97a4af",
                                    "fontFamily": "Open Sans, sans-serif",
                                    "padding": 20,
                                    "postfix": " "
                                    }
                                }],
                                "xAxes": [{
                                    "gridLines": {
                                    "display": true,
                                    "drawBorder": false,
                                    "color": "#e7eaf3",
                                    "borderDash": [4, 4]
                                    },
                                    "ticks": {
                                    "fontSize": 12,
                                    "fontColor": "#97a4af",
                                    "fontFamily": "Open Sans, sans-serif",
                                    "padding": 5
                                    },
                                    "categoryPercentage": 0.5,
                                    "maxBarThickness": "10"
                                }]
                            },
                            "cornerRadius": 2,
                            "tooltips": {
                            "prefix": " ",
                            "hasIndicator": true,
                            "mode": "index",
                            "intersect": false
                            },
                            "hover": {
                            "mode": "nearest",
                            "intersect": true
                            }
                        }
                        }'></canvas>
                    </div>
                    <!-- Year Chart -->
                    <div class="chartjs-custom" id="lastYearStatistic">
                        <canvas id="updatingData_yearly"
                                class="h-one-dash"
                                data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                            "labels":["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                            "datasets": [
                            {
                             "data": [{{$monthlyIncome[1]}},{{$monthlyIncome[2]}},{{$monthlyIncome[3]}},{{$monthlyIncome[4]}},{{$monthlyIncome[5]}},{{$monthlyIncome[6]}},{{$monthlyIncome[7]}},{{$monthlyIncome[8]}},{{$monthlyIncome[9]}},{{$monthlyIncome[10]}},{{$monthlyIncome[11]}},{{$monthlyIncome[12]}}],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "green",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "green",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#377dff"
                            },
                            {
                            "data": [{{$monthlyExpense[1]}},{{$monthlyExpense[2]}},{{$monthlyExpense[3]}},{{$monthlyExpense[4]}},{{$monthlyExpense[5]}},{{$monthlyExpense[6]}},{{$monthlyExpense[7]}},{{$monthlyExpense[8]}},{{$monthlyExpense[9]}},{{$monthlyExpense[10]}},{{$monthlyExpense[11]}},{{$monthlyExpense[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#ec9a3c",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#ec9a3c",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#00c9db"
                            }]
                        },
                        "options": {
                            "scales": {
                             "yAxes": [{
                                "gridLines": {
                                "color": "#e7eaf3",
                                "drawBorder": false,
                                "zeroLineColor": "#e7eaf3",
                                "borderDash": [4, 4]
                                },
                                "ticks": {
                                "beginAtZero": true,
{{--                                "stepSize": {{ ($account['totalIncome']/100)+1000 }},--}}
                                "fontSize": 12,
                                "fontColor": "#97a4af",
                                "fontFamily": "Open Sans, sans-serif",
                                "padding": 20,
                                "postfix": " "
                                }
                            }],
                            "xAxes": [{
                                "gridLines": {
                                "display": true,
                                "drawBorder": false,
                                "color": "#e7eaf3",
                                "borderDash": [4, 4]
                                },
                                "ticks": {
                                "fontSize": 12,
                                "fontColor": "#97a4af",
                                "fontFamily": "Open Sans, sans-serif",
                                "padding": 5
                                },
                                "categoryPercentage": 0.5,
                                "maxBarThickness": "10"
                            }]
                            },
                            "cornerRadius": 2,
                            "tooltips": {
                            "prefix": " ",
                            "hasIndicator": true,
                            "mode": "index",
                            "intersect": false
                            },
                            "hover": {
                            "mode": "nearest",
                            "intersect": true
                            }
                        }
                        }'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row gx-2 gx-lg-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="title">
                        {{\App\CPU\translate('accounts')}}
                    </h4>
                    <a class="text--info text-underline text-capitalize" href="{{route('admin.account.list')}}">{{ \App\CPU\translate('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive datatable-custom">
                        <table class="table table-thead-bordered table-nowrap table-align-middle card-table title fs-12">
                            <thead class="thead-light">
                            <tr>
                                <th class="title text-capitalize fs-14">{{ \App\CPU\translate('#') }}</th>
                                <th class="title text-capitalize fs-14">{{ \App\CPU\translate('account') }}</th>
                                <th class="title text-capitalize fs-14">{{\App\CPU\translate('balance')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                                @foreach ($accounts as $key=>$account)
                                    <tr>
                                        <td class="py-4">{{ $loop->iteration }}</td>
                                        <td class="py-4">
                                            <a class="title text-primary-hover" href="{{ route('admin.account.list') }}">
                                                {{ $account->account }}
                                            </a>
                                        </td>
                                        <td class="py-4"><span class="fw-semibold">{{ $account->balance ." ".\App\CPU\Helpers::currency_symbol()}}</span></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($accounts)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 img-one-dash" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">
                                <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="title">
                        {{\App\CPU\translate('stock_limit_products')}}
                    </h4>
                    <a class="text--info text-underline text-capitalize" href="{{route('admin.stock.stock-limit')}}">{{ \App\CPU\translate('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive datatable-custom">
                        <table class="table table-thead-bordered table-nowrap table-align-middle card-table title fs-12">
                            <thead class="thead-light">
                            <tr>
                                <th class="title text-capitalize fs-14">{{ \App\CPU\translate('#') }}</th>
                                <th class="title text-capitalize fs-14">{{ \App\CPU\translate('Product_info') }}</th>
                                <th class="title text-capitalize fs-14">{{\App\CPU\translate('QTY')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key=>$product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a class="title text-primary-hover d-flex gap-4 align-items-center" href="{{ route('admin.stock.stock-limit') }}">
                                                <img class="img-two-cati object-cover rounded-circle" src="{{ $product['image_fullpath'] }}" alt="Image Description">
                                                <span>{{ Str::limit($product->name,40) }}</span>
                                            </a>
                                        </td>
                                        <td><span class="fw-semibold">{{ $product->quantity }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($products)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 img-one-dash" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="{{\App\CPU\translate('image_description')}}">
                                <p class="mb-0">{{ \App\CPU\translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="text-center centered--messages">
           <div>
               <img class="mb-3 img-one-dash" src="{{asset('assets/admin/img/access-denied.svg')}}" alt="{{\App\CPU\translate('image_description')}}">
               <p class="mb-0 text-center">{{ \App\CPU\translate('You do not have access to this content')}}</p>
           </div>
        </div>
    @endif
</div>

@endsection

@push('script')
    <script src="{{asset('assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{asset('assets/admin')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush

@push('script_2')
<script src={{asset("assets/admin/js/global.js")}}></script>

    <script>
        "use strict";

        $('#statisticsTypeSelect').on('change', function() {
            account_stats_update($(this).val());
        });

        function account_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.account-status')}}',
                data: {
                    statistics_type: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    $('#account_stats').html(data.view);
                    replaceSvgImages();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }

        $('#chart_statistic').on('change', function() {
            chart_statistic($(this).val());
        });

        // Chart initialization
        document.addEventListener('DOMContentLoaded', function () {
            // Yearly chart
            if ($('#updatingData_yearly').length) {
                $.HSCore.components.HSChartJS.init($('#updatingData_yearly'));

                setTimeout(function () {
                    const canvas = document.getElementById('updatingData_yearly');
                    let yearlyChart = null;

                    for (let key in Chart.instances) {
                        if (Chart.instances[key].chart.canvas === canvas) {
                            yearlyChart = Chart.instances[key].chart;
                            break;
                        }
                    }

                    if (!yearlyChart) return;

                    yearlyChart.options.scales.yAxes[0].ticks.callback = function (value) {
                        return formatNumber(value);

                    };

                    yearlyChart.options.tooltips.callbacks = {
                        label: function (tooltipItem) {
                            let label = tooltipItem.datasetIndex === 0 ? 'Income : ' : 'Expense : ';
                            let value = tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            return label + '$' + value;
                        }
                    };

                    yearlyChart.update();
                }, 100);
            }

            // Monthly chart
            if ($('#updatingData_monthly').length) {
                $.HSCore.components.HSChartJS.init($('#updatingData_monthly'));

                setTimeout(function () {
                    const canvas = document.getElementById('updatingData_monthly');
                    let monthlyChart = null;

                    for (let key in Chart.instances) {
                        if (Chart.instances[key].chart.canvas === canvas) {
                            monthlyChart = Chart.instances[key].chart;
                            break;
                        }
                    }

                    if (!monthlyChart) return;

                    monthlyChart.options.scales.yAxes[0].ticks.callback = function (value) {
                        return formatNumber(value);
                    };

                    monthlyChart.options.tooltips.callbacks = {
                        label: function (tooltipItem) {
                            let label = tooltipItem.datasetIndex === 0 ? 'Income : ' : 'Expense : ';
                            let value = tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                            return label + '$' + value;
                        }
                    };

                    monthlyChart.update();
                }, 100);
            }
        });
        function formatNumber(value) {
            const absValue = Math.abs(value);

            if (absValue >= 1_000_000_000) {
                return (value / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'B';
            } else if (absValue >= 1_000_000) {
                return (value / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
            } else if (absValue >= 1_000) {
                return (value / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
            }

            return value.toString();
        }

    </script>



@endpush
