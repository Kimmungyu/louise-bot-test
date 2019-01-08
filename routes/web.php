<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return redirect('/sales');
});

Auth::routes();

Route::get('hello', function() {
  return 'Hello!';
});

Route::group(['prefix' => 'sales'], function () {
  Route::get('/', 'SalesController@index')->name('sales.index');
  Route::post('insertOrder', 'SalesController@insertOrder');
  Route::post('openCashier', 'SalesController@openCashier');
  Route::put('cancelOrder', 'SalesController@cancelOrder');
  Route::get('getSalesSummary', 'SalesController@getSalesSummary');
  Route::get('getOrdersWithDate', 'SalesController@getOrdersWithDate');
  Route::get('getOrderByReceiptNum', 'SalesController@getOrderByReceiptNum');
  Route::get('getOrderById/{orderId}', 'SalesController@getOrderById');// 2018.11.08 WonkyoungLee
  Route::get('getCashSumByKasse', 'SalesController@getCashSumByKasse');
  Route::get('dailyClosing', 'SalesController@dailyClosing');
  Route::post('reprintReceipt', 'SalesController@reprintReceipt');
  Route::post('createVoucher', 'SalesController@createVoucher');
  Route::get('checkVoucherByCode/{voucherCode}', 'SalesController@checkVoucherByCode');

  Route::get('checkPossibleCode/{voucherCode}', 'SalesController@checkPossibleCode');// 2018.10.17 WonkyoungLee
  Route::get('getVoucherByCode/{voucherCode}', 'SalesController@getVoucherByCode');// 2018.11.08 WonkyoungLee 

  Route::post('refundVoucher', 'SalesController@refundVoucher');


});

Route::group(['prefix' => 'order'], function () {
});

Route::group(['prefix' => 'product'], function () {
  Route::get('getProductWithBarcode/{barcode}', 'ProductController@getProductWithBarcode');
  Route::get('getProductWithProductCode/{productCode}', 'ProductController@getProductWithProductCode');
  Route::get('findProduct', 'ProductController@findProduct');
  Route::get('findDamiProduct', 'ProductController@findDamiProduct');
  Route::post('addNewProduct', 'ProductController@addNewProduct');
  Route::get('syncProduct', 'ProductController@syncProduct');
  Route::get('getCouponInfo/{Code}', 'ProductController@getCouponInfo');// 2018.11.12 WonkyoungLee
});

Route::group(['prefix' => 'misc'], function () {
  Route::get('/', 'MiscController@index');
  Route::get('checkPrice', 'MiscController@checkPrice');
  Route::get('findProduct', 'MiscController@findProduct');
  Route::get('findDamiProduct', 'MiscController@findDamiProduct');
  Route::get('printBarcode', 'MiscController@printBarcode');
  Route::get('printNameTag', 'MiscController@printNameTag');
  Route::get('printPage', function() {
    return view('misc.printPage');
  });
  Route::get('NamePage', function(){
    return view('misc.NamePage');
  });
  Route::get('listProduct', 'MiscController@listProduct');
});
