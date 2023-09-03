@extends('admin.layout.main')

@push('header')
  <div class="page-header page-header-light shadow">
    <div class="page-header-content d-lg-flex">
      <div class="d-flex">
        <h4 class="page-title mb-0">
          Dashboard
        </h4>
      </div>
    </div>

    <div class="page-header-content d-lg-flex border-top">
      <div class="d-flex">
        <div class="breadcrumb py-2">
          <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
          <a class="breadcrumb-item" href="#">Dashboard</a>
        </div>
        <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
          data-bs-toggle="collapse" href="#breadcrumb_elements">
          <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
        </a>
      </div>
      <div class="ms-auto d-flex gap-3 py-3 bg-white">
        <a href="{{ route('admin.dashboard.index') }}?area=jakarta">Jakarta</a>
        <span>|</span>
        <a href="{{ route('admin.dashboard.index') }}?area=bandung">Bandung</a>
        <span>|</span>
        <a href="{{ route('admin.dashboard.index') }}?area=depok">Depok</a>
      </div>
    </div>
  </div>
@endpush

@section('content')
  <div class="row g-3">
    @foreach ($status_type_widgets as $status_type_widget)
      <div class="col-lg-3 col-12">
        <div class="card text-white" style="background-color: {{ $status_type_widget->status_type->color }};">
          <div class="card-body">
            <div class="d-flex">
              <h3 class="mb-0">
                {{ $status_type_widget->status_type->device_status->where('device', '!=', null)->count() }}
              </h3>
            </div>
            <div>
              {{ $status_type_widget->status_type->name }}
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
