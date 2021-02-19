<?php

namespace App\Repositories\Admin;

use DB;

use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;

use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $roles = new Role();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $roles = $roles->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $roles->paginate($perPage);
        }
        
        return $roles->get();
    }

    public function findById($id)
    {
        return Role::findOrFail($id);
    }

    public function findByName($name)
    {
        return  Role::findOrCreate($name);
    }

    public function create($params = [])
    {
        $params['guard_name'] = 'web';

        return DB::transaction(function () use ($params) {
            if ($role = Role::create($params)) {
                $permissions = !empty($params['permissions']) ? $params['permissions'] : [];
                $role->syncPermissions($permissions);
    
                return $role;
            }
        });
    }

    public function update($id, $params = [])
    {
        $role = Role::findOrFail($id);

        if ($role->name == Role::ADMIN) {
            return true;
        }

        return DB::transaction(function () use ($params, $role) {
            $permissions = !empty($params['permissions']) ? $params['permissions'] : [];
            $role->syncPermissions($permissions);
        
            return $role->update($params);
        });
    }

    public function delete($id)
    {
        $role  = Role::findOrFail($id);

        if ($role->name == Role::ADMIN) {
            return false;
        }

        return $role->delete();
    }
}
