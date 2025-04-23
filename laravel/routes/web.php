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

Route::get('/docs/theory/receiving', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/theory/assembling_the_order', function () {
    return view('Site/docs/theory/AssemblingTheOrder', ['lang' => 'en']);
});

Route::get('/docs/theory/sorting_goods', function () {
    return view('Site/docs/theory/SortingGoods', ['lang' => 'en']);
});

Route::get('/docs/theory/continuity_processes', function () {
    return view('Site/docs/theory/ContinuityProcesses', ['lang' => 'en']);
});

Route::get('/docs/theory/packing_orders', function () {
    return view('Site/docs/theory/PackingOrders', ['lang' => 'en']);
});

Route::get('/docs/theory/order_labeling', function () {
    return view('Site/docs/theory/OrderLabeling', ['lang' => 'en']);
});

Route::get('/docs/theory/dispatch_of_orders', function () {
    return view('Site/docs/theory/DispatchOfOrders', ['lang' => 'en']);
});

Route::get('/docs/theory/queue_management', function () {
    return view('Site/docs/theory/QueueManagement', ['lang' => 'en']);
});



Route::get('/docs/api/authentification', function () {
    return view('Site/docs/api/Authentification', ['lang' => 'en']);
});

Route::get('/docs/api/php_library', function () {
    return view('Site/docs/api/PHPLibrary', ['lang' => 'en']);
});

Route::get('/docs/api/products', function () {
    return view('Site/docs/api/Products', ['lang' => 'en']);
});

Route::get('/docs/api/acceptances', function () {
    return view('Site/docs/api/Acceptances', ['lang' => 'en']);
});



Route::get('/docs/receiving', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/putaway_of_goods', function () {
    return view('Site/docs/theory/InDevelopment', ['lang' => 'en']);
});

Route::get('/docs/theory/location_labeling', function () {
    return view('Site/docs/theory/ContinuityProcesses', ['lang' => 'en']);
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

Route::get('/{lang}/docs/theory/assembling_the_order', function ($lang) {
    return view('Site/docs/theory/AssemblingTheOrder', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/sorting_goods', function ($lang) {
    return view('Site/docs/theory/SortingGoods', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/continuity_processes', function ($lang) {
    return view('Site/docs/theory/ContinuityProcesses', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/packing_orders', function ($lang) {
    return view('Site/docs/theory/PackingOrders', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/order_labeling', function ($lang) {
    return view('Site/docs/theory/OrderLabeling', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/dispatch_of_orders', function ($lang) {
    return view('Site/docs/theory/DispatchOfOrders', ['lang' => $lang]);
});

Route::get('/{lang}/docs/theory/queue_management', function ($lang) {
    return view('Site/docs/theory/QueueManagement', ['lang' => $lang]);
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

Route::post('/{lang}/new-user-form', [\App\Http\Controllers\FormNewUser::class, 'submit']);
