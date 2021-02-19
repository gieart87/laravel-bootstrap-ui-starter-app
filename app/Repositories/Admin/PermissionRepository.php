<?php

namespace App\Repositories\Admin;

use App\Repositories\Admin\Interfaces\PermissionRepositoryInterface;

use App\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function findAll($options = [])
    {
        $perPage = $options['per_page'] ?? null;
        $orderByFields = $options['order'] ?? [];

        $permissions = new Permission();

        if ($orderByFields) {
            foreach ($orderByFields as $field => $sort) {
                $permissions = $permissions->orderBy($field, $sort);
            }
        }

        if ($perPage) {
            return $permissions->paginate($perPage);
        }
        
        return $permissions->get();
    }

    public function findById($id)
    {
        return Permission::findOrFail($id);
    }

    public function create($params = [])
    {
        return Permission::firstOrCreate($params);
    }

    public function update($id, $params = [])
    {
        $permission = Permission::findOrFail($id);
       
        return $permission->update($params);
    }

    public function delete($id)
    {
        $permission  = Permission::findOrFail($id);

        return $permission->delete();
    }
}
