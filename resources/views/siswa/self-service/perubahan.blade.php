@extends('layouts/layoutMaster')

@section('title', 'Usulan Perubahan Data')

@section('content')
<div class="flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Self Service /</span> Usulan Perubahan Data
  </h4>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      @livewire('emis.self-service-perubahan')
    </div>
  </div>
</div>
@endsection
