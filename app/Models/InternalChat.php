<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Ynotz\MediaManager\Traits\OwnsMedia;

class InternalChat extends Model implements MediaOwner
{
    use HasFactory, OwnsMedia;


}
