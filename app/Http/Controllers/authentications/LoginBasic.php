<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginBasic extends Controller
{
  public function index()
  {
    if (Auth::check()) {
      return redirect()->route('dashboard');
    }
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  public function login(Request $request)
  {
    $request->validate([
      'username' => ['required', 'string'],
      'password' => ['required'],
    ]);

    $remember = $request->has('remember');

    // Cari user berdasarkan username
    $user = User::where('username', $request->username)->first();

    // Verifikasi user & password
    if ($user && Hash::check($request->password, $user->password)) {
      Auth::login($user, $remember);
      $request->session()->regenerate();

      return redirect()->intended('/');
    }

    return back()->withErrors([
      'username' => 'Username atau password salah.',
    ])->onlyInput('username');
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
  }
}
