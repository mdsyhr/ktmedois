<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    // Tell Laravel the password field name
    public function getAuthPasswordName()
    {
        return 'Password_Hash';
    }

    // Tell Laravel your email column for password reset
    public function getEmailForPasswordReset()
    {
        return $this->attributes['Email'];
    }

    // Email attribute accessor
    public function getEmailAttribute()
    {
        return $this->attributes['Email'];
    }

    // Disable remember token
    public function getRememberTokenName()
    {
        return null;
    }

    // Override password reset to use Password_Hash column
    public function setPasswordAttribute($value)
    {
        $this->attributes['Password_Hash'] = $value;
    }
}