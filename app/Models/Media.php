<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

use App\Models\Concerns\UuidTrait;

class Media extends BaseMedia
{
    use HasFactory, UuidTrait;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
}
