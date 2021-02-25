<?php

namespace Modules\Blog\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Blog\Entities\Post;

class PostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == 'PUT') {
            $title = 'required|unique:blog_posts,title,' . $this->get('id');
        } else {
            $title = 'required|unique:blog_posts,title';
        }
       
        $metaFieldsRules = [];
        if (Post::META_FIELDS) {
            foreach (Post::META_FIELDS as $metaField => $metaFieldAttr) {
                $metaFieldsRules[$metaField] = $metaFieldAttr['validation_rules'];
            }
        }

        return array_merge($metaFieldsRules, [
            'title' => $title,
            'excerpt' => '',
            'body' => '',
            'status' => '',
            'publish_date' => '',
            'tags' => '',
            'categories' => '',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);
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
