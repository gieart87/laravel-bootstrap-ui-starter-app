<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserRequest;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Admin\Interfaces\UserRepositoryInterface;

use App\Authorizable;

class UserController extends Controller
{
    use Authorizable;

    private $roleRepository;
    private $permissionRepository;
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository, PermissionRepositoryInterface $permissionRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;

        $this->data['currentAdminMenu'] = 'users';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

        if (!empty($params['start_date']) && !empty($params['end_date'])) {
            $startDate = Carbon::parse($params['start_date']);
            $endDate = Carbon::parse($params['end_date']);

            if ($endDate < $startDate) {
                return redirect('admin/users')->with('error', __('general.invalid_date_range'));
            }
        }

        $this->data['filter'] = $params;

        $this->data['users'] = $this->userRepository->findAll($options);

        return view('admin.users.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['permissions'] = $this->permissionRepository->findAll();
        $this->data['roles'] = $this->roleRepository->findAll()->pluck('name', 'id');
        $this->data['roleId'] = null;

        return view('admin.users.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $params = $request->validated();

        if ($this->userRepository->create($params)) {
            return redirect('admin/users')
                ->with('success', __('users.success_create_message'));
        }

        return redirect('admin/users/create')
            ->with('error', __('users.fail_create_message'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['user'] = $this->userRepository->findById($id);

        return view('admin.users.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findById($id);

        $this->data['user'] = $user;
        $this->data['permissions'] = $this->permissionRepository->findAll();
        $this->data['roles'] = $this->roleRepository->findAll()->pluck('name', 'id');
        $this->data['roleId'] = $user->roles->first() ? $user->roles->first()->id : null;

        return view('admin.users.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = $this->userRepository->findById($id);

        if ($this->userRepository->update($id, $request->validated())) {
            return redirect('admin/users')
                ->with('success', __('users.success_updated_message', ['name' => $user->name]));
        }

        return redirect('admin/users')
                ->with('error', __('users.fail_to_update_message', ['name' => $user->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findById($id);

        if ($user->id == auth()->user()->id) {
            return redirect('admin/users')
                ->with('error', 'Could not delete yourself.');
        }

        if ($user->hasRole(\App\Models\Role::ADMIN)) {
            return redirect('admin/users')
                ->with('error', 'Could not delete the admin user.');
        }

        if ($this->userRepository->delete($id)) {
            return redirect('admin/users')
                ->with('success', __('users.success_deleted_message', ['name' => $user->name]));
        }

        return redirect('admin/users')
                ->with('error', __('users.fail_to_delete_message', ['name' => $user->name]));
    }
}
