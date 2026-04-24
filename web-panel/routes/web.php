<?php

use App\CPU\Helpers;
use App\Models\ReturnCase;
use App\Http\Controllers\Admin\EvidenceExportController;
use App\Http\Controllers\MarketingClickEventController;
use App\Http\Controllers\WorkflowReviewRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('healthz', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'time' => now()->toIso8601String(),
    ]);
});

$marketingPagePayload = function (): array {
    $sampleReturnId = (string) config('dossentry.sample_brand_review_return_id', 'RMA-1003');
    $sampleCaseId = null;

    try {
        if (Schema::hasTable('return_cases')) {
            $sampleCaseId = ReturnCase::query()
                ->where('return_id', $sampleReturnId)
                ->value('id');
        }
    } catch (\Throwable $exception) {
        $sampleCaseId = null;
    }

    return [
        'appName' => config('app.name') === 'Laravel' ? 'Dossentry' : config('app.name', 'Dossentry'),
        'demoLoginUrl' => 'https://demo.dossentry.com/admin/auth/login',
        'guestDemo' => config('dossentry.guest_demo'),
        'sampleCaseUrl' => route('sample-cases.serial-mismatch'),
        'auditUrl' => route('return-exception-audit'),
        'returnExceptionChecklistPdfUrl' => asset('assets/dossentry/dossentry-3pl-return-exception-checklist-2026-04.pdf'),
        'exceptionWorkflowUrl' => route('solutions.3pl-return-exception-workflow'),
        'serialMismatchEvidenceUrl' => route('solutions.serial-mismatch-return-evidence'),
        'sampleBrandReviewUrl' => $sampleCaseId
            ? URL::temporarySignedRoute('returns.brand-review', now()->addDays(30), ['id' => $sampleCaseId])
            : null,
    ];
};

$sampleSerialMismatchPayload = function () use ($marketingPagePayload): array {
    return array_merge($marketingPagePayload(), [
        'sampleCaseUrl' => route('sample-cases.serial-mismatch'),
        'sampleCasePdfUrl' => route('sample-cases.serial-mismatch.pdf'),
        'sampleCaseVideoUrl' => asset('assets/dossentry/dossentry-sample-case-loom-en-final.mp4'),
        'sampleCaseFacts' => [
            'returnId' => 'RMA-SAMPLE-1001',
            'expectedSku' => 'CRW500RO',
            'expectedSerial' => 'CR15788234',
            'observedLabel' => 'CRE6000M / DSCRE99905',
            'status' => 'Hold / Needs review',
        ],
        'sampleCaseAssets' => [
            'compareBoard' => asset('assets/dossentry/sample-serial-mismatch-board.png'),
            'receivedOverview' => asset('assets/dossentry/sample-received-overview.jpg'),
            'observedLabels' => asset('assets/dossentry/sample-observed-labels.jpg'),
            'cartonStack' => asset('assets/dossentry/sample-carton-stack.jpg'),
            'palletAngle' => asset('assets/dossentry/sample-pallet-angle.jpg'),
            'openedCase' => asset('assets/dossentry/sample-opened-case.jpg'),
            'openedLid' => asset('assets/dossentry/sample-opened-lid.jpg'),
        ],
    ]);
};

