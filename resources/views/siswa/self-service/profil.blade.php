@extends('layouts/layoutMaster')

@section('title', 'Profil Saya')

@section('content')
<div class="flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Self Service /</span> Profil Saya
  </h4>

  @livewire('emis.self-service-profil')
</div>
@endsection
