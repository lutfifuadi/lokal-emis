<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;

class ProfilController extends Controller
{
    public function profil()
    {
        return view('siswa.self-service.profil');
    }

    public function perubahan()
    {
        return view('siswa.self-service.perubahan');
    }
}
