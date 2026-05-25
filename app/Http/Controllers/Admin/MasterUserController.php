<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MasterUserController extends Controller
{
    public function index()
    {
        return view('admin.master.users');
    }
}
