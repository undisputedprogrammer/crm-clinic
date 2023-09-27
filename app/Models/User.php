<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ynotz\AccessControl\Traits\WithRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, WithRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'hospital_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'chat_room_ids'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function leads(){
        return $this->hasMany(Lead::class,'assigned_to','id');
    }

    public function centers(){
        return $this->belongsToMany(Center::class, 'user_has_centers');
    }

    public function hospital(){
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function chatRoomIds(): Attribute
    {
        return Attribute::make(
            get: function ($val) {
                $arr = [];
                array_push($arr, $this->id);
                array_push($arr, $this->hospital->chat_room_id);
                array_merge($arr, $this->centers->pluck('id')->toArray());
                return $arr;
            }
        );
    }
}
