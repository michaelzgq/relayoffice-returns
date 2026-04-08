<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <title>{{ \App\CPU\translate('Unit List') }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap');

        body, * {
            font-family: 'DejaVu Sans', sans-serif !important;
        }
    </style>
</head>
<body style="font-family: DejaVu Sans, sans-serif;">

<div style="max-width: 800px; margin: auto; position: relative; min-height: 11.69in;">

    <div style="margin-bottom: 30px; border-bottom: 1px dashed #E6E7EC; padding-bottom: 10px;">
        <table style="width: 100%;">
            <tr>
                @php
                    $filterKeys = ['search', 'sorting_type', 'start_date', 'end_date'];
                    $hasFilter = collect($filterKeys)->contains(fn($key) => request()->filled($key));
                @endphp
                <td style="vertical-align: {{ $hasFilter ? 'top' : 'middle' }};">
                    <h4 style="font-size: 21px; font-weight: bold; margin:0;">{{ \App\CPU\translate('Unit List') }}</h4>
                    @if(request()->filled('sorting_type'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Sorting Type') }}:</strong> {{ ucwords(request()->get('sorting_type')) }}
                        </p>
                    @endif
                    @if(request()->filled('search'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Search') }}:</strong> {{ ucwords(request()->get('search')) }}
                        </p>
                    @endif
                    @if(request()->filled('start_date') && request()->filled('end_date'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Start Date') }}:</strong>
                            {{ request()->get('start_date') }}
                        </p>
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('End Date') }}:</strong>
                            {{ request()->get('end_date') }}
                        </p>
                    @endif
                </td>
                <td style="text-align: right;">
                    @php
                        $shopLogo = \App\Models\BusinessSetting::where('key', 'shop_logo')->value('value');
                        $logoPath = storage_path('app/public/shop/' . $shopLogo);
                        $logoFallback = public_path('assets/admin/img/160x160/img2.jpg');
                    @endphp
                    <img src="{{(isset($shopLogo) && $shopLogo != 'def.png') ?  $logoPath : $logoFallback }}" style="height: 45px;" alt="Shop Logo"/>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; font-size: 10px; border-collapse: collapse; color: #303030;">
        <thead>
        <tr style="background: #FAFAFA; border-top: 1px solid #EDF1F5; border-bottom: 1px solid #EDF1F5;">
            <th style="padding: 6px; text-align: left; vertical-align: top; white-space: nowrap;">{{ \App\CPU\translate('SL') }}</th>
            <th style="padding: 6px; text-align: left; vertical-align: top;">{{ \App\CPU\translate('Unit Name') }}</th>
            <th style="padding: 6px; text-align: left; vertical-align: top; white-space: nowrap;">{{ \App\CPU\translate('Total Products') }}</th>
            <th style="padding: 6px; text-align: left; vertical-align: top; white-space: nowrap;">{{ \App\CPU\translate('Status') }}</th>
        </tr>
        </thead>

        <tbody>
        @foreach($resources as $index => $resource)
            <tr>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; white-space: nowrap; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $index + 1 }}
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; white-space: nowrap; border-bottom: 0.5pt solid #EDF1F5;">
                    <strong>{{ $resource->unit_type }}</strong><br><br>
                    ID: <strong>{{ $resource->id }}</strong>
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; white-space: nowrap; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $resource->product_count ?? 0 }}
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; white-space: nowrap; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $resource->status ? 'Active' : 'Inactive' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<htmlpagefooter name="last-page-footer">
    <div style="border-top: 1px dashed #E6E7EC; padding-top: 6px; font-size: 9px; text-align: center;">
        <p style="margin: 6px 0;">{{ \App\CPU\translate('Thanks for using our service.') }}</p>
        <div style="background: #FAFAFA; padding: 10px 30px;">
            <table style="width: 100%; font-size: 10px;">
                <tr>
                    <td style="text-align: left;">{{ url('/') }}</td>
                    <td style="text-align: center;">{{ \App\Models\BusinessSetting::where('key', 'shop_phone')->value('value') }}</td>
                    <td style="text-align: right;">{{ \App\Models\BusinessSetting::where('key', 'shop_email')->value('value') }}</td>
                </tr>
            </table>
        </div>
    </div>
</htmlpagefooter>

<sethtmlpagefooter name="last-page-footer" value="on" show-this-page="1" />
</body>
</html>
