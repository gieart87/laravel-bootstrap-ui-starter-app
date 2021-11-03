<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Carbon\Carbon;

use Spatie\Permission\Traits\HasRoles;
use App\Models\Concerns\UuidTrait;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UuidTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d, M Y H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('d, M Y H:i:s');
    }

    public function getVerifiedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['email_verified_at'])->format('d, M Y H:i:s');
    }

    public function getShowEditRemoveBtnAttribute()
    {
        if (($this->id == auth()->user()->id) or $this->hasRole(\App\Models\Role::ADMIN)) {
            return false;
        }

        return true;
    }
}
