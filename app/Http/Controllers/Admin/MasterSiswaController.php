<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;

class MasterSiswaController extends Controller
{
    public function index()
    {
        return view('admin.master.siswa');
    }

    public function tambah()
    {
        return view('admin.master.siswa-form', ['siswaId' => null]);
    }

    public function edit($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        return view('admin.master.siswa-form', ['siswaId' => $siswa->id]);
    }
}
