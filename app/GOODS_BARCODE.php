<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods_Barcode extends Model {

  protected $connection = 'dami';
  /**
  * Primary key.
  *
  * @var string
  */
  protected $table = "GL_GOODS_BARCODES";

  protected $fillable = [];

  // Relationships

  public function goods() {
    return $this->belongsTo('App\GOODS', 'GOODS_CD', 'GOODS_CD');
  }

}
