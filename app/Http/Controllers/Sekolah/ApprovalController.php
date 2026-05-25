<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;

class ApprovalController extends Controller
{
    public function antrian()
    {
        return view('sekolah.approval.antrian');
    }
}
