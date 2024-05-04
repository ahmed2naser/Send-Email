<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'code',
        'time_code',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function generateCode()
    {
        $this->timestamps = false;
        $this->code = rand(1000, 9999);
        $this->time_code = now()->addMinute(10);
        $this->save();
    }

    public function resetCode()
    {
        $this->timestamps = false;
        $this->code = null;
        $this->time_code = null;
        $this->save();
    }
}
