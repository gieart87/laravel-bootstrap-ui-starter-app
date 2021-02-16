<?php

namespace Modules\Blog\Repositories\Admin;

use Modules\Blog\Repositories\Admin\Interfaces\TagRepositoryInterface;
use Modules\Blog\Entities\Tag;

class TagRepository implements TagRepositoryInterface
{
    public function findList()
    {
        return Tag::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    public function findById($tagId)
    {
    }
}
