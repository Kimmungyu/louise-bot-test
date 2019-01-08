<?php
namespace App\Services;
use App\Libs\ePrinter;

use App\Kasse;
use App\Order;
use App\Order_Detail;
use App\Voucher;

//
use Auth;
use DB;

class SalesService {
  private $ePrinter;

  public function __construct(ePrinter $ePrinter)
  {
      $this->ePrinter = $ePrinter;
  }

  public function getDiscountGroup() {
    return DB::table('retail_discount_groups')->get();
  }

  public function getAllKasse() {
    return Kasse::where('dept_id', 1)->get();
  }

  public function getKasseOpenReason() {
    return DB::table('kasse_open_reason')->get();
  }

  public function getCreditCardAll() {
    return DB::table('creditcard_types')->get();
  }

  public function getOrderByReceiptNum(Array $params) {
    $originalOrder = Order::where('receipt_num', $params['receiptNum'])
                          ->where('kasse_id', $params['kasseId'])
                          ->with(['orderDetail', 'orderDetail.product'])
                          ->first();

    if(empty($originalOrder)) {
      return ['isValidReceiptNum' => false];
    }

    if(!$this->isOrderCancellable($params['receiptNum'], $params['kasseId'])) {
      return ['isValidReceiptNum' => true, 'isCancellable' => false];
    }

    $cancelledProducts = [];
    $cancelledOrderIds = Order::where('cancelled_receipt_num', $params['receiptNum'])
                          ->whereNull('receipt_num')
                          ->where('kasse_id', $params['kasseId'])
                          ->get(['id'])->toArray();

    if(!empty($cancelledOrderIds)) {
      $cancelledProducts = Order_Detail::with('product')->whereIn('order_id', array_column($cancelledOrderIds, 'id'))->get();
    }

    return ['isValidReceiptNum' => true, 'isCancellable' => true, 'originalOrder' => $originalOrder, 'cancelledProducts' => $cancelledProducts];
  }

  // 2018.11.08 WonkyoungLee
  public function getOrderById(String $orderId) {
    $receiptNum = Order::where('id', $orderId)->value('receipt_num');
    $kasseId = Order::where('id', $orderId)->value('kasse_id');

    $originalOrder = Order::where('receipt_num', $receiptNum)
                          ->with(['orderDetail', 'orderDetail.product'])
                          ->first();

    if(empty($originalOrder)) {
      return ['isValidId' => false];
    }

    $receiptNum = Order::where('id', $orderId)->value('receipt_num');
    $kasseId = Order::where('id', $orderId)->value('kasse_id');

    if(!$this->isOrderCancellable($receiptNum, $kasseId)) {
      return ['isValidReceiptNum' => true, 'isCancellable' => false];
    }

    $cancelledProducts = [];
    $cancelledOrderIds = Order::where('cancelled_receipt_num', $receiptNum)
                          ->whereNull('receipt_num')
                          ->where('kasse_id', $kasseId)
                          ->get(['id'])->toArray();

    if(!empty($cancelledOrderIds)) {
      $cancelledProducts = Order_Detail::with('product')->whereIn('order_id', array_column($cancelledOrderIds, 'id'))->get();
    }

    return ['isValidReceiptNum' => true, 'isCancellable' => true, 'originalOrder' => $originalOrder, 'cancelledProducts' => $cancelledProducts];
  }

