@extends('layouts/layoutMaster')

@section('title', 'Profil Saya — EMIS')

@section('page-style')
  @include('admin._emis-styles')
@endsection

@section('content')

  {{-- ============================================ --}}
  {{-- PAGE HEADER                                 --}}
  {{-- ============================================ --}}
  <div class="emis-page-header emis-fade-up mb-6">
    <div class="emis-page-header-content">
      <h1><i class="ti tabler-user me-2" style="font-size:1.3rem;vertical-align:middle;opacity:.8;"></i>Profil Saya</h1>
      <p>Informasi biodata diri Anda</p>
    </div>
  </div>

  {{-- ============================================ --}}
  {{-- CONTENT                                     --}}
  {{-- ============================================ --}}
  <div class="card emis-fade-up delay-2">
    <div class="card-body">
      @livewire('emis.self-service-profil')
    </div>
  </div>

@endsection
