<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NavbarFull extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'navbarFull'];
    return view('content.dashboard.dashboards-analytics', ['pageConfigs' => $pageConfigs]);
  }
}
