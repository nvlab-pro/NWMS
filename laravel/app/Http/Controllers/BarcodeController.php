<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function print(Request $request)
    {
        $barcode = $request->get('barcode');
        $offerId = $request->get('id');

        return view('barcode.printOfferLabel', compact('barcode', 'offerId'));
    }
}