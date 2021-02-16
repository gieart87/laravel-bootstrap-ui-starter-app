<?php

namespace App\Repositories\Admin;

use Carbon\Carbon;
use DB;

use App\Repositories\Admin\Interfaces\UserRepositoryInterface;

use App\Models\User;
use App\Models\Role;

class UserRepository implements UserRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $users = (new User())->with('roles');

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $users = $users->orderBy($field, $sort);
            }
        }

        if (!empty($options['filter']['start_date']) && !empty($options['filter']['end_date'])) {
            $startDate = Carbon::parse($options['filter']['start_date']);
            $endDate = Carbon::parse($options['filter']['end_date']);

            $users = $users->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }

        if (!empty($options['filter']['q'])) {
            $users = $users->where('name', 'LIKE', "%{$options['filter']['q']}%")
                ->orWhere('email', 'LIKE', "%{$options['filter']['q']}%");
        }

        if ($perPage) {
            return $users->paginate($perPage);
        }
        
        return $users->get();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function create($params = [])
    {
        return DB::transaction(function () use ($params) {
            $user = User::create($params);
            $this->syncRolesAndPermissions($params, $user);
            
            return $user;
        });
    }

    public function update($id, $params = [])
    {
        $user = User::findOrFail($id);
        
        if (!$params['password']) {
            unset($params['password']);
        }

        return DB::transaction(function () use ($params, $user) {
            $user->update($params);
            $this->syncRolesAndPermissions($params, $user);
            
            return $user;
        });
    }

    public function delete($id)
    {
        $user  = User::findOrFail($id);

        return $user->delete();
    }
    
    /**
     * Sync roles and permissions
     *
     * @param Request $request
     * @param $user
     * @return string
     */
    private function syncRolesAndPermissions($params, $user)
    {
        // Get the submitted roles
        $roles = isset($params['role_id']) ? [$params['role_id']] : [];
        $permissions = isset($params['permissions']) ? $params['permissions'] : [];

        // Get the roles
        $roles = Role::find($roles);

        // check for current role changes
        if (!$user->hasAllRoles($roles)) {
            // reset all direct permissions for user
            $user->permissions()->sync([]);
        } else {
            // handle permissions
            $user->syncPermissions($permissions);
        }

        $user->syncRoles($roles);

        return $user;
    }
}
