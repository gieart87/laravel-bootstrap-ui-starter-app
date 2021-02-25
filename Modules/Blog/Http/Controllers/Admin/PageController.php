<?php

namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

use Modules\Blog\Http\Controllers\BlogController;
use Nwidart\Modules\Module;
use Modules\Blog\Http\Requests\Admin\PostRequest;

use Modules\Blog\Repositories\Admin\Interfaces\PageRepositoryInterface;

use App\Authorizable;

class PageController extends BlogController
{
    use Authorizable;

    private $pageRepository;
    private $categoryRepository;
    private $tagRepository;

    public function __construct(PageRepositoryInterface $pageRepository) //phpcs:ignore
    {
        parent::__construct();
        $this->data['currentAdminMenu'] = 'pages';

        $this->pageRepository = $pageRepository;

        $this->data['statuses'] = $this->pageRepository->getStatuses();
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

        $this->data['pages'] = $this->pageRepository->findAll($options);
        $this->data['filter'] = $params;

        return view('blog::admin.pages.index', $this->data);
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
        $this->data['pages'] = $this->pageRepository->findAllInTrash($options);
        $this->data['filter'] = $params;
        return view('blog::admin.pages.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('blog::admin.pages.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PostRequest $request)
    {
        $params = $request->validated();

        if ($page = $this->pageRepository->create($params)) {
            return redirect('admin/blog/pages/'. $page->id .'/edit')
                ->with('success', __('blog::pages.success_create_message'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $page = $this->pageRepository->findById($id);

        $this->data['page'] = $page;

        return view('blog::admin.pages.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(PostRequest $request, $id)
    {
        $page = $this->pageRepository->findById($id);
        $params = $request->validated();

        if ($this->pageRepository->update($page, $params)) {
            return redirect('admin/blog/pages/'. $id .'/edit')
                ->with('success', __('blog::pages.success_update_message'));
        }

        return redirect('admin/blog/pages/'. $id .'/edit')
            ->with('error', __('blog::pages.fail_update_message'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        $permanentDelete = (bool)$request->get('_permanent_delete');

        if ($this->pageRepository->delete($id, $permanentDelete)) {
            if ($permanentDelete) {
                return redirect('admin/blog/pages/trashed')->with('success', __('blog::pages.success_delete_message'));
            }

            return redirect('admin/blog/pages')->with('success', __('blog::pages.success_delete_message'));
        }
    
        return redirect('admin/blog/pages')->with('error', __('blog::pages.fail_delete_message'));
    }

    public function restore($id)
    {
        if ($this->pageRepository->restore($id)) {
            return redirect('admin/blog/pages/trashed')->with('success', __('blog::pages.success_restore_message'));
        }

        return redirect('admin/blog/pages/trashed')->with('error', __('blog::pages.fail_restore_message'));
    }
}
