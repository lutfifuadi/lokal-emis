@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Pemeliharaan - Aplikasi EMIS')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
<style>
  body {
    font-family: 'Quicksand', sans-serif !important;
  }
  .misc-wrapper h4 {
    color: #0f1f3d;
    font-weight: 700;
  }
</style>
@endsection

@section('content')
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <div class="mb-4">
      <span class="badge bg-warning bg-opacity-15 text-warning fs-6 px-4 py-2 rounded-pill">
        <i class="icon-base ti tabler-tools me-1"></i> Mode Pemeliharaan
      </span>
    </div>
    <h4 class="mb-2 mx-2" style="color:#0f1f3d;">Aplikasi Sedang Dalam Pemeliharaan</h4>
    <p class="mb-6 mx-2 text-muted">Maaf atas ketidaknyamanannya. Saat ini aplikasi sedang dalam masa pemeliharaan. Silakan kembali lagi nanti.</p>
    <div class="mt-12">
      <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="Under Maintenance" width="550" class="img-fluid" />
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper misc-under-maintenance-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}" height="355" alt="page-misc-under-maintenance" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>
@endsection