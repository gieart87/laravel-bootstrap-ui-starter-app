<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

use Modules\Blog\Http\Controllers\BlogController;
use Nwidart\Modules\Module;
use Modules\Blog\Http\Requests\Admin\PostRequest;

use Modules\Blog\Repositories\Admin\Interfaces\PostRepositoryInterface;
use Modules\Blog\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Modules\Blog\Repositories\Admin\Interfaces\TagRepositoryInterface;

use App\Authorizable;

class PostController extends BlogController
{
    use Authorizable;

    private $postRepository;
    private $categoryRepository;
    private $tagRepository;

    public function __construct(PostRepositoryInterface $postRepository, CategoryRepositoryInterface $categoryRepository, TagRepositoryInterface $tagRepository) //phpcs:ignore
    {
        parent::__construct();
        $this->data['currentAdminMenu'] = 'posts';

        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;

        $this->data['categories'] = $this->categoryRepository->findList();
        $this->data['statuses'] = $this->postRepository->getStatuses();
        $this->data['metaFields'] = $this->postRepository->getMetaFields();
        $this->data['viewTrash'] = false;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'created_at' => 'desc',
            ],
            'filter' => $params,
        ];

        $this->data['posts'] = $this->postRepository->findAll($options);
        $this->data['filter'] = $params;

        return view('blog::admin.posts.index', $this->data);
    }

    public function trashed(Request $request)
    {
        $params = $request->all();

        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'created_at' => 'desc',
            ],
            'filter' => $params,
        ];

        $this->data['viewTrash'] = true;
        $this->data['posts'] = $this->postRepository->findAllInTrash($options);
        $this->data['filter'] = $params;
        return view('blog::admin.posts.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->data['tags'] = $this->tagRepository->findList();
        $this->data['categories'] = $this->categoryRepository->findParentCategories();

        return view('blog::admin.posts.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PostRequest $request)
    {
        $params = $request->validated();

        if ($post = $this->postRepository->create($params)) {
            return redirect('admin/blog/posts/'. $post->id .'/edit')
                ->with('success', __('blog::posts.success_create_message'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $post = $this->postRepository->findById($id);

        $this->data['post'] = $post;
        $this->data['tags'] = $this->tagRepository->findList();
        $this->data['tagIds'] = $post->tags()->allRelatedIds();
        $this->data['categories'] = $this->categoryRepository->findParentCategories();
        $this->data['categoryIds'] = $post->categories()->allRelatedIds()->toArray();

        return view('blog::admin.posts.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(PostRequest $request, $id)
    {
        $post = $this->postRepository->findById($id);
        $params = $request->validated();

        if ($this->postRepository->update($post, $params)) {
            return redirect('admin/blog/posts/'. $id .'/edit')
                ->with('success', __('blog::posts.success_update_message'));
        }

        return redirect('admin/blog/posts/'. $id .'/edit')
            ->with('error', __('blog::posts.fail_update_message'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        $permanentDelete = (bool)$request->get('_permanent_delete');

        if ($this->postRepository->delete($id, $permanentDelete)) {
            if ($permanentDelete) {
                return redirect('admin/blog/posts/trashed')->with('success', __('blog::posts.success_delete_message'));
            }

            return redirect('admin/blog/posts')->with('success', __('blog::posts.success_delete_message'));
        }
    
        return redirect('admin/blog/posts')->with('error', __('blog::posts.fail_delete_message'));
    }

    public function restore($id)
    {
        if ($this->postRepository->restore($id)) {
            return redirect('admin/blog/posts/trashed')->with('success', __('blog::posts.success_restore_message'));
        }

        return redirect('admin/blog/posts/trashed')->with('error', __('blog::posts.fail_restore_message'));
    }
}
