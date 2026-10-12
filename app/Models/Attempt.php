<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    public function test()
    {
        return $this->belongsTo(Test::class,'test_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function raw_result()
    {
        return $this->hasOne(RawResult::class,'attempt_id');
    }
}
