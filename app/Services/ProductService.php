<?php
namespace App\Services;

use Auth;
use DB;
use App\Product;

use App\GOODS;
use App\GOODS_BARCODE;

class ProductService {

  public function getProductWithBarcode($barcode) {
    return Product::where('ean', $barcode)
                          ->orWhere('ean2', $barcode)
                          ->orWhere('ean3', $barcode)
                          ->orWhere('ean4', $barcode)
                          ->orWhere('ean5', $barcode)
                          ->first();
  }

  public function findProduct($params) {
    $query = Product::with(['damiInvens', 'invens' => function($q) {
      $q->where('BRANCH_SQ', 'LOUISE');
    }]);
    if($params['option'] == 'code') {
      $query->where($params['option'], $params['keyword']);
    } else if ($params['option'] == 'name') {
      $query->where($params['option'], 'like', '%'.$params['keyword'].'%');
    } else if ($params['option'] == 'barcode') {
      $query->where('ean', $params['keyword'])
            ->orWhere('ean2', $params['keyword'])
            ->orWhere('ean3', $params['keyword'])
            ->orWhere('ean4', $params['keyword'])
            ->orWhere('ean5', $params['keyword']);
    }
    return $query->get();
  }

  public function findDamiProduct($params) {
    $str = "LOUISE";
    $query = DB::connection('dami')->table('GL_GOODS AS GINFO')
              ->select(DB::connection('dami')->raw(
                ' GINFO.GOODS_CD code,'.
                ' GINFO.GOODS_NM name,'.
                ' GINFO.GOODS_UVP UVP,'.
                ' GINFO.GOODS_UVP2 promo_UVP,'.
                ' GINFO.GOODS_tax_rate tax_rate,'.
                ' GINFO.GOODS_UNIT_PRICE avr_buy_price,'.
                ' GBARCODE.GOODS_BARCODE_NUM ean,'.
                ' GINVEN.BRANCH_SQ isLouise,'.
                ' GINVEN.BRANCH_INVEN_BAL_QUN qty'))
              ->leftjoin('GL_GOODS_BARCODES AS GBARCODE','GINFO.GOODS_CD', '=', 'GBARCODE.GOODS_CD')
              ->leftjoin('GL_BRANCH_INVENS AS GINVEN', 'GINFO.GOODS_CD', '=', 'GINVEN.GOODS_CD') #,'and', 'GINVEN.BRANCH_SQ', '=', $str);
              ->groupby('GINFO.GOODS_NM');
              #->leftjoin('GINVEN', 'GINVEN.GOODS_CD', '=', $str);
              #->groupby('GINVEN.BRANCH_SQ');
    if($params['option'] == 'barcode') {
      $query->where('GBARCODE.GOODS_BARCODE_NUM', $params['keyword']);
    }
    else if($params['option'] == 'name') {
      $query->where('GINFO.GOODS_NM', 'LIKE', '%'.$params['keyword'].'%');
    }
    $query->orderby('GINFO.GOODS_CD');
    #dd($query->get());
    return $query->get();
  }

  public function getProductWithProductCode($productCode) {
    return Product::where('code', $productCode)->first();
  }

  public function addNewProduct($data) {
    if(!empty(Product::where('code', $data['code'])->first())) {
      return ['ok' => false, 'msg' => 'Item Code is already exist'];
    }
    if(!empty(Product::where('ean', $data['barcode'])->first())) {
      return ['ok' => false, 'msg' => 'Barcode is already exist'];
    }
    try {
      DB::table('retail_products')->insert([
        'code' => $data['code'],
        'name' => $data['name'],
        'ean' => $data['barcode'],
        'UVP' => $data['uvp'],
        'tax_rate' => $data['tax'],
        'created_by' => Auth::user()->id,
        'created_at' => date('Y-m-d H:i:s')
      ]);
    } catch(\Exception $e) {
      return ['ok' => false, 'msg' => 'Fail to insert Item', 'err' => $e->getMessage()];
    }
    return ['ok' => true];
  }

