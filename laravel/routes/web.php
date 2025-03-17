<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index', ['lang' => 'en']);
});

Route::get('/{lang}', function ($lang) {
    return view('Site/index', ['lang' => $lang]);
});

Route::post('/{lang}/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
