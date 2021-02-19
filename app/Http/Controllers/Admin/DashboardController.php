<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['currentAdminMenu'] = 'dashboard';
        return view('admin.dashboard.index', $this->data);
    }
}
