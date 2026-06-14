<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'Invoice_ID';
    public $timestamps = false;

    protected $fillable = [
        'Invoice_Num', 'Description', 'DO_ID', 'Issue_Date', 
        'Subtotal', 'Tax', 'Credit_Note', 'Total', 'Status', 'Reason', 'Created_At'
    ];

    public function items()
    {
        return $this->hasMany(ItemDetail::class, 'Invoice_ID', 'Invoice_ID');
    }

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'DO_ID', 'DO_ID');
    }
}