  public function getSalesSummary(Array $params) {
    $items = [];
    $taxes = [];
    $sales = DB::table('orders')
            ->select(DB::raw(' SUM(if(is_cancelled = "N", creditcard_amount, creditcard_amount * -1)) creditSum,'.
                               ' SUM(if(is_cancelled = "N", eccard_amount, eccard_amount * -1)) ecSum,'.
                               ' SUM(if(is_cancelled = "N", cash_amount, cash_amount * -1)) cashSum,'.
                               ' SUM(if(is_cancelled = "N", voucher_amount, voucher_amount * -1)) voucherSum,'.
                               ' SUM(if(is_cancelled = "N", sales_price, sales_price * -1)) totalSum'))
            ->where('dept_id', 1)
            ->where('date','<=',$params['toDate']);
    if(!empty($params['frDate'])) {
      $sales->where('date', '>=',$params['frDate']);
    }
    if($params['kasseId'] != 'all') {
      $sales->where('kasse_id', $params['kasseId']);
    }
    $item = DB::table('order_details AS a')
            ->select(DB::raw(' b.category categoryName, b.name itemName, a.discount_rate, '.
                             ' SUM(IF(a.is_cancelled = "N", a.qty, a.qty * -1)) AS qty, SUM(IF(a.is_cancelled = "N", a.sales_price * a.qty, a.sales_price * a.qty * -1)) total'))
            ->join('retail_products AS b', 'a.item_code', 'b.code')
            ->where('a.dept_id', 1)
            ->where('a.date','<=',$params['toDate'])
            ->groupBy('b.category', 'b.name', 'a.discount_rate')
            ->orderBy('b.name', 'a.discount_rate');
    if(!empty($params['frDate'])) {
      $item->where('a.date', '>=',$params['frDate']);
    }
    if($params['kasseId'] != 'all') {
      $item->where('a.kasse_id', $params['kasseId']);
    }
    foreach($item->get()->toArray() as $i) {
      if(empty($items[$i->itemName])) {
        $items[$i->itemName] = [];
      }
      array_push($items[$i->itemName], $i);
    }
    $tax = DB::table('order_details')
            ->select(DB::raw(' tax_rate,'.
                             ' SUM(if(is_cancelled = "N", ROUND(sales_price - (sales_price / ( 1 + tax_rate)), 2) * qty, ROUND(sales_price - (sales_price / ( 1 + tax_rate)), 2) * qty * -1)) taxSum'))
            ->where('dept_id', 1)
            ->where('date','<=',$params['toDate'])
            ->groupBy('tax_rate');
    if(!empty($params['frDate'])) {
      $tax->where('date', '>=',$params['frDate']);
    }
    if($params['kasseId'] != 'all') {
      $tax->where('kasse_id', $params['kasseId']);
    }
    foreach($tax->get()->toArray() as $t) {
      if(empty($taxes[$t->tax_rate])) {
        $taxes[$t->tax_rate] = $t;
      }
    }
    return ['sales' => $sales->get()->toArray(), 'items' => $items, 'tax' => $taxes];
  }

  public function getOrdersWithDate(Array $params) {
    $query = Order::where('dept_id', 1)->where('date','<=',$params['toDate'])->with(['kasse', 'orderDetail', 'orderDetail.product', 'voucher'])->orderBy('created_at','DESC');
    if(!empty($params['frDate'])) {
      $query->where('date', '>=',$params['frDate']);
    }
    if($params['kasseId'] != 'all') {
      $query->where('kasse_id', $params['kasseId']);
    }
    return $query->get()->toArray();
  }
  
