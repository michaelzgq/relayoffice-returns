<!doctype html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;"/>
    <meta charset="UTF-8">
    <title>{{ \App\CPU\translate('Stock Limit Product List') }}</title>

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
                    $filterKeys = ['search', 'availability', 'min_price', 'max_price', 'stocks', 'category_ids', 'subcategory_ids', 'brand_ids', 'supplier_id'];
                    $hasFilter = collect($filterKeys)->contains(fn($key) => request()->filled($key));
                @endphp
                <td style="vertical-align: {{ $hasFilter ? 'top' : 'middle' }};">
                    <h4 style="font-size: 21px; font-weight: bold; margin:0;">{{ \App\CPU\translate('Stock Limit Product List') }}</h4>
                    @if(request()->filled('search'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Search') }}:</strong> {{ ucwords(request()->get('search')) }}
                        </p>
                    @endif
                    @if(request()->filled('availability'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Availability') }}:</strong> {{ ucwords(request()->get('availability')) }}
                        </p>
                    @endif
                    @if(request()->filled('min_price') && request()->filled('max_price'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Min Price') }}:</strong>
                            {{ request()->get('min_price') }}
                        </p>
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Max Price') }}:</strong>
                            {{ request()->get('max_price') }}
                        </p>
                    @endif
                    @if(request()->filled('stocks'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Stocks') }}:</strong> {{ ucwords(implode(', ', str_replace('_', ' ', json_decode(request()->get('stocks'), true)))) }}
                        </p>
                    @endif
                    @if(request()->filled('category_ids'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Categories') }}:</strong> {{ ucwords(implode(', ', $categoryNames)) }}
                        </p>
                    @endif
                    @if(request()->filled('subcategory_ids'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Sub Categories') }}:</strong> {{ ucwords(implode(', ', $subcategoryNames)) }}
                        </p>
                    @endif
                    @if(request()->filled('brand_ids'))
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Brands') }}:</strong> {{ ucwords(implode(', ', $brandNames)) }}
                        </p>
                    @endif
                    @if(request()->filled('supplier_id') && request()->get('supplier_id') != 'all')
                        <p style="color: #646B73; margin:0;">
                            <strong>{{ \App\CPU\translate('Supplier') }}:</strong> {{ ucwords($supplierName) }}
                        </p>
                    @endif
                </td>
                <td style="text-align: right;">
                    @php
                        $shopLogo = \App\Models\BusinessSetting::where('key', 'shop_logo')->value('value');
                        $logoPath = storage_path('app/public/shop/' . $shopLogo);
                        $logoFallback = public_path('assets/admin/img/160x160/img2.jpg');
                    @endphp
                    <img src="{{ (isset($shopLogo) && $shopLogo != 'def.png') ? $logoPath : $logoFallback  }}" style="height: 45px;" alt="Shop Logo"/>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; font-size: 10px; border-collapse: collapse; color: #303030;">
        <thead>
        <tr style="background: #FAFAFA; border-top: 1px solid #EDF1F5; border-bottom: 1px solid #EDF1F5;">
            <th style="padding: 6px; text-align: left; vertical-align: top;">{{ \App\CPU\translate('SL') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Product Name') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Supplier Info') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Category') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Quantity') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Orders') }}</th>
            <th style="padding: 6px;text-align: left; vertical-align: top;">{{ \App\CPU\translate('Status') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $index => $product)
            <tr>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $index + 1 }}
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">
                    <table>
                        <tr>
                            <td style="padding-right: 10px; vertical-align: top;">
                                <img
                                    src="{{ ( !empty($product->image) && $product->image != 'def.png') ?  storage_path('app/public/product/' . $product->image) : public_path('assets/admin/img/160x160/img2.jpg') }}"
                                    style="width: 30px; height: 30px; object-fit: cover; border-radius: 5px;"
                                    alt="Product Image"/>
                            </td>
                            <td style="padding-right: 10px; vertical-align: top;">
                                <strong>{{ $product->name }}</strong><br><br>
                                ID: <strong>{{ $product->product_code }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">
                    @if($product?->supplier)
                        <table>
                            <tr>
                                <td style="padding-right: 10px; vertical-align: top;">
                                    {{ $product?->supplier?->name }}
                                    <br>
                                    {{ $product?->supplier?->mobile }}
                                </td>
                            </tr>
                        </table>
                    @else
                        <span>{{ \App\CPU\translate('N/A') }}</span>
                    @endif
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $product?->category?->name }}
                </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">  {{ $product['quantity'] }} </td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;"> {{ $product->order_count ?? 0 }}</td>
                <td style="padding: 10px 6px; text-align: left; vertical-align: top; border-bottom: 0.5pt solid #EDF1F5;">
                    {{ $product->quantity < 1 ? 'Out of Stock' : 'Low Stock' }}
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

<sethtmlpagefooter name="last-page-footer" value="on" show-this-page="1"/>
</body>
</html>
