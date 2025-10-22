<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // arahkan ke resources/views/admin/dashboard/dashboard.blade.php
        return view('admin.dashboard.dashboard');
    }
}
