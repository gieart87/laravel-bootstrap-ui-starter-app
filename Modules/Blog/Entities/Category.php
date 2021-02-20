<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Carbon\Carbon;

use App\Models\Concerns\UuidTrait;

class Category extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'parent_id',
        'slug',
        'name',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_categories';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected static function newFactory()
    {
        return \Modules\Blog\Database\factories\CategoryFactory::new();
    }

    public function children()
    {
        return $this->hasMany('Modules\Blog\Entities\Category', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('Modules\Blog\Entities\Category', 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany('Modules\Blog\Entities\Post', 'blog_categories_posts', 'post_id', 'category_id');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('d, M Y H:i:s');
    }
}
