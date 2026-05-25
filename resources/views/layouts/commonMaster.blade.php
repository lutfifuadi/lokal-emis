<!DOCTYPE html>
@php
  use Illuminate\Support\Str;
  use App\Helpers\Helpers;

  $menuFixed =
      $configData['layout'] === 'vertical'
          ? $menuFixed ?? ''
          : ($configData['layout'] === 'front'
              ? ''
              : $configData['headerType']);
  $navbarType =
      $configData['layout'] === 'vertical'
          ? $configData['navbarType']
          : ($configData['layout'] === 'front'
              ? 'layout-navbar-fixed'
              : '');
  $isFront = ($isFront ?? '') == true ? 'Front' : '';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';

  // Get skin name from configData - only applies to admin layouts
  $isAdminLayout = !Str::contains($configData['layout'] ?? '', 'front');
  $skinName = $isAdminLayout ? $configData['skinName'] ?? 'default' : 'default';

  // Get semiDark value from configData - only applies to admin layouts
  $semiDarkEnabled = $isAdminLayout && filter_var($configData['semiDark'] ?? false, FILTER_VALIDATE_BOOLEAN);

  // Generate primary color CSS if color is set
  $primaryColorCSS = '';
  if (isset($configData['color']) && $configData['color']) {
      $primaryColorCSS = Helpers::generatePrimaryColorCSS($configData['color']);
  }

@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
  class="{{ $navbarType ?? '' }} {{ $contentLayout ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
  dir="{{ $configData['textDirection'] }}" data-skin="{{ $skinName }}" data-assets-path="{{ asset('/assets') . '/' }}"
  data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $configData['layout'] }}-menu-template"
  data-bs-theme="{{ $configData['theme'] }}" @if ($isAdminLayout && $semiDarkEnabled) data-semidark-menu="true" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>
    @yield('title') | {{ config('variables.templateName') ? config('variables.templateName') : 'TemplateName' }}
    - {{ config('variables.templateSuffix') ? config('variables.templateSuffix') : 'TemplateSuffix' }}
  </title>
  <meta name="description"
    content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta name="keywords"
    content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}" />
  <meta property="og:title" content="{{ config('variables.ogTitle') ? config('variables.ogTitle') : '' }}" />
  <meta property="og:type" content="{{ config('variables.ogType') ? config('variables.ogType') : '' }}" />
  <meta property="og:url" content="{{ config('variables.productPage') ? config('variables.productPage') : '' }}" />
  <meta property="og:image" content="{{ config('variables.ogImage') ? config('variables.ogImage') : '' }}" />
  <meta property="og:description"
    content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
  <meta property="og:site_name"
    content="{{ config('variables.creatorName') ? config('variables.creatorName') : '' }}" />
  <meta name="robots" content="noindex, nofollow" />
  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Canonical SEO -->
  <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)
  @livewireStyles

  @if (
      $primaryColorCSS &&
          (config('custom.custom.primaryColor') ||
              isset($_COOKIE['admin-primaryColor']) ||
              isset($_COOKIE['front-primaryColor'])))
    <!-- Primary Color Style -->
    <style id="primary-color-style">
      {!! $primaryColorCSS !!}
    </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)
</head>

<body>
  {{-- Banner Impersonation (tampil saat sesi impersonation aktif) --}}
  @if(session('impersonated_by'))
    <div id="impersonation-banner" style="
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      z-index: 9999;
      background: rgba(255, 152, 0, 0.18);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-top: 1.5px solid rgba(255, 152, 0, 0.45);
      padding: 10px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 2px 16px rgba(255, 152, 0, 0.12);
    ">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="
          background: rgba(255,152,0,0.22);
          border-radius: 50%;
          width: 34px; height: 34px;
          display:flex; align-items:center; justify-content:center;
          flex-shrink:0;
        ">
          <i class="icon-base ti tabler-user-shield" style="color:#e65100; font-size:18px;"></i>
        </span>
        <div>
          <span style="font-weight:700; color:#e65100; font-size:13px; letter-spacing:0.02em;">
            MODE IMPERSONATION AKTIF
          </span>
          <span style="display:block; font-size:12px; color:#6d4c41; margin-top:1px;">
            Anda sedang login sebagai <strong>{{ auth()->user()->name }}</strong>
            ({{ auth()->user()->roles->first()?->name ?? '-' }})
          </span>
        </div>
      </div>
      <form method="POST" action="{{ route('impersonate.stop') }}" style="margin:0;">
        @csrf
        <button type="submit" style="
          background: linear-gradient(135deg, #ff9800, #e65100);
          color: #fff;
          border: none;
          border-radius: 8px;
          padding: 7px 16px;
          font-size: 13px;
          font-weight: 600;
          cursor: pointer;
          display: flex;
          align-items: center;
          gap: 6px;
          transition: opacity 0.2s;
          white-space: nowrap;
        " onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
          <i class="icon-base ti tabler-arrow-back-up" style="font-size:15px;"></i>
          Kembali ke Admin
        </button>
      </form>
    </div>
    {{-- Dorong konten ke atas agar tidak tertutup banner bawah --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var banner = document.getElementById('impersonation-banner');
        if (banner) {
          document.body.style.paddingBottom = banner.offsetHeight + 'px';
        }
      });
    </script>
  @endif

  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)
  @livewireScripts
</body>

</html>
