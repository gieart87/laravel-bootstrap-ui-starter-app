<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\RoleRepository;

use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;
use App\Repositories\Admin\PermissionRepository;

use App\Repositories\Admin\Interfaces\UserRepositoryInterface;
use App\Repositories\Admin\UserRepository;

use Nwidart\Modules\Facades\Module;

class AdminRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initModules();
    }

    private function initModules()
    {
        $modules = Module::getOrdered();
        $moduleAdminMenus = [];

        if ($modules) {
            foreach ($modules as $module) {
                $moduleDetails = $this->getModuleDetails($module);

                if (!empty($moduleDetails['admin_menus'])) {
                    $moduleAdminMenus[] = [
                        'module' => $module->getLowerName(),
                        'admin_menus' => $moduleDetails['admin_menus'],
                    ];
                }
            }
        }

        View::share('moduleAdminMenus', $moduleAdminMenus);
    }

    private function getModuleDetails($module)
    {
        $moduleJson = $module->getPath(). '/module.json';
        return json_decode(file_get_contents($moduleJson), true);
    }
}
