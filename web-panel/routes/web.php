<?php

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
