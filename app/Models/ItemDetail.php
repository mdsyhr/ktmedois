<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemDetail extends Model
{
    protected $table = 'item_details';
    protected $primaryKey = 'Details_ID';
    public $timestamps = false;

    protected $fillable = ['Invoice_ID', 'Item_Desc', 'Quantity', 'Unit_Price'];
}