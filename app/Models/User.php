<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = ['name','email','password'];

    protected $hidden = ['password', 'remember_token'];

    protected static function booted(){
        static::creating(function($model){
            if (!$model->getKey()){
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function contacts() {
        return $this->hasMany(Contact::class, "user_id");
    }
}
