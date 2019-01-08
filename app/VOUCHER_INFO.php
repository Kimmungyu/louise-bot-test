<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VOUCHER_INFO extends Model
{
  /**
  * Primary key.
  *
  * @var string
  */
  protected $table = "VOUCHER_INFO";

  protected $fillable = [
    'id', 'code', 'type', 'amount', 'use_condition', 'available'
  ];
}
