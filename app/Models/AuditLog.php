<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $primaryKey = 'Log_ID';
    public $timestamps = false;

    protected $fillable = ['User_ID', 'Action', 'Affected_Record', 'Timestamp'];
}