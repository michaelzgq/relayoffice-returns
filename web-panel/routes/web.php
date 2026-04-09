<?php

use App\Http\Controllers\Admin\EvidenceExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::get('/', function (Request $request) {
    $host = $request->getHost();
    $demoHosts = [
        'demo.dossentry.com',
        'relayoffice-returns-app.onrender.com',
        'demo.relayoffice.ai',
    ];

    if (in_array($host, $demoHosts, true)) {
        return redirect('admin/auth/login');
    }

    return response()->view('landing', [
        'appName' => config('app.name') === 'Laravel' ? 'Dossentry' : config('app.name', 'Dossentry'),
        'demoLoginUrl' => 'https://demo.dossentry.com/admin/auth/login',
    ]);
})->name('landing');

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
