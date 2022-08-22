<?php

use Arifpay\Arifpay\ArifPay;
use Arifpay\Arifpay\Helper\ArifpaySupport;
use Arifpay\Arifpay\Lib\ArifpayBeneficary;
use Arifpay\Arifpay\Lib\ArifpayCheckoutItem;
use Arifpay\Arifpay\Lib\ArifpayCheckoutRequest;
use Arifpay\Arifpay\Lib\ArifpayOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$arifpay = new ArifPay('Your-api-key');
$dt = Carbon::now()->addHours(6);
$expired = "2023-01-13T17:09:42.411";
$data = new ArifpayCheckoutRequest(
    "https://api.arifpay.com",
    floor(rand() * 10000) . "",
    'https://api.arifpay.com',
    'https://gateway.arifpay.net/test/callback',
    'https://127.0.0.1/beu-laravel',
    ["TELEBIRR"],
    $expired,
    [
        ArifpayCheckoutItem::fromJson([
            "name" => 'Bannana',
            "price" => 10,
            "quantity" => 1,
        ]),
    ],
    [
        ArifpayBeneficary::fromJson([
            "accountNumber" => '01320811436100',
            "bank" => 'AWINETAA',
            "amount" => 10.0,
        ]),
    ],
);

Route::get("/test", function (Request $request) use($arifpay, $data) {
    
    $session = $arifpay->checkout->create($data, new ArifpayOptions(false));
    return $session;
});

Route::get("/direct/awash", function (Request $request) use($arifpay, $data) {
    
    $session = $arifpay->checkout->create($data, new ArifpayOptions(false));

    return $arifpay->directPay->awash->pay($session->session_id, "251961186323");
    
});
Route::get("/direct/awash/verify/{id}", function (Request $request) use($arifpay, $data) {
    
    $session = $arifpay->checkout->create($data, new ArifpayOptions(false));
    dd($request->get("id"));
    return $arifpay->directPay->awash->verify($session->session_id, $request->get("id"));
    
});

Route::get("/direct/telebirr", function (Request $request) use($arifpay, $data) {
   
    $session = $arifpay->checkout->create($data, new ArifpayOptions(false));

    return $arifpay->directPay->telebirr->pay($session->session_id);
    
});

Route::get("/fetch/{id}", function (Request $request, $id) use($arifpay, $data) {
    $arifpay = new ArifPay('Your-api-key');
    return $arifpay->checkout->fetch($id,new ArifpayOptions(false));
});

Route::get("/cancel/{id}", function (Request $request, $id) use($arifpay, $data) {
    $arifpay = new ArifPay('Your-api-key');
    return $arifpay->checkout->cancel($id,new ArifpayOptions(false));
});
