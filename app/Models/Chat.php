<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    protected $guarded = [];
    protected $hidden = [];

    public function messages(){
        return $this->hasMany(Message::class , 'chat_id' , 'id' );
    }
    public function user(){
        return $this->belongsTo(User::class , 'user_id' , 'id' );
    }
//    public function clinic(){
//        return $this->belongsTo(User::class , 'clinic_id' , 'id' );
//    }
}
