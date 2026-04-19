<?php

namespace Tests\Feature;

use Tests\TestCase;

class SolutionPageRoutingTest extends TestCase
{
    public function test_3pl_exception_workflow_page_serves_public_solution_content(): void
    {
        $response = $this->get('http://dossentry.com/3pl-return-exception-workflow');

        $response->assertOk();
        $response->assertSee('Handle High-Risk Return Exceptions Without Rebuilding the Case Later');
        $response->assertSee('3PL return exception workflow');
        $response->assertSee('Request Workflow Review');
        $response->assertSee(route('sample-cases.serial-mismatch'));
        $response->assertSee(route('solutions.serial-mismatch-return-evidence'));
        $response->assertSee('dossentry-sample-case-loom-en-final.mp4', false);
        $response->assertSee(json_encode(route('marketing.click-events.store')), false);
        $response->assertSee('property="og:title"', false);
    }

    public function test_serial_mismatch_evidence_page_serves_public_solution_content(): void
    {
        $response = $this->get('http://dossentry.com/serial-mismatch-return-evidence');

        $response->assertOk();
        $response->assertSee('Serial Number Mismatch Returns Need More Than Photos');
        $response->assertSee('Serial mismatch return evidence');
        $response->assertSee('View Sample Case');
        $response->assertSee(route('sample-cases.serial-mismatch'));
        $response->assertSee(route('solutions.3pl-return-exception-workflow'));
        $response->assertSee('sample-serial-mismatch-board.png', false);
        $response->assertSee(json_encode(route('marketing.click-events.store')), false);
        $response->assertSee('property="og:title"', false);
    }
}