$solutionPagePayload = function (string $slug) use ($marketingPagePayload): array {
    $pages = [
        '3pl-return-exception-workflow' => [
            'pageKey' => 'solution_3pl_exception_workflow',
            'metaTitle' => 'Dossentry | 3PL Return Exception Workflow',
            'metaDescription' => 'See how multi-brand 3PL teams can handle high-risk return exceptions with one review-ready record instead of rebuilding the case later.',
            'metaImage' => asset('assets/dossentry/og-home.png'),
            'metaImageAlt' => 'Dossentry overview for warehouse-side return exception workflow.',
            'eyebrow' => '3PL return exception workflow',
            'title' => 'Handle High-Risk Return Exceptions Without Rebuilding the Case Later',
            'intro' => 'Dossentry helps multi-brand 3PL teams capture the right evidence, apply the right client rule, and send one review-ready case record when a return becomes disputed.',
            'proofPills' => [
                'Multi-brand 3PL fit',
                'Warehouse-side evidence',
                'Review-ready case record',
                'No station rebuild',
            ],
            'visualType' => 'video',
            'visualPoster' => asset('assets/dossentry/sample-serial-mismatch-board.png'),
            'visualSrc' => asset('assets/dossentry/dossentry-sample-case-loom-en-final.mp4'),
            'visualAlt' => 'Dossentry 77-second workflow demo.',
            'visualNote' => 'Use this page when the question is not “do we inspect returns,” but “what do we send back when a client challenges one messy case?”',
            'sections' => [
                [
                    'kicker' => 'What Breaks',
                    'title' => 'What breaks in most 3PL return workflows',
                    'copy' => 'Most warehouses do inspect returns. The real problem starts later, when the evidence is incomplete, the client rule is unclear, and someone has to rebuild the case from photos, folders, spreadsheets, and chat threads.',
                ],
                [
                    'kicker' => 'Why It Hurts',
                    'title' => 'Why these cases create so much friction',
                    'copy' => 'The costly cases are not the easy restocks. They are the cases with serial mismatch, damage ambiguity, missing parts, wrong item risk, or incomplete proof. That is where warehouse teams, client success teams, and brands start asking different versions of the same question: what actually happened here?',
                ],
                [
                    'kicker' => 'Workflow',
                    'title' => 'How the workflow should work',
                    'copy' => 'When a high-risk return arrives, the warehouse should follow the client playbook, capture the required evidence, document what was observed, and hold the case when the facts do not line up. The next reviewer should be able to understand the situation in under a minute.',
                ],
                [
                    'kicker' => 'Record',
                    'title' => 'What Dossentry puts in one case record',
                    'copy' => 'Each case can include the expected return record, observed condition, required photos, inspector notes, timeline, hold reason, and recommended next step. Instead of explaining the same case three times, the warehouse can send one link or one PDF.',
                ],
                [
                    'kicker' => 'Fit',
                    'title' => 'Built for the narrow part of returns that gets questioned later',
                    'copy' => 'Dossentry is built for the warehouse-side exception layer inside multi-brand 3PL operations. It is not trying to replace the entire returns platform. It is designed for the part of the workflow that becomes expensive when the case is challenged later.',
                ],
            ],
            'proofTitle' => 'What a better exception workflow changes',
            'proofBody' => 'Instead of reacting after the case gets messy, the warehouse creates one structured record while the item is still in hand. That makes hold decisions easier to defend and client review much faster.',
            'proofBullets' => [
                'One place for expected record, observed facts, and evidence completeness.',
                'Hold posture documented before the case gets pushed forward by habit.',
                'A shareable case record for ops, client success, or brand review.',
            ],
            'faq' => [
                [
                    'question' => 'Is this replacing a full returns platform?',
                    'answer' => 'No. This is the warehouse-side exception and review workflow, not a full end-to-end returns suite.',
                ],
                [
                    'question' => 'Who is this page for?',
                    'answer' => 'Founders, ops leads, and client-facing operators inside multi-brand 3PL teams.',
                ],
                [
                    'question' => 'What kinds of returns fit this workflow?',
                    'answer' => 'Serial mismatch, missing parts, damage ambiguity, wrong item risk, and incomplete evidence cases.',
                ],
                [
                    'question' => 'What is the fastest proof asset to show internally?',
                    'answer' => 'The public sample case and the 77-second demo are the quickest way to align people on the workflow.',
                ],
            ],
            'primaryCtaLabel' => 'Request Workflow Review',
            'primaryCtaUrl' => route('landing') . '#review-request',
            'primaryCtaTrack' => 'workflow_review',
            'secondaryCtaLabel' => 'View Sample Case',
            'secondaryCtaUrl' => route('sample-cases.serial-mismatch'),
            'secondaryCtaTrack' => 'sample_case',
            'navLinks' => [
                [
                    'label' => 'Serial mismatch evidence',
                    'url' => route('solutions.serial-mismatch-return-evidence'),
                ],
            ],
            'relatedLinks' => [
                [
                    'label' => 'Serial mismatch evidence page',
                    'url' => route('solutions.serial-mismatch-return-evidence'),
                ],
                [
                    'label' => 'Compare against generic inspection apps',
                    'url' => route('compare.generic-inspection-apps'),
                ],
            ],
        ],
        'serial-mismatch-return-evidence' => [
            'pageKey' => 'solution_serial_mismatch_evidence',
            'metaTitle' => 'Dossentry | Serial Mismatch Return Evidence',
            'metaDescription' => 'See how warehouses can document serial mismatch returns with the evidence, hold posture, and escalation context a reviewer actually needs.',
            'metaImage' => asset('assets/dossentry/sample-serial-mismatch-board.png'),
            'metaImageAlt' => 'Serial mismatch return evidence board.',
            'eyebrow' => 'Serial mismatch return evidence',
            'title' => 'Serial Number Mismatch Returns Need More Than Photos',
            'intro' => 'When the observed unit identity does not match the expected return record, the warehouse needs a defensible workflow for evidence, hold posture, and escalation.',
            'proofPills' => [
                'Serial comparison',
                'Hold before release',
                'Evidence completeness',
                'Shareable case record',
            ],
            'visualType' => 'image',
            'visualPoster' => asset('assets/dossentry/sample-serial-mismatch-board.png'),
            'visualSrc' => asset('assets/dossentry/sample-serial-mismatch-board.png'),
            'visualAlt' => 'Sample serial mismatch comparison board.',
            'visualNote' => 'The point is not having more photos. The point is showing what was expected, what was observed, and why the case stayed on hold.',
            'sections' => [
                [
                    'kicker' => 'Risk',
                    'title' => 'Why serial mismatch cases become expensive',
                    'copy' => 'A serial mismatch is not just another inspection note. It is a high-risk exception that can trigger refund disputes, client friction, and avoidable write-offs if the warehouse cannot clearly show what was expected, what was observed, and what was verified.',
                ],
                [
                    'kicker' => 'Evidence',
                    'title' => 'What the warehouse needs to capture',
                    'copy' => 'A reliable serial mismatch workflow should capture the expected SKU and serial, the observed label, comparison photos, opened-unit verification, inspector notes, and a clear hold reason. If any of that is missing, the next reviewer is forced to guess.',
                ],
                [
                    'kicker' => 'Hold',
                    'title' => 'Why the case should stay on hold',
                    'copy' => 'When the carton label or unit identity does not match the expected record, the case should not move forward as if nothing happened. The warehouse should maintain hold posture until the reviewer has enough evidence to decide the next action with confidence.',
                ],
                [
                    'kicker' => 'Review',
                    'title' => 'What a clean review record looks like',
                    'copy' => 'The reviewer should see the expected record, the observed mismatch, the evidence set, the timeline, and the reason the case is still on hold. That is the difference between a defensible exception workflow and a folder full of disconnected images.',
                ],
                [
                    'kicker' => 'Sample',
                    'title' => 'See a sample serial mismatch case',
                    'copy' => 'This sample shows how one warehouse-side case can be documented with a comparison board, evidence photos, timeline, and one shareable review-ready record.',
                ],
            ],
            'proofTitle' => 'Why this page works as a first proof asset',
            'proofBody' => 'Serial mismatch is concrete enough to understand quickly and expensive enough to matter. It is one of the cleanest examples of why a return exception should not be handled from memory.',
            'proofBullets' => [
                'Expected record and observed label shown side by side.',
                'Clear reason for hold posture instead of a vague inspector note.',
                'A direct path from warehouse evidence to reviewer decision.',
            ],
            'faq' => [
                [
                    'question' => 'Is this based on a real customer case?',
                    'answer' => 'No. It is a sample case built to show the workflow clearly.',
                ],
                [
                    'question' => 'Who should open this page first?',
                    'answer' => 'Warehouse managers, returns leads, ops leads, and founders evaluating how disputed returns are documented.',
                ],
                [
                    'question' => 'Can the case be shared outside the warehouse team?',
                    'answer' => 'Yes. The workflow is designed to produce one record that ops, client success, or a brand reviewer can open directly.',
                ],
                [
                    'question' => 'What should happen after the mismatch is found?',
                    'answer' => 'Capture the required proof, document the mismatch, keep the case on hold, and escalate with one review-ready record.',
                ],
            ],
            'primaryCtaLabel' => 'View Sample Case',
            'primaryCtaUrl' => route('sample-cases.serial-mismatch'),
            'primaryCtaTrack' => 'sample_case',
            'secondaryCtaLabel' => 'Request Workflow Review',
            'secondaryCtaUrl' => route('landing') . '#review-request',
            'secondaryCtaTrack' => 'workflow_review',
            'navLinks' => [
                [
                    'label' => '3PL workflow',
                    'url' => route('solutions.3pl-return-exception-workflow'),
                ],
            ],
            'relatedLinks' => [
                [
                    'label' => '3PL exception workflow page',
                    'url' => route('solutions.3pl-return-exception-workflow'),
                ],
                [
                    'label' => 'Watch the 77-second sample demo',
                    'url' => route('sample-cases.serial-mismatch') . '#sample-video',
                ],
            ],
        ],
    ];

    return array_merge($marketingPagePayload(), $pages[$slug]);
};

