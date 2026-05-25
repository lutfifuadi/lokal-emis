<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MasterTahunAjaranController extends Controller
{
    public function index()
    {
        return view('admin.master.tahun-ajaran');
    }
}
