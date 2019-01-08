<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $connection = 'local';
  /**
  * Primary key.
  *
  * @var bigint
  */
  protected $table = "retail_products";

  protected $fillable = [
    'id', 'category', 'code', 'name', 'UVP', 'promo_UVP', 'tax_rate', 'article_num',
    'ean', 'ean2', 'ean3', 'ean4', 'ean5',
    'ean_box', 'ean_box2', 'ean_box3', 'ean_box4', 'ean_box5', 'created_at', 'created_by'
  ];

  public function orderDetail() {
    return $this->hasMany('App\Order_Detail', 'item_code', 'item_code');
  }

  public function invens() {
    return $this->hasOne('App\Branch_Invens', 'GOODS_CD', 'code');
  }

  public function damiInvens() {
    return $this->hasOne('App\Goods_Invens', 'GOODS_CD', 'code');
  }
}
