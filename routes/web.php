<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.pages.home');
})->name('home');

Route::get('/about', function () {
    return view('layouts.pages.about');
})->name('about');

Route::get('/booking', function () {
    return view('layouts.pages.booking');
})->name('booking');

Route::get('/contact', function () {
    return view('layouts.pages.contact');
})->name('contact');
