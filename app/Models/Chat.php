<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['message','direction','lead_id','status','wamid','template_id'];

    public function template() {
        return $this->belongsTo(Message::class,'template_id','id');
    }
}
