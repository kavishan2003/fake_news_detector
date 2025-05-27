<?php


use App\Livewire\Analytics;
use App\Livewire\FakenessDetail; 
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/{slug}', FakenessDetail::class)->name('fakeness.detail');

Route::get('/analytics', Analytics::class);