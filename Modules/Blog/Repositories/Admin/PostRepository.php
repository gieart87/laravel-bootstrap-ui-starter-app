<?php

namespace Modules\Blog\Repositories\Admin;

use Facades\Str;
use DB;

use Modules\Blog\Repositories\Admin\Interfaces\PostRepositoryInterface;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\Tag;

class PostRepository implements PostRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $posts = (new Post())->with('user');

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $posts = $posts->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['q'])) {
            $posts = $posts->where(function ($query) use ($options) {
                $query->where('title', 'LIKE', "%{$options['filter']['q']}%")
                    ->orWhere('body', 'LIKE', "%{$options['filter']['q']}%");
            });
        }

        if (!empty($options['filter']['status'])) {
            $posts = $posts->where('status', $options['filter']['status']);
        }

        if ($perPage) {
            return $posts->paginate($perPage);
        }
        
        return $posts->get();
    }

    public function findAllInTrash($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $posts = (new Post())->onlyTrashed()->with('user');

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $posts = $posts->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['q'])) {
            $posts = $posts->where(function ($query) use ($options) {
                $query->where('title', 'LIKE', "%{$options['filter']['q']}%")
                    ->orWhere('body', 'LIKE', "%{$options['filter']['q']}%");
            });
        }

        if (!empty($options['filter']['status'])) {
            $posts = $posts->where('status', $options['filter']['status']);
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
        $params['user_id'] = auth()->user()->id;
        $params['post_type'] = Post::POST;
        $params['slug'] = Str::slug($params['title']);
        $params['code'] = $this->generateCode();
        $params = array_merge($params, $this->buildMetaParams($params));

        return DB::transaction(function () use ($params) {
            if ($post = Post::create($params)) {
                $this->setFeaturedImage($post, $params);
                $this->syncCategories($post, $params);
                $this->syncTags($post, $params);

                return $post;
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
        $postCode = Str::random(12);

        if (self::isCodeExists($postCode)) {
            return generateOrderCode();
        }

        return $postCode;
    }

    /**
     * Check if the generated order code is exists
     *
     * @param string $orderCode order code
     *
     * @return void
     */
    private static function isCodeExists($postCode)
    {
        return Post::where('code', '=', $postCode)->exists();
    }

    public function update(Post $post, $params = [])
    {
        $params = array_merge($params, $this->buildMetaParams($params));

        return DB::transaction(function () use ($post, $params) {
            $this->setFeaturedImage($post, $params);
            $this->syncCategories($post, $params);
            $this->syncTags($post, $params);
           
            return $post->update($params);
        });
    }

    private function setFeaturedImage($post, $params)
    {
        if (isset($params['image'])) {
            $post->clearMediaCollection('images');

            $post->addMediaFromRequest('image')->toMediaCollection('images');
            $post->featured_image = $post->getFirstMedia('images')->getUrl();
            $post->featured_image_caption = $post->getFirstMedia('images')->name;

            $post->save();
        }
    }

    private function syncCategories($post, $params)
    {
        $categoryIds = (isset($params['categories'])) ? $params['categories'] : [];
        $post->categories()->sync($categoryIds);
    }

    private function syncTags($post, $params)
    {
        if (isset($params['tags'])) {
            $tagIds = [];

            foreach ($params['tags'] as $tag) {
                if (!Str::isUuid($tag)) {
                    $newTag = Tag::firstOrCreate(['name' => $tag, 'slug' => Str::slug($tag)]);
                    $tagIds[] = $newTag->id;
                } else {
                    $tagIds[] = $tag;
                }
            }

            $post->tags()->sync($tagIds);
        }
    }

    private function buildMetaParams($params)
    {
        $metaParams = [];
        foreach (Post::META_FIELDS as $metaField => $metaFieldAttr) {
            if (!empty($params[$metaField])) {
                $metaParams[$metaField] = $params[$metaField];
            }
        }

        $params['metas'] = $metaParams;

        return $params;
    }

    public function delete($id, $permanentDelete = false)
    {
        $post  = Post::withTrashed()->findOrFail($id);
        $this->checkUserCanDeletePost($post);

        return DB::transaction(function () use ($post, $permanentDelete) {
            if ($permanentDelete) {
                $post->tags()->sync([]);
                $post->categories()->sync([]);

                return $post->forceDelete();
            }

            return $post->delete();
        });
    }

    private function checkUserCanDeletePost($post)
    {
        $currentUser = auth()->user();
        $canDeletePost = $currentUser->hasRole('admin') || ($post->user_id == $currentUser->id);

        if ($canDeletePost) {
            return;
        }

        abort(403, __('blog::posts.fail_delete_message'));
    }

    public function restore($id)
    {
        $post  = Post::withTrashed()->findOrFail($id);
        if ($post->trashed()) {
            return $post->restore();
        }

        return false;
    }

    public function getStatuses()
    {
        return Post::STATUSES;
    }

    public function getMetaFields()
    {
        return Post::META_FIELDS;
    }
}
