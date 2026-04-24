<?php

namespace Tests\Feature\Returns;

use App\Models\ReturnExpectedInbound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class ExpectedInboundImportTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_expected_inbound_csv_imports_rows_and_prefills_inspection(): void
    {
        $admin = $this->signInAdmin();
        $bundle = $this->createBrandWithProfile([
            'name' => 'Peak Audio',
        ]);

        $csv = implode("\n", [
            'return_id,brand_name,product_sku,serial_number,tracking_number,return_reason,expected_condition',
            'RMA-INBOUND-1001,Peak Audio,SKU-IN-1001,SN-IN-1001,1Z999,Defective,opened_damaged',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.inbound.store'), [
            'inbound_csv' => UploadedFile::fake()->createWithContent('expected-inbound.csv', $csv),
        ]);

        $response->assertRedirect(route('admin.returns.inbound.index'));

        $expectedInbound = ReturnExpectedInbound::query()->where('return_id', 'RMA-INBOUND-1001')->firstOrFail();

        $this->assertSame($bundle['brand']->id, $expectedInbound->brand_id);
        $this->assertSame('SKU-IN-1001', $expectedInbound->product_sku);
        $this->assertSame('opened_damaged', $expectedInbound->expected_condition);

        $inspectResponse = $this->actingAs($admin, 'admin')->get(route('admin.returns.inspect', [
            'expected_id' => $expectedInbound->id,
        ]));

        $inspectResponse->assertOk();
        $inspectResponse->assertSee('Expected inbound matched');
        $inspectResponse->assertSee('RMA-INBOUND-1001');
        $inspectResponse->assertSee('SKU-IN-1001');
        $inspectResponse->assertSee('SN-IN-1001');
    }
}
