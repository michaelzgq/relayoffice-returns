<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SampleCaseRoutingTest extends TestCase
{
    private function samplePdfPath(): string
    {
        return base_path('public/assets/dossentry/dossentry-sample-case-serial-mismatch-2026-04.pdf');
    }

    public function test_sample_case_page_serves_public_workflow_asset(): void
    {
        $response = $this->get('http://dossentry.com/sample-cases/serial-mismatch-review');

        $response->assertOk();
        $response->assertSee('Sample Case Only');
        $response->assertSee('Serial mismatch caught before the case moved forward.');
        $response->assertSee('RMA-SAMPLE-1001');
        $response->assertSee('Download Sample PDF');
        $response->assertSee(json_encode(route('marketing.click-events.store')), false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('sample-serial-mismatch-board.png', false);
    }

    public function test_sample_case_pdf_route_serves_pdf_when_file_exists(): void
    {
        $pdfPath = $this->samplePdfPath();
        File::ensureDirectoryExists(dirname($pdfPath));
        $original = File::exists($pdfPath) ? File::get($pdfPath) : null;

        try {
            File::put($pdfPath, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF");

            $response = $this->get(route('sample-cases.serial-mismatch.pdf'));

            $response->assertOk();
            $response->assertHeader('content-type', 'application/pdf');
        } finally {
            if ($original !== null) {
                File::put($pdfPath, $original);
            } elseif (File::exists($pdfPath)) {
                File::delete($pdfPath);
            }
        }
    }
}
