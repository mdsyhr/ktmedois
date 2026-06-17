<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'Notification_ID';
    public $timestamps = false;

    protected $fillable = [
        'User_ID',
        'Type',
        'Content',
        'Status',
        'Created_At',
    ];
}