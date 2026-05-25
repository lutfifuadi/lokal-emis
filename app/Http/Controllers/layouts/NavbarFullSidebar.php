<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NavbarFullSidebar extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'navbarFullSidebar'];
    return view('content.dashboard.dashboards-analytics', ['pageConfigs' => $pageConfigs]);
  }
}
