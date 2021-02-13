<?php

namespace Modules\Blog\Repositories\Admin;

use Modules\Blog\Repositories\Admin\Interfaces\PostRepositoryInterface;
use Modules\Blog\Entities\Post;

class PostRepository implements PostRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $posts = new Post();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $posts = $posts->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $posts->paginate($perPage);
        }
        
        return $posts->get();
    }

    public function findById($id)
    {
        return Post::findOrFail($id);
    }

    public function create($params = [])
    {
        $params['guard_name'] = 'web';

        return DB::transaction(function () use ($params) {
            if ($post = Post::create($params)) {
                $permissions = !empty($params['permissions']) ? $params['permissions'] : [];
                $post->syncPermissions($permissions);
    
                return $post;
            }
        });
    }

    public function update($id, $params = [])
    {
        $post = Post::findOrFail($id);

        if ($post->name == Post::ADMIN) {
            return true;
        }

        return DB::transaction(function () use ($params, $post) {
            $permissions = !empty($params['permissions']) ? $params['permissions'] : [];
            $post->syncPermissions($permissions);
        
            return $post->update($params);
        });
    }

    public function delete($id)
    {
        $post  = Post::findOrFail($id);

        if ($post->name == Post::ADMIN) {
            return false;
        }

        return $post->delete();
    }
}
