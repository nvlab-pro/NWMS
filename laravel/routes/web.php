<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index');
});

Route::get('/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
Route::post('/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);


Route::get('/bel', function () {
    return view('Site/bel/index');
});

Route::get('/bg', function () {
    return view('Site/bg/index');
});

Route::get('/ch', function () {
    return view('Site/ch/index');
});

Route::get('/de', function () {
    return view('Site/de/index');
});

Route::get('/fr', function () {
    return view('Site/fr/index');
});

Route::get('/gr', function () {
    return view('Site/gr/index');
});

Route::get('/kz', function () {
    return view('Site/kz/index');
});

Route::get('/pr', function () {
    return view('Site/bg/index');
});

Route::get('/rus', function () {
    return view('Site/rus/index');
});


Route::get('/sp', function () {
    return view('Site/bg/index');
});

Route::get('/ukr', function () {
    return view('Site/bg/index');
});

