<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index');
});

Route::post('/new-user-form', [\App\Http\Controllers\FormNewUser  ::class, 'submit']);


Route::get('/rus', function () {
    return view('Site/rus/index');
});

