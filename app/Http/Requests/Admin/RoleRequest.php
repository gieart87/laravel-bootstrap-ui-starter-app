<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Role;
use Auth;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->id) {
            $role = Role::findOrFail($this->id);

            if ($role->name == Role::ADMIN) {
                return [];
            }
        }

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'min:2',
                Rule::unique(Role::class)->ignore($this->id),
            ],
            'permissions' => [
                'required',
                'array',
                'min:1'
            ],
            'permissions.*' => [
                'required',
                'string'
            ]
        ];
    }
}