  public function syncProduct() {
    $products = [];
    $goods = Goods::with('barcode')->get();
    $idx = 0;

    foreach($goods as $good) {
      if($good['GOODS_DIVISION_CD'] == 'COUPON')
      {
        $products[$idx] = [
          'id' => $good['GOODS_SQ'],
          'category' => $good['GOODS_DIVISION_CD'],
          'code' => $good['GOODS_CD'],
          'name' => $good['GOODS_NM'],
          'UVP' => $good['GOODS_UVP'],
          'promo_UVP' => $good['GOODS_UVP2'],
          'is_discountable' => 'N',
          'tax_rate' => $good['GOODS_TAX_RATE'],
          'article_num' => $good['GOODS_PRODUCT_CD'],
          'avr_buy_price' => $good['GOODS_AVR_PRICE'],
          'ean' => NULL,
          'ean2' => NULL,
          'ean3' => NULL,
          'ean4' => NULL,
          'ean5' => NULL,
          'ean_box' => NULL,
          'ean_box2' => NULL,
          'ean_box3' => NULL,
          'ean_box4' => NULL,
          'ean_box5' => NULL,
          'created_at' => date('Y-m-d H:i:s'),
          'created_by' => 9999
        ];
      }
      else
      {
        $products[$idx] = [
          'id' => $good['GOODS_SQ'],
          'category' => $good['GOODS_DIVISION_CD'],
          'code' => $good['GOODS_CD'],
          'name' => $good['GOODS_NM'],
          'UVP' => $good['GOODS_UVP'],
          'promo_UVP' => $good['GOODS_UVP2'],
          'is_discountable' => 'Y',
          'tax_rate' => $good['GOODS_TAX_RATE'],
          'article_num' => $good['GOODS_PRODUCT_CD'],
          'avr_buy_price' => $good['GOODS_AVR_PRICE'],
          'ean' => NULL,
          'ean2' => NULL,
          'ean3' => NULL,
          'ean4' => NULL,
          'ean5' => NULL,
          'ean_box' => NULL,
          'ean_box2' => NULL,
          'ean_box3' => NULL,
          'ean_box4' => NULL,
          'ean_box5' => NULL,
          'created_at' => date('Y-m-d H:i:s'),
          'created_by' => 9999
        ];
      }

      if(!empty($good['barcode'])) {
        foreach($good['barcode'] as $barcode) {
          if($barcode['GOODS_BARCODE_CD'] == 100) {
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 1) {
                $products[$idx]['ean'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 2) {
                $products[$idx]['ean2'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 3) {
                $products[$idx]['ean3'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 4) {
                $products[$idx]['ean4'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 5) {
                $products[$idx]['ean5'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
          }
          else if ($barcode['GOODS_BARCODE_CD'] == 200) {
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 1) {
                $products[$idx]['ean_box'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 2) {
                $products[$idx]['ean_box2'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 3) {
                $products[$idx]['ean_box3'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 4) {
                $products[$idx]['ean_box4'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
            if($barcode['GOODS_BARCODE_ORDER_NUM'] == 5) {
                $products[$idx]['ean_box5'] = str_replace('-', '', $barcode['GOODS_BARCODE_NUM']);
            }
          }
        }
      }
      $idx++;
    }

    DB::beginTransaction();
    try {
      DB::table('retail_products')->delete();
      foreach (array_chunk($products,1000) as $p) {
        DB::table('retail_products')->insert($p);
      }
      DB::commit();
    } catch(\Exception $e) {
      DB::rollback();
      return ['ok' => false, 'msg' => $e->getMessage()];
    }
    return ['ok' => true];
  }

  public function listProduct($params) {
    $query = DB::table('cafe_menus')->where('is_delete','N');
    $data = $query->paginate(30);
    return $data->appends($params);
  }

  // 2018.11.12 WonkyoungLee
  public function getCouponInfo($code) {
    $query = DB::table('VOUCHER_INFO')->where('code', $code)->get();
    
    // return $query->get();
    return ['ok' => count($query) > 0 ? true : false, 'couponInfo' => $query];
  }
}