$redirectMarketingDemoHost = function (Request $request) {
    $host = $request->getHost();
    $demoHosts = Helpers::dossentry_public_demo_hosts();
    $internalHost = Helpers::dossentry_internal_admin_host();

    if ($internalHost) {
        $demoHosts[] = $internalHost;
    }

    if (in_array($host, $demoHosts, true)) {
        return redirect('admin/auth/login');
    }
};

Route::get('/', function (Request $request) use ($marketingPagePayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('landing', $marketingPagePayload());
})->name('landing');

Route::get('compare/generic-inspection-apps', function (Request $request) use ($marketingPagePayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('compare.generic-inspection-apps', $marketingPagePayload());
})->name('compare.generic-inspection-apps');

Route::get('sample-cases/serial-mismatch-review', function (Request $request) use ($sampleSerialMismatchPayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('sample-cases.serial-mismatch-review', $sampleSerialMismatchPayload());
})->name('sample-cases.serial-mismatch');

Route::get('return-exception-audit', function (Request $request) use ($marketingPagePayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('lead-magnets.return-exception-audit', $marketingPagePayload());
})->name('return-exception-audit');

Route::get('3pl-return-exception-workflow', function (Request $request) use ($solutionPagePayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('solutions.marketing-solution-page', $solutionPagePayload('3pl-return-exception-workflow'));
})->name('solutions.3pl-return-exception-workflow');

