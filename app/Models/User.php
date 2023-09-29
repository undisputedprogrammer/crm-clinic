<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;


use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Ynotz\MediaManager\Traits\OwnsMedia;
use Ynotz\AccessControl\Traits\WithRoles;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MediaOwner
{
    use HasApiTokens, HasFactory, Notifiable, WithRoles, OwnsMedia;

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
        'chat_room_ids','user_picture'
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



    public function getMediaStorage(): array
    {
        return ['user_picture' => [
            'disc' => 'local',
            'folder' => '/images/user_picture'
        ]];
    }

    public function userPicture(): Attribute
    {
        return Attribute::make(
            get: function(){
                return $this->getSingleMediaForDisplay('user_picture');
            }
        );
    }

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
                array_merge($arr, $this->centers->pluck('chat_room_id')->toArray());
                return $arr;
            }
        );
    }
}
