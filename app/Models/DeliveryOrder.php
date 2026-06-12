<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $table = 'delivery_order';
    protected $primaryKey = 'DO_ID';
    public $timestamps = false;

    protected $fillable = [
        'Cust_ID',
        'Supplier_ID',
        'DO_Number',
        'PO_Number',
        'Staff_ID',
        'DO_Link',
        'Proof_Link',
        'Status',
        'Reason',
        'Created_Date'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'DO_ID', 'DO_ID');
    }
}
