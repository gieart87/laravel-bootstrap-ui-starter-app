<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlogController extends Controller
{
    protected $data = [];
    protected $perPage = 20;

    public function __construct()
    {
        $this->data['currentAdminMenu'] = '';
    }
}
