<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Concerns\UuidTrait;

class Role extends SpatieRole
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'id',
        'name',
        'guard_name',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    const ADMIN = 'Admin';

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d, M Y H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('d, M Y H:i:s');
    }
}
