@extends('layouts/layoutMaster')

@section('title', 'Antrian Approval Perubahan Data')

@section('content')
<div class="flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">Approval /</span> Antrian Perubahan Data
  </h4>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      @livewire('emis.approval-antrian')
    </div>
  </div>
</div>
@endsection
