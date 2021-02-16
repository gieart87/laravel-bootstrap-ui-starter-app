<?php

namespace Modules\Blog\Repositories\Admin;

use Modules\Blog\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Modules\Blog\Entities\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Paginated collection
     *
     * @param int $perPage per page items
     *
     * @return Collection
     */
    public function paginate($perPage)
    {
        return Category::orderBy('name', 'ASC')->paginate($perPage);
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

    public function findNestedList()
    {
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->get();

        $nestedCategories = [];
        foreach ($categories as $category) {
            $nestedCategories[$category->id] = $category->name;
            $nestedCategories = array_merge($nestedCategories, $this->getChildren($category, 0));
        }

        return $nestedCategories;
    }

    private function getChildren($category, $level = 0)
    {
        $nestedCategories = [];
        if ($category->children->count()) {
            $level++;
            foreach ($category->children as $child) {
                $nestedCategories[$child->id] = str_repeat('-', $level * 2) . ' ' . $child->name;
                $nestedCategories = array_merge($nestedCategories, $this->getChildren($child, $level));
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
