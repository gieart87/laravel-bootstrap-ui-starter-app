<?php

namespace Modules\Blog\Repositories\Admin\Interfaces;

interface CategoryRepositoryInterface
{
    /**
     * Paginated collection
     *
     * @param int $perPage per page items
     *
     * @return Collection
     */
    public function paginate(int $perPage);

    /**
     * Find single record by id
     *
     * @param int $categoryId category id
     *
     * @return Category
     */
    public function findById(int $categoryId);

    /**
     * Get categories as dropdown
     *
     * @param int $exceptCategoryId except category id
     *
     * @return array
     */
    public function findList(int $exceptCategoryId = null);

    public function findNestedList();

    public function findParentCategories();

    /**
     * Create new record
     *
     * @param array $params request params
     *
     * @return Category
     */
    public function create($params);

    /**
     * Update existing record
     *
     * @param array $params     request params
     * @param int   $categoryId category id
     *
     * @return Category
     */
    public function update($params, int $categoryId);

    /**
     * Delete a record
     *
     * @param int $categoryId category id
     *
     * @return boolean
     */
    public function delete(int $categoryId);
}
