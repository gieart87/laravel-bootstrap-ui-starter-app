<?php

namespace Modules\Blog\Repositories\Admin;

use Facades\Str;
use DB;

use Modules\Blog\Repositories\Admin\Interfaces\PageRepositoryInterface;
use Modules\Blog\Entities\Post;

class PageRepository implements PageRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $pages = (new Post())->with('user')
            ->where('post_type', Post::PAGE);

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $pages = $pages->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['q'])) {
            $pages = $pages->where(function ($query) use ($options) {
                $query->where('title', 'LIKE', "%{$options['filter']['q']}%")
                    ->orWhere('body', 'LIKE', "%{$options['filter']['q']}%");
            });
        }

        if (!empty($options['filter']['status'])) {
            $pages = $pages->where('status', $options['filter']['status']);
        }

        if ($perPage) {
            return $pages->paginate($perPage);
        }
        
        return $pages->get();
    }

    public function findAllInTrash($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $pages = (new Post())->onlyTrashed()->with('user')
            ->where('post_type', Post::PAGE);

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $pages = $pages->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['q'])) {
            $pages = $pages->where(function ($query) use ($options) {
                $query->where('title', 'LIKE', "%{$options['filter']['q']}%")
                    ->orWhere('body', 'LIKE', "%{$options['filter']['q']}%");
            });
        }

        if (!empty($options['filter']['status'])) {
            $pages = $pages->where('status', $options['filter']['status']);
        }

        if ($perPage) {
            return $pages->paginate($perPage);
        }

        return $pages->get();
    }

    public function findById($id)
    {
        return Post::where('post_type', Post::PAGE)
            ->where('id', $id)
            ->firstOrFail();
    }

    public function create($params = [])
    {
        $params['user_id'] = auth()->user()->id;
        $params['post_type'] = Post::PAGE;
        $params['slug'] = Str::slug($params['title']);
        $params['code'] = $this->generateCode();

        return DB::transaction(function () use ($params) {
            if ($page = Post::create($params)) {
                return $page;
            }
        });
    }

    /**
     * Generate order code
     *
     * @return string
     */
    public static function generateCode()
    {
        $pageCode = Str::random(12);

        if (self::isCodeExists($pageCode)) {
            return generateOrderCode();
        }

        return $pageCode;
    }

    /**
     * Check if the generated order code is exists
     *
     * @param string $orderCode order code
     *
     * @return void
     */
    private static function isCodeExists($pageCode)
    {
        return Post::where('code', '=', $pageCode)->exists();
    }

    public function update(Post $page, $params = [])
    {
        return DB::transaction(function () use ($page, $params) {
            return $page->update($params);
        });
    }

    public function delete($id, $permanentDelete = false)
    {
        $page  = Post::withTrashed()
            ->where('post_type', Post::PAGE)
            ->where('id', $id)
            ->firstOrFail();
        
        return DB::transaction(function () use ($page, $permanentDelete) {
            if ($permanentDelete) {
                return $page->forceDelete();
            }

            return $page->delete();
        });
    }

    public function restore($id)
    {
        $page  = Post::withTrashed()
            ->where('post_type', Post::PAGE)
            ->where('id', $id)
            ->firstOrFail();

        if ($page->trashed()) {
            return $page->restore();
        }

        return false;
    }

    public function getStatuses()
    {
        return Post::STATUSES;
    }
}
