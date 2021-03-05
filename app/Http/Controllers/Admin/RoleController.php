<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Nwidart\Modules\Facades\Module;

use App\Http\Requests\Admin\RoleRequest;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;

use App\Models\Role;
use App\Authorizable;

class RoleController extends Controller
{
    use Authorizable;

    private $roleRepository;
    private $permissionRepository;

    public function __construct(RoleRepositoryInterface $roleRepository, PermissionRepositoryInterface $permissionRepository) // phpcs:ignore
    {
        parent::__construct();

        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;

        $this->data['currentAdminMenu'] = 'roles';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = [
            'per_page' => $this->perPage,
            'order' => [
                'name' => 'asc',
            ]
        ];

        $this->data['roles'] = $this->roleRepository->findAll($options);

        return view('admin.roles.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->data['permissions'] = $this->permissionRepository->findAll();
        return view('admin.roles.form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $params = $request->validated();

        if ($this->roleRepository->create($params)) {
            return redirect('admin/roles')
                ->with('success', __('roles.success_create_message'));
        }

        return redirect('admin/roles/create')
            ->with('error', 'Role could not be saved!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->data['role'] = $this->roleRepository->findById($id);
        return view('admin.roles.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->findById($id);

        $this->data['role'] = $role;
        $this->data['permissions'] = $this->permissionRepository->findAll();
        $this->data['disabled'] = ($role->name == Role::ADMIN) ? 'disabled' : '';

        return view('admin.roles.form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = $this->roleRepository->findById($id);

        if ($this->roleRepository->update($id, $request->validated())) {
            return redirect('admin/roles')
                ->with('success', __('roles.success_updated_message', ['name' => $role->name]));
        }

        return redirect('admin/roles')
                ->with('error', __('roles.fail_to_update_message', ['name' => $role->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = $this->roleRepository->findById($id);

        if ($this->roleRepository->delete($id)) {
            return redirect('admin/roles')
                ->with('success', __('roles.success_deleted_message', ['name' => $role->name]));
        }

        return redirect('admin/roles')
                ->with('error', __('roles.fail_to_delete_message', ['name' => $role->name]));
    }

    public function reloadPermissions($roleId = null)
    {
        $this->initModules();
        if ($roleId) {
            return redirect('admin/roles/'. $roleId . '/edit')
            ->with('success', __('roles.success_relaod_permission_message'));
        }
        
        return redirect('admin/roles/create')
            ->with('success', __('roles.success_relaod_permission_message'));
    }


    private function initModules()
    {
        $modules = Module::getOrdered();
        $moduleAdminMenus = [];

        if ($modules) {
            foreach ($modules as $module) {
                $this->initModulePermissions($module);
            }
        }
    }

    private function getModuleDetails($module)
    {
        $moduleJson = $module->getPath(). '/module.json';
        return json_decode(file_get_contents($moduleJson), true);
    }

    private function initModulePermissions($module)
    {
        $moduleDetails = $this->getModuleDetails($module);
        if (!empty($moduleDetails['permissions'])) {
            foreach ($moduleDetails['permissions'] as $permission) {
                $this->initPermissionActions($permission);
            }
        }
    }

    private function initPermissionActions($permission)
    {
        $permissionMappings = ['view', 'add', 'edit', 'delete'];

        $permissionActions = [];
        foreach ($permissionMappings as $permissionMapping) {
            $name = $permissionMapping . '_' . $permission;
            $permissionActions[] = $this->permissionRepository->create(['name' => $name]);
        }

        $adminRole = $this->roleRepository->findByName('Admin');
        $adminRole->givePermissionTo($permissionActions);
    }
}
