<?php
namespace App\Libs;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

use App\Helpers\Price;
use App\Kasse;

class ePrinter {
  private $ePrinter;
  private $aLeft = Printer::JUSTIFY_LEFT;
  private $aCenter = Printer::JUSTIFY_CENTER;
  private $aRight = Printer::JUSTIFY_RIGHT;

  public function __construct()
  {
    $kasse = Kasse::where('pc_ip', $_SERVER['REMOTE_ADDR'])->first();
    if(empty($kasse)) {
      $connector = new WindowsPrintConnector("posprinter");
    }
    else {
      $connector = new WindowsPrintConnector("smb://".$kasse->pc_account.":".$kasse->pc_password."@".$kasse->pc_ip."/posprinter");
    }
    $this->ePrinter = new Printer($connector);
  }

  public function printOK($order, $copy = 'N') {
    try {
      for($i=0;$i<2;$i++) {
        if($i == 0) {
          $this->header('Accepted', '- For Shop -', $copy);
        }
        else {
          $this->header('Accepted', '- For Customer -', $copy);
        }

        $brutto7Sum = 0;
        $brutto19Sum = 0;
        $priceSum = 0;
        $originalPriceSum = 0;


        $this->ePrinter->text('Kasse ID . '.$order->kasse_id);
        $this->ePrinter->feed(1);
        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);

        foreach($order->orderDetail as $item) {
          if($item->product->category == 'COUPON')
          {
            $UVP = (float)((int)$item->sales_price);
            $UVPStr = number_format($UVP, 2, ',', '.');
            $UVPWithClass = '';

            $price = 0;
            $price = round($UVP, 2) * (int)$item->qty;
            $priceStr = number_format($price, 2, ',', '.');

            $itemName = substr($item->product->name,0, 25);
            $sumStr = $item->qty.' * '.$UVPStr;

            $this->ePrinter->text($itemName.$this->textPos($itemName, $UVPWithClass).$UVPWithClass);
            $this->ePrinter->feed(1);

            $this->ePrinter->setEmphasis(true);

            $this->ePrinter->text($sumStr.$this->textPos($sumStr, $priceStr).$priceStr);
            $this->ePrinter->feed(1);
            
            $this->ePrinter->setEmphasis(false);
            
            $priceSum += $price;
          }
          else
          {
            $UVP = (float)((int)$item->product->promo_UVP === 0 || $item->product->promo_UVP == null ? $item->product->UVP : $item->product->promo_UVP);
            $UVPStr = number_format($UVP, 2, ',', '.');
            $UVPWithClass = $UVPStr.' '.((float)$item->tax_rate == 0.07 ? 'A' : 'B');

            $price = 0;
            $price = round($UVP, 2) * (int)$item->qty;
            $priceStr = number_format($price, 2, ',', '.');

            $itemName = substr($item->product->name,0, 25);
            $sumStr = $item->qty.' * '.$UVPStr;

            $this->ePrinter->text($itemName.$this->textPos($itemName, $UVPWithClass).$UVPWithClass);
            $this->ePrinter->feed(1);

            $this->ePrinter->setEmphasis(true);
            if($item->discount_rate > 0) {
              $UVP = round((float)$item->sales_price, 2);
              $price = $UVP * (int)$item->qty;
              $priceStr = number_format($price, 2, ',', '.');
              $sumStr .= ' * -'.round((float)$item->discount_rate* 100, 0).'%';

              $this->ePrinter->text($sumStr.$this->textPos($sumStr, $priceStr).$priceStr);
              $this->ePrinter->feed(1);
            }
            else {
              $this->ePrinter->text($sumStr.$this->textPos($sumStr, $priceStr).$priceStr);
              $this->ePrinter->feed(1);
            }
            $this->ePrinter->setEmphasis(false);

            if((float)$item->tax_rate === 0.07) {
              $brutto7Sum += $price;
            }
            else if((float)$item->tax_rate === 0.19) {
              $brutto19Sum += $price;
            }
            $priceSum += $price;
            $originalPriceSum += $price;
          }
        }
        // $brutto7Sum
        $brutto19Sum = round($brutto19Sum / $originalPriceSum * $priceSum, 2);
        $brutto7Sum = $priceSum - $brutto19Sum;

        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);
        $this->ePrinter->setEmphasis(true);
        $this->ePrinter->setTextSize(2, 1);
        $this->ePrinter->text('Summe'.$this->textPos('Summe', number_format($priceSum, 2, ',', '.').' EUR', 2).number_format($priceSum, 2, ',', '.').' EUR');
        $this->ePrinter->feed(1);
        $this->ePrinter->setTextSize(1, 1);
        $this->ePrinter->setEmphasis(false);

        $this->ePrinter->feed(1);
        $this->ePrinter->text('A = 7%, B = 19%');
        $this->ePrinter->feed(1);

        if($brutto7Sum > 0) {
          $netto7Sum = Price::getNetto($brutto7Sum, 0.07);
          $this->ePrinter->text('7% Netto.'.$this->textPos('7% Netto.', number_format($netto7Sum, 2, ',', '.').' EUR').number_format($netto7Sum, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('MwSt 7%'.$this->textPos('MwSt 7%', number_format($brutto7Sum - $netto7Sum, 2, ',', '.').' EUR').number_format($brutto7Sum - $netto7Sum, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if ($brutto19Sum > 0) {
          $netto19Sum = Price::getNetto($brutto19Sum, 0.19);
          $this->ePrinter->text('19% Netto.'.$this->textPos('19% Netto.', number_format($netto19Sum, 2, ',', '.').' EUR').number_format($netto19Sum, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('MwSt 19%'.$this->textPos('MwSt 19%', number_format($brutto19Sum - $netto19Sum, 2, ',', '.').' EUR').number_format($brutto19Sum - $netto19Sum, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        $this->ePrinter->feed(1);

        if(abs($order->creditcard_amount) != 0) {
          $this->ePrinter->text('Credit Card: '.$this->textPos('Credit Card: ', number_format($order->creditcard_amount, 2, ',', '.').' EUR').number_format($order->creditcard_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs($order->eccard_amount) != 0) {
          $this->ePrinter->text('EC Card: '.$this->textPos('EC Card: ', number_format($order->eccard_amount, 2, ',', '.').' EUR').number_format($order->eccard_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs($order->voucher_amount) != 0) {
          $this->ePrinter->text('Gutschein('.$order->voucher_code.'): '.$this->textPos('Gutschein('.$order->voucher_code.'): ', number_format($order->voucher_amount, 2, ',', '.').' EUR').number_format($order->voucher_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);

        }  
        if(abs($order->cash_amount) != 0) {
          $this->ePrinter->text('BAR: '.$this->textPos('BAR: ', number_format($order->cash_amount, 2, ',', '.').' EUR').number_format($order->cash_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('BAR received: '.$this->textPos('BAR received: ', number_format($order->cash_received, 2, ',', '.').' EUR').number_format($order->cash_received, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('BAR change: '.$this->textPos('BAR change: ', number_format(((float)$order->cash_received - (float)$order->cash_amount), 2, ',', '.').' EUR').number_format(((float)$order->cash_received - (float)$order->cash_amount), 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        $this->footer($order->receipt_num);
      }

      /* Close printer */
      $this->ePrinter->close();
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    return ['ok' => true];
  }

  public function cancelData($order, $copy = 'N') {
    try {
      for($i=0;$i<2;$i++) {
        $brutto7Sum = 0;
        $brutto19Sum = 0;
        $priceSum = 0;
        $withCouponPriceSum = 0;
        $itemCount = 0;

        $cancelledBrutto7Sum = 0;
        $cancelledBrutto19Sum = 0;
        $cancelledPriceSum = 0;
        $withCouponCancelledPriceSum = 0;     
        $cancelledItemCount = 0;   
        
        $originalBrutto7Sum = 0;
        $originalBrutto19Sum = 0;
        $originalPriceSum = 0;
        $withCouponOriginalPriceSum = 0;     
        $originalItemCount = 0;

        $masterCouponAmount = 0;

        $isCouponOrder = 0;
        $checkRemainItem = 0;
        $tmp = 0;

        if($i == 0) {
          $this->header('Canceled', '- For Shop -', $copy);
        }
        else {
          $this->header('Canceled', '- For Customer -', $copy);
        }

        $this->ePrinter->text('Kasse ID . '.$order['order']['dept_id']);
        $this->ePrinter->feed(1);
        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);

        foreach($order['order']['order_detail'] as $item) 
        {
          $UVP = round((float)$item['sales_price'], 2);
          $price = $UVP * (int)$item['qty'];
          $itemStr = substr($item['product']['name'],0 , 25).' * '.$item['qty'];
          if($item['product']['category'] != 'COUPON')
          {
            $priceStr = number_format($price * -1, 2, ',', '.').($item['tax_rate'] == 0.07 ? ' A' : ' B');
          }
          else
          {
            $priceStr = number_format($price * -1, 2, ',', '.').('');
          }
          $this->ePrinter->text($itemStr.$this->textPos($itemStr, $priceStr).$priceStr);
          $this->ePrinter->feed(1);

          if($item['product']['category'] != 'COUPON' && $item['product']['category'] != 'MASTERCOUPON')
          {
            if((float)$item['tax_rate'] == 0.07) {
              $brutto7Sum += $price;
            }
            else if((float)$item['tax_rate'] == 0.19) {
              $brutto19Sum += $price;
            }
            $priceSum += $price;
          }
          else if($item['product']['category'] == 'MASTERCOUPON')
          {
            // $price = $price * -1;
            $priceSum += $price;
          }

          if($item['product']['category'] != 'MASTERCOUPON')
          {
            $itemCount += (int)$item['qty'];
          }
          else
          {
            $masterCouponAmount = $price * -1;
          }


          $withCouponPriceSum += $price;
        }
        
        if(isset($order['cancelled']) && count($order['cancelled']) > 0 && !empty($order['cancelled']))
        {
          foreach($order['cancelled'] as $cancelledItem) 
          {
            if($cancelledItem['product']['category'] != 'COUPON' || $item['product']['category'] != 'MASTERCOUPON')
            {
              if((float)$cancelledItem['tax_rate'] == 0.07)
              {
                $cancelledBrutto7Sum += round((float)$cancelledItem['sales_price'], 2)  * (int)$cancelledItem['qty'];
              }
              else if((float)$cancelledItem['tax_rate'] == 0.19)
              {
                $cancelledBrutto19Sum  += round((float)$cancelledItem['sales_price'], 2)  * (int)$cancelledItem['qty'];
              }
              $cancelledPriceSum += round((float)$cancelledItem['sales_price'], 2)  * (int)$cancelledItem['qty'];
            }
            // $withCouponcancelledPriceSum += $cancelledItem['netto'] + $cancelledItem['vat'];
            if($cancelledItem['product']['category'] != 'MASTERCOUPON')
            {
              $cancelledItemCount += (int)$cancelledItem['qty'];
            }
            else
            {
              $masterCouponAmount += ((float)$cancelledItem['netto'] + (float)$cancelledItem['vat']) * -1;
              // $cancelledPriceSum += $masterCouponAmount;
            }
          }
        }

        foreach($order['original']['order_detail'] as $originalItem) 
        {
          if($originalItem['product']['category'] != 'COUPON')
          {
            if((float)$originalItem['tax_rate'] == 0.07)
            {
              $originalBrutto7Sum += round((float)$originalItem['sales_price'], 2)  * (int)$originalItem['qty'];
            }
            else if((float)$originalItem['tax_rate'] == 0.19)
            {
              $originalBrutto19Sum  += round((float)$originalItem['sales_price'], 2)  * (int)$originalItem['qty'];
            }
            $originalPriceSum += round((float)$originalItem['sales_price'], 2)  * (int)$originalItem['qty'];
          }
          else
          {
            $originalPriceSum += round((float)$originalItem['sales_price'], 2)  * (int)$originalItem['qty'];
            $isCouponOrder = 1;
          }
          // $withCouponOriginalPriceSum += $originalItem['netto'] + $originalItem['vat'];
          $originalItemCount += (int)$originalItem['qty'];
        }

        $checkRemainItem = $originalItemCount - $cancelledItemCount - $itemCount;

        if($isCouponOrder == 1 && $checkRemainItem == 0)
        {
          $tmp = 1;
          $brutto19Sum = $originalBrutto19Sum - $cancelledBrutto19Sum;
          $brutto7Sum = $originalBrutto7Sum - $cancelledBrutto7Sum;
        }
        else if($checkRemainItem == 0)
        {
          $tmp = 2;
          $brutto19Sum = $originalBrutto19Sum - $cancelledBrutto19Sum + $masterCouponAmount;
          $brutto7Sum = $originalBrutto19Sum - $brutto19Sum;
        }

        $this->ePrinter->feed(1);

        $brutto19Sum = round($brutto19Sum / $priceSum * $withCouponPriceSum, 2);
        $brutto7Sum = $withCouponPriceSum - $brutto19Sum;
        
        // $this->ePrinter->text($brutto19Sum.' . '.$brutto7Sum.' . '.$tmp);
        // $this->ePrinter->feed(1);
        // $this->ePrinter->text($originalPriceSum.' . '.$cancelledPriceSum.' . '.$priceSum.' . '.$masterCouponAmount.' . '.$withCouponPriceSum);
        // $this->ePrinter->feed(1);
        // $this->ePrinter->text($isCouponOrder.' . '.$originalItemCount.' . '.$cancelledItemCount.' . '.$checkRemainItem);
        // $this->ePrinter->feed(1);

        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);
        $this->ePrinter->setEmphasis(true);
        $this->ePrinter->setTextSize(2, 1);
        $this->ePrinter->text('Summe'.$this->textPos('Summe', number_format((float)$order['order']['sales_price'] * -1, 2, ',', '.').' EUR', 2).number_format((float)$order['order']['sales_price'] * -1, 2, ',', '.').' EUR');
        $this->ePrinter->feed(1);
        $this->ePrinter->setTextSize(1, 1);
        $this->ePrinter->setEmphasis(false);
        $this->ePrinter->feed(1);
        $this->ePrinter->text('A = 7%, B = 19%');
        $this->ePrinter->feed(1);

        if($brutto7Sum > 0) {
          $netto7Sum = Price::getNetto($brutto7Sum, 0.07);
          $this->ePrinter->text('7% Netto.'.$this->textPos('7% Netto.', number_format($netto7Sum * -1, 2, ',', '.').' EUR').number_format($netto7Sum * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('MwSt 7%'.$this->textPos('MwSt 7%', number_format(($brutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR').number_format(($brutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if ($brutto19Sum > 0) {
          $netto19Sum = Price::getNetto($brutto19Sum, 0.19);
          $this->ePrinter->text('19% Netto.'.$this->textPos('19% Netto.', number_format($netto19Sum * -1, 2, ',', '.').' EUR').number_format($netto19Sum * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
          $this->ePrinter->text('MwSt 19%'.$this->textPos('MwSt 19%', number_format(($brutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR').number_format(($brutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }

        // if($cancelledBrutto7Sum > 0) {
        //   $netto7Sum = Price::getNetto($cancelledBrutto7Sum, 0.07);
        //   $this->ePrinter->text('7% Netto.'.$this->textPos('7% Netto.', number_format($netto7Sum * -1, 2, ',', '.').' EUR').number_format($netto7Sum * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        //   $this->ePrinter->text('MwSt 7%'.$this->textPos('MwSt 7%', number_format(($cancelledBrutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR').number_format(($cancelledBrutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        // }
        // if ($cancelledBrutto19Sum > 0) {
        //   $netto19Sum = Price::getNetto($cancelledBrutto19Sum, 0.19);
        //   $this->ePrinter->text('19% Netto.'.$this->textPos('19% Netto.', number_format($netto19Sum * -1, 2, ',', '.').' EUR').number_format($netto19Sum * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        //   $this->ePrinter->text('MwSt 19%'.$this->textPos('MwSt 19%', number_format(($cancelledBrutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR').number_format(($cancelledBrutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        // }

        // if($originalBrutto7Sum > 0) {
        //   $netto7Sum = Price::getNetto($originalBrutto7Sum, 0.07);
        //   $this->ePrinter->text('7% Netto.'.$this->textPos('7% Netto.', number_format($netto7Sum * -1, 2, ',', '.').' EUR').number_format($netto7Sum * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        //   $this->ePrinter->text('MwSt 7%'.$this->textPos('MwSt 7%', number_format(($originalBrutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR').number_format(($originalBrutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        // }
        // if ($originalBrutto19Sum > 0) {
        //   $netto19Sum = Price::getNetto($originalBrutto19Sum, 0.19);
        //   $this->ePrinter->text('19% Netto.'.$this->textPos('19% Netto.', number_format($netto19Sum * -1, 2, ',', '.').' EUR').number_format($netto19Sum * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        //   $this->ePrinter->text('MwSt 19%'.$this->textPos('MwSt 19%', number_format(($originalBrutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR').number_format(($originalBrutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR');
        //   $this->ePrinter->feed(1);
        // }

        $this->ePrinter->feed(1);

        if(abs($order['order']['creditcard_amount']) != 0) {
          $this->ePrinter->text('Credit Card: '.$this->textPos('Credit Card: ', number_format($order['order']['creditcard_amount'] * -1, 2, ',', '.').' EUR').number_format($order['order']['creditcard_amount'] * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs($order['order']['eccard_amount']) != 0) {
          $this->ePrinter->text('EC Card: '.$this->textPos('EC Card: ', number_format($order['order']['eccard_amount'] * -1, 2, ',', '.').' EUR').number_format($order['order']['eccard_amount'] * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs($order['order']['cash_amount']) != 0) {
          $this->ePrinter->text('BAR: '.$this->textPos('BAR: ', number_format($order['order']['cash_amount'] * -1, 2, ',', '.').' EUR').number_format($order['order']['cash_amount'] * -1, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }

        if($i == 0) {
          $this->footer($order['order']['receipt_num'], 'del');
        }
        else {
          $this->footer($order['order']['receipt_num']);
        }
      }

      if(abs((float)$order['order']['cash_amount']) != 0) {
        $this->ePrinter->pulse();
      }
      /* Close printer */
      $this->ePrinter->close();
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    return ['ok' => true];
  }

  // public function cancelData($order, $copy = 'N') {
  //   try {
  //     for($i=0;$i<2;$i++) {
  //       $brutto7Sum = 0;
  //       $brutto19Sum = 0;
  //       $priceSum = 0;
  //       $cancelledBrutto7Sum = 0;
  //       $cancelledBrutto19Sum = 0;

  //       if($i == 0) {
  //         $this->header('Canceled', '- For Shop -', $copy);
  //       }
  //       else {
  //         $this->header('Canceled', '- For Customer -', $copy);
  //       }

  //       $this->ePrinter->text('Kasse ID . '.$order->kasse_id);
  //       $this->ePrinter->feed(1);
  //       $this->ePrinter->text('------------------------------------------');
  //       $this->ePrinter->feed(1);

  //       foreach($order->orderDetail as $item) {
  //         $UVP = round((float)$item->sales_price, 2);
  //         $price = $UVP * (int)$item->qty;
  //         $itemStr = substr($item->product->name,0 , 25).' * '.$item->qty;
  //         $priceStr = number_format($price * -1, 2, ',', '.').($item->tax_rate == 0.07 ? ' A' : ' B');
  //         $this->ePrinter->text($itemStr.$this->textPos($itemStr, $priceStr).$priceStr);
  //         $this->ePrinter->feed(1);

  //         if((float)$item->tax_rate == 0.07) {
  //           $brutto7Sum += $price;
  //         }
  //         else if((float)$item->tax_rate == 0.19) {
  //           $brutto19Sum += $price;
  //         }
  //         $priceSum += $price;
  //       }

  //       $brutto19Sum = round($brutto19Sum / $priceSum * $priceSum, 2);
  //       $brutto7Sum = $priceSum - $brutto19Sum;

  //       $this->ePrinter->text('------------------------------------------');
  //       $this->ePrinter->feed(1);
  //       $this->ePrinter->setEmphasis(true);
  //       $this->ePrinter->setTextSize(2, 1);
  //       $this->ePrinter->text('Summe'.$this->textPos('Summe', number_format((float)$order->sales_price * -1, 2, ',', '.').' EUR', 2).number_format((float)$order->sales_price * -1, 2, ',', '.').' EUR');
  //       $this->ePrinter->feed(1);
  //       $this->ePrinter->setTextSize(1, 1);
  //       $this->ePrinter->setEmphasis(false);
  //       $this->ePrinter->feed(1);
  //       $this->ePrinter->text('A = 7%, B = 19%');
  //       $this->ePrinter->feed(1);

  //       if($brutto7Sum > 0) {
  //         $netto7Sum = Price::getNetto($brutto7Sum, 0.07);
  //         $this->ePrinter->text('7% Netto.'.$this->textPos('7% Netto.', number_format($netto7Sum * -1, 2, ',', '.').' EUR').number_format($netto7Sum * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //         $this->ePrinter->text('MwSt 7%'.$this->textPos('MwSt 7%', number_format(($brutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR').number_format(($brutto7Sum - $netto7Sum) * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //       }
  //       if ($brutto19Sum > 0) {
  //         $netto19Sum = Price::getNetto($brutto19Sum, 0.19);
  //         $this->ePrinter->text('19% Netto.'.$this->textPos('19% Netto.', number_format($netto19Sum * -1, 2, ',', '.').' EUR').number_format($netto19Sum * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //         $this->ePrinter->text('MwSt 19%'.$this->textPos('MwSt 19%', number_format(($brutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR').number_format(($brutto19Sum - $netto19Sum) * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //       }

  //       $this->ePrinter->feed(1);

  //       if(abs($order->creditcard_amount) != 0) {
  //         $this->ePrinter->text('Credit Card: '.$this->textPos('Credit Card: ', number_format($order->creditcard_amount * -1, 2, ',', '.').' EUR').number_format($order->creditcard_amount * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //       }
  //       if(abs($order->eccard_amount) != 0) {
  //         $this->ePrinter->text('EC Card: '.$this->textPos('EC Card: ', number_format($order->eccard_amount * -1, 2, ',', '.').' EUR').number_format($order->eccard_amount * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //       }
  //       if(abs($order->cash_amount) != 0) {
  //         $this->ePrinter->text('BAR: '.$this->textPos('BAR: ', number_format($order->cash_amount * -1, 2, ',', '.').' EUR').number_format($order->cash_amount * -1, 2, ',', '.').' EUR');
  //         $this->ePrinter->feed(1);
  //       }

  //       if($i == 0) {
  //         $this->footer($order->cancelled_receipt_num, 'del');
  //       }
  //       else {
  //         $this->footer($order->cancelled_receipt_num);
  //       }
  //     }

  //     if(abs((float)$order->cash_amount) != 0) {
  //       $this->ePrinter->pulse();
  //     }
  //     /* Close printer */
  //     $this->ePrinter->close();
  //   } catch(\Exception $e) {
  //     return ['ok' => false, 'msg' => $e->getMessage()];
  //   }
  //   return ['ok' => true];
  // }

  public function openCashier() {
    try {
      $this->ePrinter->pulse();
      /* Close printer */
      $this->ePrinter->close();
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    return ['ok' => true];
  }

  public function header(String $type, String $for = '', String $copy) {
    $this->ePrinter->setJustification($this->aCenter);
    $this->ePrinter->setEmphasis(true);
    $this->ePrinter->setTextSize(2, 1);
    $this->ePrinter->text("LOUISE26");
    $this->ePrinter->setTextSize(1, 1);
    $this->ePrinter->setEmphasis(false);
    $this->ePrinter->feed(1);
    $this->ePrinter->text("Louisenstraße 26");
    $this->ePrinter->feed(1);
    $this->ePrinter->text("61348 Bad Homburg vor der Höhe");
    $this->ePrinter->feed(1);
    $this->ePrinter->text(date('d/m/Y H:i'));
    $this->ePrinter->feed(1);
    if($copy === 'Y') {
      $this->ePrinter->text('- Copied -');
      $this->ePrinter->feed(1);
    }
    if($for !== '') {
      $this->ePrinter->text($for);
      $this->ePrinter->feed(1);
    }
    $this->ePrinter->text($type);
    $this->ePrinter->feed(2);
    $this->ePrinter->text("------------------------------------------");
    $this->ePrinter->feed(1);
    $this->ePrinter->setJustification($this->aLeft);
  }

  public function footer(String $barcode = '', String $type = '') {
    $this->ePrinter->setJustification($this->aCenter);
    $this->ePrinter->text('------------------------------------------');
    $this->ePrinter->feed(1);
    $this->ePrinter->text('Vielen Dank für Ihren Einkauf.');
    $this->ePrinter->feed(1);
    $this->ePrinter->text('Thank you for your visit.');
    $this->ePrinter->feed(2);
    $this->ePrinter->text('UST-ID Nr : DE296 413 204');
    $this->ePrinter->feed(1);
    $this->ePrinter->text('Dami GmbH');
    $this->ePrinter->feed(2);
    if($barcode !== '') {
      $this->ePrinter->setBarcodeWidth(2);
      $this->ePrinter->setBarcodeHeight(81);
      $this->ePrinter->barcode($barcode, Printer::BARCODE_CODE93);
      $this->ePrinter->text(implode(' ',str_split($barcode)));
      $this->ePrinter->feed(1);
    }
    if($type == 'del') {
      $this->ePrinter->feed(4);
      $this->ePrinter->setJustification($this->aCenter);
      $this->ePrinter->text('_________________________');
      $this->ePrinter->feed(1);
      $this->ePrinter->text('Signiture');
      $this->ePrinter->feed(1);
    }
    $this->ePrinter->feed(2);
    $this->ePrinter->cut();
  }

  public function textPos($leftStr, $rightStr, $fontSize = 1) {
    $maxChar = (int)(42 / $fontSize);
    $emptySpace = $maxChar - strlen($leftStr) - strlen($rightStr);
    return str_repeat(' ', $emptySpace);
  }

  public function voucherOK($order, $copy = 'N') {
    try {
      for($i=0;$i<2;$i++) {
        if($i == 0) {
          $this->header('Accepted', '- For Shop -', $copy);
        }
        else {
          $this->header('Accepted', '- For Customer -', $copy);
        }

        $this->ePrinter->text('Kasse ID . '.$order->kasse_id);
        $this->ePrinter->feed(1);

        $this->ePrinter->text('Transaktions-Nr . '.$order->receipt_num);
        $this->ePrinter->feed(1);

        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);

        foreach($order->voucher as $voucher) {
          $UVP = (float)$voucher['amount'];
          $UVPStr = number_format($UVP, 2, ',', '.');
          $priceStr = number_format($UVP, 2, ',', '.');

          $itemName = 'Gutschein';
          $sumStr = '1 * '.$UVPStr;
          $this->ePrinter->text($itemName.$this->textPos($itemName, $UVPStr).$UVPStr);
          $this->ePrinter->feed(1);

          $this->ePrinter->setEmphasis(true);
          if((float)$voucher['discount_rate'] > 0) {
            $UVP = round((float)$voucher['amount'] * (1 - (float)$voucher['discount_rate']), 2);
            $sumStr .= ' * -'.round((float)$voucher['discount_rate'] * 100, 0).'%';
            $priceStr = number_format($UVP, 2, ',', '.');

            $this->ePrinter->text($sumStr.$this->textPos($sumStr, $priceStr).$priceStr);
            $this->ePrinter->feed(1);
          }
          else {
            $this->ePrinter->text($sumStr.$this->textPos($sumStr, $priceStr).$priceStr);
            $this->ePrinter->feed(1);
          }
          $this->ePrinter->setEmphasis(false);
        }

        $this->ePrinter->text('------------------------------------------');
        $this->ePrinter->feed(1);
        $this->ePrinter->setEmphasis(true);
        $this->ePrinter->setTextSize(2, 1);
        $this->ePrinter->text('Summe'.$this->textPos('Summe', number_format((float)$order->sales_price, 2, ',', '.').' EUR', 2).number_format((float)$order->sales_price, 2, ',', '.').' EUR');
        $this->ePrinter->feed(1);
        $this->ePrinter->setTextSize(1, 1);
        $this->ePrinter->setEmphasis(false);
        $this->ePrinter->feed(1);

        if(abs((float)$order->creditcard_amount) != 0) {
          $this->ePrinter->text('Credit Card: '.$this->textPos('Credit Card: ', number_format((float)$order->creditcard_amount, 2, ',', '.').' EUR').number_format((float)$order->creditcard_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs((float)$order->eccard_amount) != 0) {
          $this->ePrinter->text('EC Card: '.$this->textPos('EC Card: ', number_format((float)$order->eccard_amount, 2, ',', '.').' EUR').number_format((float)$order->eccard_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        if(abs((float)$order->cash_amount) != 0) {
          $this->ePrinter->text('BAR: '.$this->textPos('BAR: ', number_format((float)$order->cash_amount, 2, ',', '.').' EUR').number_format((float)$order->cash_amount, 2, ',', '.').' EUR');
          $this->ePrinter->feed(1);
        }
        $this->footer($order->receipt_num);
      }

      /* Close printer */
      $this->ePrinter->close();
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    return ['ok' => true];
  }
  public function cancelVoucher($order, $copy = 'N') {

  }
}
