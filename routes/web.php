<?php


use Faker\Guesser\Name;
use App\Livewire\Detector;
use App\Livewire\Analytics;
use App\Livewire\FakenessDetail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectorController;
use Illuminate\Routing\Middleware\ThrottleRequests;


Route::get('/', function () {
    return view('welcome');
});





Route::middleware('throttle:daily-ip-limit')->group(function () {

    Route::post('/checkFakeness', [DetectorController::class, 'checkFakeness'])->name('checkFakeness');
    Route::get('/checkFakeness', function () {
        return redirect('/');
    });
});
Route::middleware('throttle:60,1')->group(function () {

    Route::get('/analytics', Analytics::class)->name('analytics');

    Route::get('/abc/{slug}', FakenessDetail::class)->name('fakeness.detail');

});
// Route::get('/api/data', [SomeController::class, 'index']);