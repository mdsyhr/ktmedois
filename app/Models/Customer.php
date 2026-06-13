<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'Cust_ID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'User_ID',
        'Cust_Name',
        'Cust_Address',
        'Contact',
        'Cust_Info',
    ];
}
