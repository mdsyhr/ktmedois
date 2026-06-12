<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'Supplier_ID';
    public $timestamps = false;

    protected $fillable = [
        'User_ID',
        'Supplier_Name',
        'Billing_Address',
        'Vendor_Number',
        'Contact_Person',
        'Phone',
        'Email',
        'Status',
        'Inactive_Date',
    ];

    // A supplier belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'User_ID', 'User_ID');
    }
}