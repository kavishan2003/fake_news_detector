<?php

use App\Livewire\FakenessDetail;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/{slug}', FakenessDetail::class)->name('fakeness.detail');
