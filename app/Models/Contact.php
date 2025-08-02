<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = ["id", "user_id", "first_name", "last_name", "phone", "email", "address", "dob", "notes", "started"];

    protected static function booted(){
        static::creating(function($model){
            if (!$model->getKey()){
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user () {
        return $this->belongsTo(User::class, "user_id");
    }  
}
