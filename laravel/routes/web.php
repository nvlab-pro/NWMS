<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index', ['lang' => 'en']);
});

Route::get('/terms', function () {
    return view('Site/terms', ['lang' => 'en']);
});

Route::get('/privacy', function () {
    return view('Site/privacy', ['lang' => 'en']);
});

$availableLanguages = ['en', 'bg', 'rus', 'ukr', 'fr', 'de', 'sp', 'ch', 'pr', 'kz', 'bel', 'gr'];

foreach ($availableLanguages as $lang) {
    Route::get('/' . $lang, function () use ($lang) { // добавили use ($lang)
        return view('Site/index', ['lang' => $lang]);
    });
}

Route::get('/{lang}/terms', function ($lang) {
    return view('Site/terms', ['lang' => $lang]);
});

Route::get('/{lang}/privacy', function ($lang) {
    return view('Site/privacy', ['lang' => $lang]);
});

Route::post('/{lang}/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
