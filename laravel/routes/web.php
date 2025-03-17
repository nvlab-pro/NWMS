<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index', ['lang' => 'en']);
});

Route::get('/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
Route::post('/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);


Route::get('/bel', function () {
    return view('Site/index', ['lang' => 'bel']);
});

Route::get('/bg', function () {
    return view('Site/index', ['lang' => 'bg']);
});

Route::get('/ch', function () {
    return view('Site/index', ['lang' => 'ch']);
});

Route::get('/de', function () {
    return view('Site/index', ['lang' => 'de']);
});

Route::get('/fr', function () {
    return view('Site/index', ['lang' => 'fr']);
});

Route::get('/gr', function () {
    return view('Site/index', ['lang' => 'gr']);
});

Route::get('/kz', function () {
    return view('Site/index', ['lang' => 'kz']);
});

Route::get('/pr', function () {
    return view('Site/index', ['lang' => 'pr']);
});

Route::get('/rus', function () {
    return view('Site/index', ['lang' => 'rus']);
});

Route::get('/sp', function () {
    return view('Site/index', ['lang' => 'sp']);
});

Route::get('/ukr', function () {
    return view('Site/index', ['lang' => 'ukr']);
});

