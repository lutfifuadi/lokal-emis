<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class MasterSekolahController extends Controller
{
    public function index()
    {
        return view('admin.master.sekolah');
    }
}
