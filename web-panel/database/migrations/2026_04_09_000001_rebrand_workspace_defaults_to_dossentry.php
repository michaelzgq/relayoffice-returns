<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->updateSetting('shop_name', [
            '6POS',
            '6POS Returns Demo',
            'AM tech Shop',
            'RelayOffice Returns',
            '',
            null,
        ], 'Dossentry');

        $this->updateSetting('shop_email', [
            'ops@6pos.local',
            'ops@relayoffice.ai',
            '',
            null,
        ], 'ops@dossentry.local');

        $this->updateSetting('footer_text', [
            'Returns demo workspace',
            'Returns workspace',
            'RelayOffice Returns workspace',
            '',
            null,
        ], 'Dossentry workspace');
    }

    public function down(): void
    {
        // Intentionally left blank to avoid overwriting user-customized branding.
    }

    private function updateSetting(string $key, array $legacyValues, string $newValue): void
    {
        $existing = DB::table('business_settings')->where('key', $key)->first();

        if (! $existing) {
            DB::table('business_settings')->insert([
                'key' => $key,
                'value' => $newValue,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        if (in_array($existing->value, $legacyValues, true)) {
            DB::table('business_settings')
                ->where('key', $key)
                ->update([
                    'value' => $newValue,
                    'updated_at' => now(),
                ]);
        }
    }
};
