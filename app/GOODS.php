<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model {

  protected $connection = 'dami';
  /**
  * Primary key.
  *
  * @var string
  */
  protected $table = "GL_GOODS";

  // Relationships
  public function barcode() {
    return $this->hasMany('App\GOODS_BARCODE', 'GOODS_CD', 'GOODS_CD');
  }
}
