<?php

namespace Modules\Blog\Repositories\Admin;

use Illuminate\Support\Str;

use Modules\Blog\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Modules\Blog\Entities\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
     /**
     * Category collection
     *
     * @param $options conditions and sort
     *
     * @return Collection
     */
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $categories = (new Category())->with('parent');

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $categories = $categories->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['q'])) {
            $categories = $categories->where(function ($query) use ($options) {
                $query->where('name', 'LIKE', "%{$options['filter']['q']}%");
            });
        }

        if ($perPage) {
            return $categories->paginate($perPage);
        }
        
        return $categories->get();
    }

    /**
     * Find single record by id
     *
     * @param int $categoryId category id
     *
     * @return Category
     */
    public function findById($categoryId)
    {
        return Category::findOrFail($categoryId);
    }

    /**
     * Get categories as dropdown
     *
     * @param int $exceptCategoryId except category id
     *
     * @return array
     */
    public function findList($exceptCategoryId = null)
    {
        $categories = new Category;
        
        if ($exceptCategoryId) {
            $categories = $categories->where('id', '!=', $exceptCategoryId);
        }

        $categories = $categories->orderBy('name', 'asc');

        return $categories->pluck('name', 'id');
    }

    public function findNestedList($exceptCategoryId = null)
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $exceptCategoryId)
            ->orderBy('name', 'asc')->get();

        $nestedCategories = [];
        foreach ($categories as $category) {
            $nestedCategories[$category->id] = $category->name;
            $nestedCategories = array_merge($nestedCategories, $this->getChildren($category, 0, $exceptCategoryId));
        }

        return $nestedCategories;
    }

    private function getChildren($category, $level = 0, $exceptCategoryId = null)
    {
        $nestedCategories = [];
        if ($category->children->count()) {
            $level++;
            foreach ($category->children as $child) {
                if ($child->id != $exceptCategoryId) {
                    $nestedCategories[$child->id] = str_repeat('-', $level * 2) . ' ' . $child->name;
                    $nestedCategories = array_merge($nestedCategories, $this->getChildren($child, $level));
                }
            }
        }
       
        return $nestedCategories;
    }

    public function findParentCategories()
    {
        return Category::with('children')->whereNull('parent_id')->orderBy('name', 'asc')->get();
    }

    /**
     * Create new record
     *
     * @param array $params request params
     *
     * @return Category
     */
    public function create($params)
    {
        $params['slug'] = isset($params['name']) ? Str::slug($params['name']) : null;
        
        if (!isset($params['parent_id'])) {
            $params['parent_id'] = null;
        }

        return Category::create($params);
    }

    /**
     * Update existing record
     *
     * @param array $params request params
     * @param int   $id     category id
     *
     * @return Category
     */
    public function update($params, $id)
    {
        $params['slug'] = isset($params['name']) ? Str::slug($params['name']) : null;
        
        if (!isset($params['parent_id'])) {
            $params['parent_id'] = null;
        }

        $category = Category::findOrFail($id);

        return $category->update($params);
    }

    /**
     * Delete a record
     *
     * @param int $categoryId category id
     *
     * @return boolean
     */
    public function delete($categoryId)
    {
        $category  = Category::findOrFail($categoryId);

        return $category->delete();
    }
}
