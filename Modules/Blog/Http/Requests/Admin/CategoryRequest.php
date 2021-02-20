<?php

namespace Modules\Blog\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $parentId = $this->get('parent_id');
        $id = $this->get('id');

        if (!$parentId) {
            if ($this->method() == 'PUT') {
                $name = 'unique:blog_categories,name,'.$id;
                $slug = 'unique:blog_categories,slug,'.$id;
            } else {
                $name = 'required|unique:blog_categories,name';
                $slug = 'unique:blog_categories,slug';
            }
        } else {
            if ($this->method() == 'PUT') {
                $name = 'required|unique:blog_categories,name,'.$id.',id,parent_id,'.$parentId;
                $slug = 'unique:blog_categories,slug,'.$id;
            } else {
                $name = 'required|unique:blog_categories,name,NULL,id,parent_id,'.$parentId;
                $slug = 'unique:blog_categories,slug';
            }
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'parent_id' => '',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
