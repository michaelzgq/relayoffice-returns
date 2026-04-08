<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoBootstrapSeeder extends Seeder
{
    public function run()
    {
        DB::table('currencies')->updateOrInsert(
            ['currency_code' => 'USD'],
            [
                'country' => 'United States Dollar',
                'currency_symbol' => '$',
                'exchange_rate' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $settings = [
            'shop_logo' => null,
            'pagination_limit' => '12',
            'currency' => 'USD',
            'shop_name' => '6POS Returns Demo',
            'shop_address' => 'Los Angeles, CA',
            'shop_phone' => '0000000000',
            'shop_email' => 'ops@6pos.local',
            'footer_text' => 'Returns demo workspace',
            'country' => 'US',
            'stock_limit' => '10',
            'time_zone' => 'America/Los_Angeles',
            'vat_reg_no' => 'N/A',
            'fav_icon' => null,
            'currency_symbol_position' => 'left',
            'digit_after_decimal' => '2',
        ];

        foreach ($settings as $key => $value) {
            DB::table('business_settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
