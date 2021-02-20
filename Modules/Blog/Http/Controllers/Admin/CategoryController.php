<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

use Modules\Blog\Http\Controllers\BlogController;
use Nwidart\Modules\Module;

use Modules\Blog\Http\Requests\Admin\CategoryRequest;
use Modules\Blog\Repositories\Admin\Interfaces\CategoryRepositoryInterface;

use App\Authorizable;

class CategoryController extends BlogController
{
    use Authorizable;

    private $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository) //phpcs:ignore
    {
        parent::__construct();
        $this->data['currentAdminMenu'] = 'categories';

        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $this->data['categories'] = $this->getCategories($request);
        $this->data['nestedCategories'] = $this->categoryRepository->findNestedList();

        return view('blog::admin.categories.index', $this->data);
    }

    private function getCategories($request = null)
    {
        $params = !empty($request) ? $request->all() : [];

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'name' => 'asc',
            ],
            'filter' => $params,
        ];

        return $this->categoryRepository->findAll($options);
    }


    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->data['nestedCategories'] = $this->categoryRepository->findNestedList();
        $this->data['categories'] = $this->getCategories();

        return view('blog::admin.categories.index', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CategoryRequest $request)
    {
        $params = $request->validated();

        if ($this->categoryRepository->create($params)) {
            return redirect('admin/blog/categories')
                ->with('success', __('blog::categories.success_create_message'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->findById($id);
        $this->data['category'] = $category;
        $this->data['nestedCategories'] = $this->categoryRepository->findNestedList($id);
        $this->data['categories'] = $this->getCategories();

        return view('blog::admin.categories.index', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CategoryRequest $request, $id)
    {
        $params = $request->validated();

        if ($this->categoryRepository->update($params, $id)) {
            return redirect('admin/blog/categories')
                ->with('success', __('blog::categories.success_update_message'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if ($this->categoryRepository->delete($id)) {
            return redirect('admin/blog/categories')
                ->with('success', __('blog::categories.success_delete_message'));
        }

        return redirect('admin/blog/categories')
            ->with('error', __('blog::categories.fail_delete_message'));
    }
}
