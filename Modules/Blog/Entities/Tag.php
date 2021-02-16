<?php

namespace Modules\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\Concerns\UuidTrait;

class Tag extends Model
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
    protected $table = 'blog_tags';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected static function newFactory()
    {
        return \Modules\Blog\Database\factories\TagFactory::new();
    }

    public function posts()
    {
        return $this->belongsToMany('Modules\Blog\Entities\Post', 'blog_posts_tags', 'tag_id', 'post_id');
    }
}
