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
        'sampleBrandReviewUrl' => $sampleCaseId
            ? URL::temporarySignedRoute('returns.brand-review', now()->addDays(30), ['id' => $sampleCaseId])
            : null,
    ];
};

$sampleSerialMismatchPayload = function () use ($marketingPagePayload): array {
    return array_merge($marketingPagePayload(), [
        'sampleCaseUrl' => route('sample-cases.serial-mismatch'),
        'sampleCasePdfUrl' => route('sample-cases.serial-mismatch.pdf'),
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
