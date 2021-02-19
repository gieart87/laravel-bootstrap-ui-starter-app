<?php

namespace Modules\Blog\Tests;

use Nwidart\Modules\Facades\Module;
use Tests\TestCase;

use App\Repositories\Admin\RoleRepository;
use App\Repositories\Admin\PermissionRepository;

class BlogTestCase extends TestCase
{
    protected function setupBlogPermissions()
    {
        $moduleJson = base_path().'/Modules/Blog/module.json';
        $moduleDetails = json_decode(file_get_contents($moduleJson), true);
        if (!empty($moduleDetails['permissions'])) {
            foreach ($moduleDetails['permissions'] as $permission) {
                $this->initPermissionActions($permission);
            }
        }
    }

    private function initPermissionActions($permission)
    {
        $permissionMappings = ['view', 'add', 'edit', 'delete'];
        $permissionRepository = new PermissionRepository();
        $roleRepository = new RoleRepository();

        $permissionActions = [];
        foreach ($permissionMappings as $permissionMapping) {
            $name = $permissionMapping . '_' . $permission;
            $permissionActions[] = $permissionRepository->create(['name' => $name]);
        }

        $adminRole = $roleRepository->findByName('admin');
        $adminRole->givePermissionTo($permissionActions);
    }
}
