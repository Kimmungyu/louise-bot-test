<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\SalesService;

class SalesController extends Controller
{
  private $salesService;

  public function __construct(SalesService $salesService)
  {
      $this->middleware('auth');
      $this->salesService = $salesService;
  }
  
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $discountGroup = $this->salesService->getDiscountGroup();
    $kasses = $this->salesService->getAllKasse();
    $reasons = $this->salesService->getKasseOpenReason();
    $creditCards = $this->salesService->getCreditCardAll();

    return view('sales.index', [
      'discountGroup' => $discountGroup,
      'kasses' => $kasses,
      'reasons' => $reasons,
      'creditCards' => $creditCards
    ]);
  }

  public function getOrderByReceiptNum(Request $request) {
    return response($this->salesService->getOrderByReceiptNum($request->all()));
  }
  // 2018.11.08 WonkyoungLee
  public function getOrderById($orderId) {
    return response($this->salesService->getOrderById($orderId));
  }

  public function getOrdersWithDate(Request $request) {
    $orders = $this->salesService->getOrdersWithDate($request->all());
    return response(['orders' => $orders]);
  }

  public function getSalesSummary(Request $request) {
    $data = $this->salesService->getSalesSummary($request->all());
    return response(['data' => $data]);
  }

  public function insertOrder(Request $request) {
    return response($this->salesService->insertOrder($request->all()));
  }

  public function cancelOrder(Request $request) {
    return response($this->salesService->cancelOrder($request->all()));
  }

  public function openCashier(Request $request) {
    return response($this->salesService->openCashier($request->all()));
  }

  public function getCashSumByKasse() {
    return response($this->salesService->getCashSumByKasse());
  }

  public function dailyClosing() {
    return response($this->salesService->dailyClosing());
  }

  public function reprintReceipt(Request $request) {
    return response($this->salesService->reprintReceipt((int)$request->orderId));
  }

  public function createVoucher(Request $request) {
    return response($this->salesService->createVoucher($request->all()));
  }

  public function checkVoucherByCode($voucherCode) {
    return response($this->salesService->checkVoucherByCode($voucherCode));
  }
  // 2018.10.17 WonkyoungLee
  public function checkPossibleCode($voucherCode) {
    return response($this->salesService->checkPossibleCode($voucherCode));
  }

  // 2018.11.08 WonkyoungLee
  public function getVoucherByCode($voucherCode) {
    return response($this->salesService->getVoucherByCode($voucherCode));
  }
}

