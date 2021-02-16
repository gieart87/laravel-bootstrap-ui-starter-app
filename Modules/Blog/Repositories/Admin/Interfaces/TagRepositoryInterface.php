<?php

namespace Modules\Blog\Repositories\Admin\Interfaces;

interface TagRepositoryInterface
{
    public function findList();
    public function findById($tagId);
}
