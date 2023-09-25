<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCenter extends Model
{
    use HasFactory;

    public $table = 'user_has_centers';

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function center(){
        return $this->hasOne(Center::class, 'id','center_id');
    }
}
