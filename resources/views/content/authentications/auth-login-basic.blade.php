@php
$pageConfigs = ['myLayout' => 'blank'];
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Basic - Pages')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
  /* Semi Dark Green Theme with Gradient and Glassmorphism */
  .authentication-wrapper.authentication-basic {
    background: radial-gradient(circle at 50% 50%, #0d2a1d 0%, #05140e 100%) !important;
  }

  .authentication-wrapper.authentication-basic .authentication-inner {
    block-size: auto !important;
  }

  /* Decorative green glow shapes */
  .authentication-wrapper.authentication-basic .authentication-inner::before {
    background: #2ecc71 !important;
    opacity: 0.18 !important;
  }
  .authentication-wrapper.authentication-basic .authentication-inner::after {
    background: #2ecc71 !important;
    opacity: 0.18 !important;
  }

  /* Glassmorphism Card */
  .authentication-wrapper.authentication-basic .card {
    background: rgba(13, 40, 27, 0.45) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.4) !important;
  }

  /* Typography color adjustments for dark green background */
  .authentication-wrapper.authentication-basic h4,
  .authentication-wrapper.authentication-basic .card-body {
    color: #e8f5e9 !important;
  }
  
  .authentication-wrapper.authentication-basic p {
    color: #a3b899 !important;
  }

  .authentication-wrapper.authentication-basic label.form-label {
    color: #c8e6c9 !important;
    font-weight: 500;
  }

  /* Inputs and Controls styling */
  .authentication-wrapper.authentication-basic .form-control {
    background-color: rgba(5, 20, 14, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
    transition: all 0.2s ease-in-out;
  }

  .authentication-wrapper.authentication-basic .form-control:focus {
    border-color: #2ecc71 !important;
    box-shadow: 0 0 0 0.25rem rgba(46, 204, 113, 0.25) !important;
    background-color: rgba(5, 20, 14, 0.8) !important;
    color: #ffffff !important;
  }

  /* Autofill/Autocomplete override to keep text white on dark background */
  .authentication-wrapper.authentication-basic input:-webkit-autofill,
  .authentication-wrapper.authentication-basic input:-webkit-autofill:hover, 
  .authentication-wrapper.authentication-basic input:-webkit-autofill:focus, 
  .authentication-wrapper.authentication-basic input:-webkit-autofill:active {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0 1000px #092015 inset !important;
    transition: background-color 5000s ease-in-out 0s;
  }

  .authentication-wrapper.authentication-basic .input-group-text {
    background-color: rgba(5, 20, 14, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
  }

  .authentication-wrapper.authentication-basic .input-group:focus-within .form-control,
  .authentication-wrapper.authentication-basic .input-group:focus-within .input-group-text {
    border-color: #2ecc71 !important;
  }

  .authentication-wrapper.authentication-basic .form-control::placeholder {
    color: rgba(255, 255, 255, 0.4) !important;
  }

  /* Links & Accents */
  .authentication-wrapper.authentication-basic a {
    color: #2ecc71 !important;
    transition: color 0.2s;
  }

  .authentication-wrapper.authentication-basic a:hover {
    color: #27ae60 !important;
    text-decoration: underline;
  }

  /* Remember Me checkbox */
  .authentication-wrapper.authentication-basic .form-check-input {
    background-color: rgba(5, 20, 14, 0.5) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
  }

  .authentication-wrapper.authentication-basic .form-check-input:checked {
    background-color: #2ecc71 !important;
    border-color: #2ecc71 !important;
  }

  .authentication-wrapper.authentication-basic .form-check-label {
    color: #c8e6c9 !important;
  }

  /* Emerald Gradient Login Button */
  .authentication-wrapper.authentication-basic .btn-primary {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%) !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.35) !important;
    color: #ffffff !important;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .authentication-wrapper.authentication-basic .btn-primary:hover {
    background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%) !important;
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.5) !important;
    transform: translateY(-1px);
  }

  .authentication-wrapper.authentication-basic .btn-primary:active {
    transform: translateY(0);
  }

  /* Logo text & SVG coloring */
  .authentication-wrapper.authentication-basic .app-brand-text {
    color: #ffffff !important;
  }

  .authentication-wrapper.authentication-basic .app-brand-logo svg {
    fill: #2ecc71 !important;
  }

  /* Error validation alerts */
  .authentication-wrapper.authentication-basic .alert-danger {
    background: rgba(220, 53, 69, 0.18) !important;
    border: 1px solid rgba(220, 53, 69, 0.3) !important;
    color: #f8d7da !important;
  }

  /* Eye toggler icon base styling */
  .authentication-wrapper.authentication-basic .input-group-text .icon-base {
    color: #a9dfbf !important;
  }
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic vh-100 overflow-hidden d-flex align-items-center justify-content-center">
  <div class="authentication-inner">
    <!-- Login -->
    <div class="card">
      <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center mb-6">
          <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">@include('_partials.macros')</span>
            <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
        <p class="mb-6">Please sign-in to your account and start the adventure</p>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="formAuthentication" class="mb-4" action="{{ route('login.post') }}" method="POST">
          @csrf
          <div class="mb-6 form-control-validation">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username"
              placeholder="Masukkan username Anda" value="{{ old('username') }}" autofocus />
          </div>
          <div class="mb-6 form-password-toggle form-control-validation">
            <div class="d-flex justify-content-between">
              <label class="form-label" for="password">Password</label>
              <a href="{{ url('auth/forgot-password-basic') }}">
                <small>Forgot Password?</small>
              </a>
            </div>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password" />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
          </div>
          <div class="my-8">
            <div class="d-flex justify-content-between">
              <div class="form-check mb-0 ms-2">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                <label class="form-check-label" for="remember-me"> Remember Me </label>
              </div>
            </div>
          </div>
          <div class="mb-6">
            <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
    <!-- /Login -->
  </div>
</div>
@endsection