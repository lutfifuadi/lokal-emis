<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MasterKelasController extends Controller
{
    public function index()
    {
        return view('admin.master.kelas');
    }
}
