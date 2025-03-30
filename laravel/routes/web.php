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

Route::get('/docs/theory', function () {
    return view('Site/docs/theory/main', ['lang' => 'en']);
});

Route::get('/docs/theory/receiving_goods', function () {
    return view('Site/docs/theory/receivingGoods', ['lang' => 'en']);
});

Route::get('/docs/theory/putaway_of_goods', function () {
    return view('Site/docs/theory/PutawayOfGoods', ['lang' => 'en']);
});

Route::get('/docs/theory/warehouse_labeling', function () {
    return view('Site/docs/theory/WarehouseLabeling', ['lang' => 'en']);
});

Route::get('/docs/theory/whats_next', function () {
    return view('Site/docs/theory/WhatsNext', ['lang' => 'en']);
});

Route::get('/docs/theory/orders', function () {
    return view('Site/docs/theory/Orders', ['lang' => 'en']);
});

Route::get('/docs/theory/theory_again', function () {
    return view('Site/docs/theory/TheoryAgain', ['lang' => 'en']);
});

Route::get('/docs/receiving', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/putaway_of_goods', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/location_labeling', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/orders', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/receiving', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
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


Route::get('/{lang}/docs/theory', function ($lang) {
    return view('Site/docs/theory/main', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/receiving_goods', function ($lang) {
    return view('Site/docs/theory/receivingGoods', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/putaway_of_goods', function ($lang) {
    return view('Site/docs/theory/PutawayOfGoods', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/warehouse_labeling', function ($lang) {
    return view('Site/docs/theory/WarehouseLabeling', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/whats_next', function ($lang) {
    return view('Site/docs/theory/WhatsNext', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/orders', function ($lang) {
    return view('Site/docs/theory/Orders', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/theory_again', function ($lang) {
    return view('Site/docs/theory/TheoryAgain', ['lang' => $lang]);
});

Route::get('/{lang}/docs/receiving', function ($lang) {
    return view('Site/docs/theory/InDevelopment', ['lang' => $lang]);
});

Route::get('/{lang}/docs/putaway_of_goods', function ($lang) {
    return view('Site/docs/theory/InDevelopment', ['lang' => $lang]);
});

Route::get('/{lang}/docs/location_labeling', function ($lang) {
    return view('Site/docs/theory/InDevelopment', ['lang' => $lang]);
});

Route::get('/{lang}/docs/orders', function ($lang) {
    return view('Site/docs/theory/InDevelopment', ['lang' => $lang]);
});

Route::get('/{lang}/docs/receiving', function ($lang) {
    return view('Site/docs/theory/InDevelopment', ['lang' => $lang]);
});

Route::post('/{lang}/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