  public function insertOrder(Array $data) {
    $arrayOrder = [];
    $arrayProducts = [];
    $deliveryCost = 0;
    $kasseId = $this->getKasseId();

    $lastOrder = Order::where('date', date('Y-m-d'))->where('is_cancelled', 'N')->where('kasse_id', $kasseId)->orderBy('id', 'desc')->first();
    $receiptNum = empty($lastOrder) ? date('Ymd').'0000001' : date('Ymd').sprintf('%07d', (int)substr($lastOrder->receipt_num, -7) + 1);
    $orderNum = Order::where('date', date('Y-m-d'))->where('kasse_id', $kasseId)->max('order_num') + 1;

    $creditCardStatus = $data['payment']['creditCard']['status'] === '2' ? '1' : '0';
    $ecCardStatus = $data['payment']['ecCard']['status'] === '2' ? '1' : '0';
    $cashStatus = $data['payment']['cash']['status'] === '2' ? '1' : '0';

    $voucherStatus = $data['payment']['voucher_Sum']['status'] === '2' ? '1' : '0';
    $voucherArr = $data['payment']['voucher'];


    if(!empty($data['basket']['ADD_DLV_CHG'])) {
      $deliveryCost = $data['basket']['ADD_DLV_CHG']['product']['UVP'];
    }

    $arrayOrder = [
      'date' => date('Y-m-d'),
      'order_num' => $orderNum,
      'dept_id' => 1,
      'receipt_num' => $receiptNum,
      'cancelled_receipt_num' => $data['originalReceiptNum'] != '' ? $data['originalReceiptNum'] : null,
      'original_price' => $data['listPrice'],
      'sales_price' => $data['netPrice'],
      'payment_method' => $creditCardStatus.$ecCardStatus.$cashStatus.$voucherStatus,
      'creditcard_type_id' => $data['payment']['creditCard']['type'] != 0 ? $data['payment']['creditCard']['type'] : null,
      'creditcard_amount' => $data['payment']['creditCard']['amount'],
      'eccard_amount' => $data['payment']['ecCard']['amount'],
      'cash_amount' => $data['payment']['cash']['amount'],
      'cash_received' => $data['cashReceived'],

      // 2018.10.25 WonkyoungLee
      'voucher_amount' => $data['payment']['voucher_Sum']['status'] === '2' ? $data['payment']['voucher_Sum']['amount'] : 0,
      'voucher_code' => $data['payment']['voucher_Sum']['status'] === '2' ? $data['payment']['voucher_Sum']['code'] : null,
      'voucher_list' => $data['payment']['voucher'],

      'delivery_cost' => $deliveryCost,
      'is_voucher_sales' => 'N',
      'member_id' => $data['membership'],
      'kasse_id' => $kasseId,
      'cashier_id' => Auth::User()->id
    ];

    DB::beginTransaction();
    try {
      $order_id = Order::create($arrayOrder);
      $idx = 1;

      foreach($data['basket'] as $basket) {
        array_push($arrayProducts, [
          'date' => date('Y-m-d'),
          'item_num'  => $idx++,
          'order_id' => $order_id->id,
          'dept_id' => 1,
          'item_id' => $basket['product']['id'],
          'item_code' => $basket['product']['code'],
          'qty' => $basket['qty'],
          'sales_price' => !empty($basket['product']['discountedUVP']) ? $basket['product']['discountedUVP'] : $basket['product']['UVP'],
          'tax_rate' => $basket['product']['tax_rate'],
          'discount_rate' => !empty($basket['product']['dc']) && $basket['product']['dc'] > 0 ? $basket['product']['dc'] : 0,
          'kasse_id' => $kasseId,
          'cashier_id' => Auth::User()->id,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),

          'netto' => round((!empty($basket['product']['discountedUVP']) ? $basket['product']['discountedUVP'] : $basket['product']['UVP'])*$basket['qty']/(1 + $basket['product']['tax_rate']), 2),
          'vat' => round((!empty($basket['product']['discountedUVP']) ? $basket['product']['discountedUVP'] : $basket['product']['UVP'])*$basket['qty'], 2) - round((!empty($basket['product']['discountedUVP']) ? $basket['product']['discountedUVP'] : $basket['product']['UVP'])*$basket['qty']/(1 + $basket['product']['tax_rate']), 2)
        ]);
      }

      DB::table('order_details')->insert($arrayProducts);

      if($arrayOrder['cash_amount'] > 0) {
        DB::table('kasse_open')->insert([
          'dept_id' => in_array($kasseId, [3, 4]) ? 2 : 1,
          'kasse_id' => $kasseId,
          'open_type' => 2,
          'cashier_id' => Auth::User()->id,
          'amount' => $arrayOrder['cash_amount'],
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }

      // 2018.10.26 WonkyoungLee      
      for($i = 0; $i < count($arrayOrder['voucher_list']); $i++)
      {
          if($arrayOrder['voucher_list'][$i]['amount'] > 0) 
          {
            DB::table('vouchers')->insert([
              'code' => $arrayOrder['voucher_list'][$i]['code'],
              'order_id' => $order_id->id,
              'amount' => $arrayOrder['voucher_list'][$i]['amount'],
              'discount_rate' => 0,
              'type' => 2,
              'dept_id' => in_array($kasseId, [3, 4]) ? 2 : 1,
              'kasse_id' => $kasseId,
              'cashier_id' => Auth::User()->id,
              'created_at' => date('Y-m-d H:i:s')
            ]);
        }

      }
      DB::commit();
    } catch(\Exception $e) {
      DB::rollback();
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    
    $pResult = $this->ePrinter->printOK(Order::with(['orderDetail', 'orderDetail.product'])->where('id', $order_id->id)->first());
    return ['ok' => true, 'printerOK' => $pResult, 'orderId' => $order_id->id];
  }

  public function cancelOrder($data) {
    //dd($data);
    $arrayProducts = [];
    $updateData = ['cancelled_date' => date('Y-m-d'), 'updated_at'=> date('Y-m-d H:i:s')];
    $orderNum = Order::where('date', date('Y-m-d'))->where('kasse_id', $data['order']['kasse_id'])->max('order_num') + 1;
    $payment_method = '';
    $payment_method = ((float)$data['order']['creditcard_amount'] > 0 ? '1' : '0').
                      ((float)$data['order']['eccard_amount'] > 0 ? '1' : '0').
                      ((float)$data['order']['cash_amount'] > 0 ? '1' : '0').
                      ((float)$data['order']['voucher_amount'] > 0 ? '1' : '0');
    DB::beginTransaction();
    try {
      Order::where('id', $data['order']['id'])->update($updateData);
      Order_Detail::where('order_id', $data['order']['id'])->update($updateData);
      $cancelledOrder = Order::create([
        'date' => date('Y-m-d'),
        'order_num' => $orderNum,
        //        'receipt_num' => '',
        'cancelled_receipt_num' => $data['order']['receipt_num'],
        'dept_id' => 1,
        'is_cancelled' => 'Y',
        'original_price' => $data['order']['sales_price'],
        'sales_price' => $data['order']['sales_price'],
        'payment_method' => $payment_method,
        'creditcard_type_id' => $data['order']['creditcard_amount'] > 0 ? $data['order']['creditcard_type_id'] : null,
        'creditcard_amount' => $data['order']['creditcard_amount'],
        'eccard_amount' => $data['order']['eccard_amount'],
        'cash_amount' => $data['order']['cash_amount'],
        'voucher_amount' => $data['order']['voucher_amount'],
        'member_id' => $data['order']['member_id'],
        'kasse_id' => $data['order']['kasse_id'],
        'cashier_id' => Auth::User()->id,
      ]);
      foreach($data['order']['order_detail'] as $product) {
        DB::table('order_details')->insert([
          'date' => date('Y-m-d'),
          'item_num'  => $product['item_num'],
          'order_id' => $cancelledOrder->id,
          'dept_id' => 1,
          'item_id' => $product['item_id'],
          'item_code' => $product['item_code'],
          'qty' => $product['qty'],
          'sales_price' => $product['sales_price'],
          'tax_rate' => $product['tax_rate'],
          'discount_rate' => $product['discount_rate'],
          'is_cancelled' => 'Y',
          'kasse_id' => $product['kasse_id'],
          'cashier_id' => Auth::User()->id,
          'created_at' => date('Y-m-d H:i:s'),
          'netto' => round(($product['sales_price'])*$product['qty']/(1 + $product['tax_rate']), 2),
          'vat' => round(($product['sales_price'])*$product['qty'], 2) - round(($product['sales_price'])*$product['qty']/(1 + $product['tax_rate']), 2)
        ]);
      }
      if($data['order']['cash_amount'] > 0) {
        DB::table('kasse_open')->insert([
          'dept_id' => in_array($product['kasse_id'], [3, 4]) ? 2 : 1,
          'kasse_id' => $product['kasse_id'],
          'open_type' => 3,
          'cashier_id' => Auth::User()->id,
          'amount' => $data['order']['cash_amount'],
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }
      if($data['order']['voucher_amount'] > 0) {
        DB::table('vouchers')->insert([
          'code' => $data['order']['voucher_code'],
          'order_id' => $cancelledOrder->id,
          'amount' => (float)$data['order']['voucher_amount'],
          'discount_rate' => 0,
          'type' => 3,
          'expired_at' => null,
          'dept_id' => 1, // shop: 1, cafe: 2
          'kasse_id' => $data['order']['kasse_id'],
          'cashier_id' => Auth::User()->id,
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }
      DB::commit();
    } catch(\Exception $e) {
      DB::rollback();
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    $pResult = $this->ePrinter->cancelData($data);
    // original
    // $pResult = $this->ePrinter->cancelData(Order::with(['orderDetail', 'orderDetail.product'])->where('id', $cancelledOrder->id)->first());
    return ['ok' => true, 'printerOK' => $pResult];
  }

  public function openCashier($data) {
    $kasseId = $this->getKasseId();
    try {
      DB::table('kasse_open')->insert([
        'dept_id' => in_array($kasseId, [3, 4]) ? 2 : 1,
        'kasse_id' => $kasseId,
        'open_type' => $data['open_type'],
        'cashier_id' => Auth::User()->id,
        'reason_id' => $data['reason_id'] ?? null,
        'amount' => $data['amount'] ?? null,
        'note' => $data['note'] ?? null,
        'created_at' => date('Y-m-d H:i:s')
      ]);
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => 'Fail to open Kasse!', 'err' => $e->getMessage()];
    }
    return $this->ePrinter->openCashier();
  }

  public function getKasseId() {
    return Auth::user()->kasse_id;
  }

  public function getCashSumByKasse(String $date = null) {
    $kasseId = $this->getKasseId();
    $lastCash = $this->getLastCashByKasseId($kasseId, $date);
    $cashSum = DB::table('kasse_open')
                ->select(DB::raw(
                  ' SUM( '.
                  '   CASE WHEN open_type = 1 THEN '.
                  '           CASE WHEN reason_id = 1 THEN amount '.
                  '                WHEN reason_id IN (2, 3) THEN amount * -1 '.
                  '           END '.
                  '        WHEN open_type = 2 THEN amount '.
                  '        WHEN open_type = 3 THEN amount * -1 '.
                  '   END ) cashSum '
                ))
                ->whereDate('created_at', $date ?? date('Y-m-d'))
                ->where('kasse_id', $kasseId)
                ->get();
    return (float)$lastCash + (float)$cashSum[0]->cashSum;
  }

  public function dailyClosing() {
    $kasseId = $this->getKasseId();
    $kasseCols = $this->getColsByKasseId($kasseId);
    // 1. 마감현금 기입
    $dailyClosing = DB::table('daily_closing')->where('date', date('Y-m-d'))->first();
    $cashSum = $this->getCashSumByKasse();
    try {
      if(empty($dailyClosing)) {
        DB::table('daily_closing')->insert([
          'date' => date('Y-m-d'),
          'transaction_id' => (int)(DB::table('daily_closing')->max('transaction_id')) + 1,
          $kasseCols['name'] => $cashSum,
          $kasseCols['closed'] => date('Y-m-d H:i:s')
        ]);
      }
      else {
        DB::table('daily_closing')->where('date', date('Y-m-d'))->update([
          $kasseCols['name'] => $cashSum,
          $kasseCols['closed'] => date('Y-m-d H:i:s')
        ]);
      }
    } catch(\Exception $e) {
        return ['ok' => false, 'msg' => 'Something is wrong...', 'err' => $e->getMessage()];
    }
    // 2. 회계처리
    // TO-DO
    return ['ok' => true];
  }

  public function getColsByKasseId(int $kasseId) : Array {
    $kasses = [
      3 => ['name' => 'cafe1', 'closed' => 'cafe1_closed'],
      4 => ['name' => 'cafe2', 'closed' => 'cafe2_closed'],
      5 => ['name' => 'retail1', 'closed' => 'retail1_closed'],
      6 => ['name' => 'retail2', 'closed' => 'retail2_closed']
    ];
    return $kasses[$kasseId];
  }

  public function getLastCashByKasseId(int $kasseId, String $date = null) {
    $kasseCols = $this->getColsByKasseId($kasseId);
    $lastClosing = DB::table('daily_closing')->where('date', '<', $date ?? date('Y-m-d'))->orderby('date', 'desc')->first();
    return empty($lastClosing) ? 0 : $lastClosing->{$kasseCols['name']};
  }

  public function reprintReceipt(int $orderId) {
    $order = Order::with(['orderDetail', 'orderDetail.product', 'voucher'])->where('id', $orderId)->first();
    if(count($order->orderDetail) > 0) {
      return $order->is_cancelled == 'N' ? $this->ePrinter->printOK($order, 'Y') : $this->ePrinter->cancelData($order, 'Y');
    }
    if(count($order->voucher) > 0) {

      return $order->is_cancelled == 'N' ? $this->ePrinter->voucherOK($order, 'Y') : $this->ePrinter->cancelVoucher($order, 'Y');

    }
  }

  public function isOrderCancellable(String $receiptNum, String $kasseId) {
    $originalOrder = Order::where('receipt_num', $receiptNum)->where('kasse_id', $kasseId)->sum('sales_price');
    $cancelledOrderSum = Order::where('cancelled_receipt_num', $receiptNum)->whereNull('receipt_num')->where('kasse_id', $kasseId)->sum('sales_price');
    return (float)$originalOrder - (float)$cancelledOrderSum > 0 ? true : false;
  }

  public function createVoucher($data) {
    $arrayOrder = [];
    $vouchers = [];

    $arrayProducts = [];

    $kasseId = $this->getKasseId();

    $lastOrder = Order::where('date', date('Y-m-d'))->where('is_cancelled', 'N')->where('kasse_id', $kasseId)->orderBy('id', 'desc')->first();
    $receiptNum = empty($lastOrder) ? date('Ymd').'0000001' : date('Ymd').sprintf('%07d', (int)substr($lastOrder->receipt_num, -7) + 1);
    $orderNum = Order::where('date', date('Y-m-d'))->where('kasse_id', $kasseId)->max('order_num') + 1;

    $creditCardStatus = (float)$data['cc_amount'] > 0 ? '1' : '0';
    $ecCardStatus = (float)$data['ec_amount'] > 0 ? '1' : '0';
    $cashStatus = (float)$data['cash_amount'] > 0 ? '1' : '0';

    $arrayOrder = [
      'date' => date('Y-m-d'),
      'order_num' => $orderNum,

      'dept_id' => 2,

      'receipt_num' => $receiptNum,
      'cancelled_receipt_num' => null,
      'original_price' => $data['original_price'],
      'sales_price' => $data['sales_price'],
      'payment_method' => $creditCardStatus.$ecCardStatus.$cashStatus.'0',
      'creditcard_type_id' => $data['cc_type'] != 0 ? $data['cc_type'] : null,
      'creditcard_amount' => $data['cc_amount'],
      'eccard_amount' => $data['ec_amount'],
      'cash_amount' => $data['cash_amount'],
      'cash_received' => 0,
      'is_takeout' => 'N',
      'is_voucher_sales' => 'Y',
      'kasse_id' => $kasseId,
      'cashier_id' => Auth::User()->id
    ];

    DB::beginTransaction();
    try {
      $order_id = Order::create($arrayOrder);
      $idx = 1;

      foreach($data['vouchers'] as $voucher) {
        array_push($vouchers, [
          'code' => $voucher['code'],
          'order_id' => $order_id->id,
          'amount' => (float)$voucher['amount'],
          'discount_rate' => (float)$voucher['dc'],
          'type' => 1,
          'expired_at' => null,
          'dept_id' => 1,
          'kasse_id' => $kasseId,
          'cashier_id' => Auth::User()->id,
          'created_at' => date('Y-m-d H:i:s')
        ]);

        // 2018.10.16 Wonkyoung Add
        array_push($arrayProducts, [
          'date' => date('Y-m-d'),
          'item_num'  => $idx++,
          'order_id' => $order_id->id,
          'dept_id' => 1,
          'item_id' => 0000,
          'item_code' => 'Gutschein('.$voucher['code'].')',
          'qty' => 1,
          'sales_price' => (float)$voucher['amount'],
          'tax_rate' => 0.00,
          'discount_rate' => 0.00,
          'kasse_id' => $kasseId,
          'cashier_id' => Auth::User()->id,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }
      DB::table('vouchers')->insert($vouchers);
      DB::table('order_details')->insert($arrayProducts); // 2018.10.16 Wonkyoung Add


      if($arrayOrder['cash_amount'] > 0) {
        DB::table('kasse_open')->insert([
          'dept_id' => in_array($kasseId, [3, 4]) ? 2 : 1,
          'kasse_id' => $kasseId,
          'open_type' => 2,
          'cashier_id' => Auth::User()->id,
          'amount' => $arrayOrder['cash_amount'],
          'created_at' => date('Y-m-d H:i:s')
        ]);
      }
      DB::commit();
    } catch(\Exception $e) {
      DB::rollback();
      return ['ok' => false, 'msg' => $e->getMessage()];
    }

    $pResult = $this->ePrinter->voucherOK(Order::with(['voucher'])->where('id', $order_id->id)->first(), 'N');
    return ['ok' => true, 'printerOK' => $pResult];
  }

  public function checkVoucherByCode(String $voucherCode) {
    $voucher = DB::table('vouchers')
                ->select(DB::raw(
                  ' code, '.
                  ' SUM( '.
                  '   CASE WHEN type = 1 or type = 3 THEN amount'.
                  '        WHEN type = 2 or type = 4 THEN amount * -1'.
                  '   END ) checkSum '
                ))
                ->where('code', $voucherCode)
                ->groupBy('code')
                ->get();
    return ['ok' => count($voucher) > 0 ? true : false, 'voucher' => $voucher];
  }
  // 2018.10.17 WonkoyngLee
  public function checkPossibleCode(String $voucherCode) {    
    $voucher = DB::select(DB::raw("SELECT EXISTS (SELECT CODE FROM vouchers WHERE CODE='$voucherCode') AS ENABLE"));

    return ['ok' => count($voucher) > 0 ? 1 : 0, 'count' => $voucher[0]->ENABLE];
  }

  // 2018.11.08 WonkyoungLee
  public function getVoucherByCode(String $voucherCode) {
    $voucher = DB::table('vouchers')
                ->where('code', $voucherCode)
                ->where('type', '1')
                ->get();
    return ['ok' => count($voucher) > 0 ? true : false, 'voucher' => $voucher];

  }
}
?>