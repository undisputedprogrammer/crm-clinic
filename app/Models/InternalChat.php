<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Ynotz\MediaManager\Traits\OwnsMedia;

class InternalChat extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;

    protected $guarded = [];

    protected $appends = [
        'chat_pics'
    ];

    public function getMediaStorage(): array
    {
        return [
            'chat_pics' => [
                'disk' => 'local',
                'folder' => '/images/chat_pics'
            ]
        ];
    }

    public function chatPics(): Attribute
    {
        return Attribute::make(
            get: function($val) {
                return $this->getAllMediaForDisplay('chat_pics');
            }
        );
    }
}
