<?php

namespace Tests\Feature\Returns;

use App\Models\ReturnCase;
use App\Models\ReturnCaseEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsReturnsFixtures;
use Tests\TestCase;

class RefundQueueFlowTest extends TestCase
{
    use BuildsReturnsFixtures;
    use RefreshDatabase;

    public function test_queue_cannot_release_case_with_incomplete_evidence(): void
    {
        $admin = $this->signInAdmin();
        $case = $this->createReturnCaseWithDecision([
            'refund_status' => 'hold',
            'evidence_complete' => false,
            'evidence_photo_count' => 2,
            'required_photo_count' => 4,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.cases.refund-decision', $case->id), [
            'redirect_to' => 'queue',
            'refund_status' => 'ready_to_release',
            'decision_note' => 'Attempted release',
        ]);

        $response->assertSessionHasErrors('refund_status');
        $this->assertSame('hold', $case->fresh()->refund_status);
    }

    public function test_batch_queue_action_updates_cases_and_records_batch_event(): void
    {
        $admin = $this->signInAdmin();
        $firstCase = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-BATCH-1',
            'refund_status' => 'hold',
        ]);
        $secondCase = $this->createReturnCaseWithDecision([
            'return_id' => 'RMA-BATCH-2',
            'refund_status' => 'hold',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.returns.queue.refund-decision'), [
            'case_ids' => [$firstCase->id, $secondCase->id],
            'refund_status' => 'needs_review',
            'decision_note' => 'Batch escalation for audit.',
        ]);

        $response->assertRedirect(route('admin.returns.queue.index'));

        $this->assertSame('needs_review', $firstCase->fresh()->refund_status);
        $this->assertSame('needs_review', $secondCase->fresh()->refund_status);

        $latestEvent = ReturnCaseEvent::query()
            ->where('return_case_id', $firstCase->id)
            ->latest('id')
            ->first();

        $this->assertSame('Decision review updated from batch queue action', $latestEvent?->title);
        $this->assertSame('Batch escalation for audit.', $latestEvent?->description);
    }

    public function test_queue_page_uses_decision_queue_language(): void
    {
        $admin = $this->signInAdmin();
        $this->createReturnCaseWithDecision([
            'refund_status' => 'hold',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.returns.queue.index'));

        $response->assertOk();
        $response->assertSee('Decision Queue');
        $response->assertSee('Ready for brand review');
        $response->assertSee('Needs ops review');
    }
}
