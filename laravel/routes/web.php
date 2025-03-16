<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index');
});

Route::get('/2', function () {
    return view('Site/index2');
});

Route::post('/new-user-form', [\App\Http\Controllers\FormNewUser  ::class, 'submit']);
