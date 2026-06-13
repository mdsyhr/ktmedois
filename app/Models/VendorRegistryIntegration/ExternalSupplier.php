<?php

namespace App\Models\VendorRegistryIntegration;

use Illuminate\Database\Eloquent\Model;

class ExternalSupplier extends Model
{
    // Use the external database connection
    protected $connection = 'mysql_external';
    protected $table = 'supplier';
    protected $primaryKey = 'SUPPLIERID';
    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'SUPPLIERID',
        'SUPPLIER_COMP_REG_NO',
        'SUPPLIER_COMP_NAME',
        'SUPPLIER_CTC_NO',
        'SUPPLIER_CTC_PERSON',
        'SUPPLIER_EMAIL_ADD',
        'SUPPLIER_EXPIRED_DATE',
        'SUPPLIER_CTC_STATUS',
    ];
}