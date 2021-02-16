<?php

namespace Modules\Blog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        return [
            'title' => $title,
            'excerpt' => '',
            'body' => '',
            'status' => '',
            'publish_date' => '',
            'tags' => '',
            'categories' => '',
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
