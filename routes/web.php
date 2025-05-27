<?php


use App\Livewire\Analytics;
use App\Livewire\FakenessDetail; 
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/analytics', Analytics::class)->name('analytics');

Route::get('/abc/{slug}', FakenessDetail::class)->name('fakeness.detail');
