<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch_Invens extends Model {

  protected $connection = 'dami';
  /**
  * Primary key.
  *
  * @var string
  */
  protected $table = "GL_BRANCH_INVENS";

  protected $fillable = [];

  // Relationships
  public function product() {
    return $this->belongsTo('App\Product', 'GOODS_CD', 'code');
  }

}
