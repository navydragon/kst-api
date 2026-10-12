<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    public function user_attempts($user_id)
    {
        return $this->hasMany(Attempt::class)->where('user_id',$user_id)->get();
    }
}
