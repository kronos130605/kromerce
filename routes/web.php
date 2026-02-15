<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kromerce', function () {
    return view('kromerce-preview');
})->name('kromerce.preview');

Route::get('/kromerce-full', function () {
    return view('kromerce-full-preview');
})->name('kromerce.full');
