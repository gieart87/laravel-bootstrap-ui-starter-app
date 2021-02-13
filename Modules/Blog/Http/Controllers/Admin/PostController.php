<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
// use Illuminate\Routing\Controller;

use Modules\Blog\Http\Controllers\BlogController;
use Nwidart\Modules\Module;
use Modules\Blog\Http\Requests\PostRequest;

use Modules\Blog\Repositories\Admin\Interfaces\PostRepositoryInterface;
use Modules\Blog\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use Modules\Blog\Repositories\Admin\Interfaces\TagRepositoryInterface;

class PostController extends BlogController
{
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
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'created_at' => 'desc',
            ]
        ];

        $this->data['posts'] = $this->postRepository->findAll($options);
        return view('blog::admin.posts.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('blog::admin.posts.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PostRequest $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('blog::show');
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
        dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
