<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Site/index', ['lang' => 'en']);
});

Route::get('/about_wms', function () {
    return view('Site/about_wms', ['lang' => 'en']);
});

Route::get('/terms', function () {
    return view('Site/terms', ['lang' => 'en']);
});

Route::get('/privacy', function () {
    return view('Site/privacy', ['lang' => 'en']);
});

Route::get('/pricing', function () {
    return view('Site/pricing', ['lang' => 'en']);
});

Route::get('/support', function () {
    return view('Site/support', ['lang' => 'en']);
});

$availableLanguages = [
    'bel', 'bg', 'ch', 'de', 'en', 'fr', 'gr',
    'it', 'jp', 'kl', 'kz', 'pr', 'ro', 'rus', 'sp', 'tr', 'ukr'
];

foreach ($availableLanguages as $lang) {
    Route::get('/' . $lang, function () use ($lang) { // добавили use ($lang)
        return view('Site/index', ['lang' => $lang]);
    });
}

Route::get('/{lang}/about_wms', function ($lang) {
    return view('Site/about_wms', ['lang' => $lang]);
});

Route::get('/{lang}/terms', function ($lang) {
    return view('Site/terms', ['lang' => $lang]);
});

Route::get('/{lang}/privacy', function ($lang) {
    return view('Site/privacy', ['lang' => $lang]);
});

Route::get('/{lang}/pricing', function ($lang) {
    return view('Site/pricing', ['lang' => $lang]);
});

Route::get('/{lang}/support', function ($lang) {
    return view('Site/support', ['lang' => $lang]);
});

Route::post('/{lang}/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
