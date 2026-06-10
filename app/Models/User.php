<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'User_ID';
    public $timestamps = false;

    protected $fillable = [
        'Username',
        'Password_Hash',
        'Role',
        'Email',
        'Status',
        'Last_Login',
    ];

    protected $hidden = [
        'Password_Hash',
    ];

    // Tell Laravel your username column
    public function getAuthIdentifierName()
    {
        return 'Username';
    }

    // Tell Laravel your password column
    public function getAuthPassword()
    {
        return $this->Password_Hash;
    }

    // Tell Laravel the password field name for Auth::attempt
    public function getAuthPasswordName()
    {
        return 'Password_Hash';
    }
}