<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ \App\CPU\translate('Product Barcode') }}</title>
</head>
<body style="font-family: sans-serif; font-size: 0.875rem; margin: 0; padding: 0;">

@if ($quantity)
    <table style="width: 100%; border-spacing: 10px;">
        @for ($i = 0; $i < $quantity; $i++)
            @if ($i % 3 == 0)
                <tr>
                    @endif

                    <td style="text-align: center; border: 1px dotted #ccc; padding: 12px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="font-weight: bold; text-transform: capitalize; text-align: center;">
                                    {{ optional(\App\Models\BusinessSetting::where('key', 'shop_name')->first())->value }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    {{ \Illuminate\Support\Str::limit($product->name, 30) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    {{ $product['selling_price'] . ' ' . \App\CPU\Helpers::currency_symbol() }}
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: center;">
                                    <img src="{{ $barcodeImage }}" style="display: block; margin: 0 auto; height: auto;" alt="Barcode"/>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: center;">
                                    {{ $product->product_code }}
                                </td>
                            </tr>
                        </table>
                    </td>


                @if (($i + 1) % 3 == 0 || $i + 1 == $quantity)
                </tr>
            @endif
        @endfor
    </table>
@endif

</body>
</html>
