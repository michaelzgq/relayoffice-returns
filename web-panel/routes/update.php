<?php
/*
|--------------------------------------------------------------------------
| Install Routes
|--------------------------------------------------------------------------
|
| This route is responsible for handling the intallation process
|
|
|
*/

use App\Http\Controllers\UpdateController;
use Illuminate\Support\Facades\Route;
Route::controller(UpdateController::class)->group(function() {
    Route::get('/', 'update_software_index')->name('index');
    Route::post('update-system', 'update_software')->name('update-system');
});
Route::fallback(function () {
    return redirect('/');
});