Route::get('serial-mismatch-return-evidence', function (Request $request) use ($solutionPagePayload, $redirectMarketingDemoHost) {
    if ($redirect = $redirectMarketingDemoHost($request)) {
        return $redirect;
    }

    return response()->view('solutions.marketing-solution-page', $solutionPagePayload('serial-mismatch-return-evidence'));
})->name('solutions.serial-mismatch-return-evidence');

Route::get('sample-cases/serial-mismatch-review/pdf', function () {
    $pdfPath = base_path('public/assets/dossentry/dossentry-sample-case-serial-mismatch-2026-04.pdf');

    abort_unless(is_file($pdfPath), 404);

    return response()->file($pdfPath, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="dossentry-sample-case-serial-mismatch-2026-04.pdf"',
    ]);
})->name('sample-cases.serial-mismatch.pdf');

Route::post('workflow-review-request', [WorkflowReviewRequestController::class, 'store'])
    ->name('workflow-review-requests.store');

Route::post('marketing/click-events', [MarketingClickEventController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('marketing.click-events.store');

Route::view('privacy-policy', 'legal.privacy')->name('privacy-policy');
Route::view('terms-of-service', 'legal.terms')->name('terms-of-service');

Route::middleware('signed')->group(function () {
    Route::controller(EvidenceExportController::class)->group(function () {
        Route::get('brand-review/{id}', 'brandReview')->name('returns.brand-review');
        Route::get('brand-review/{id}/pdf', 'brandReviewPdf')->name('returns.brand-review.pdf');
    });
});

Route::fallback(function(){
    return redirect('admin/auth/login');
});

Route::get('authentication-failed', function () {
    $errors = [];
    $errors[] = ['code' => 'auth-001', 'message' => 'Invalid credential! or unauthenticated.'];
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::get('product-details', function(){
    return view('admin-views.product.details');
})->name('admin-views.product.details');
