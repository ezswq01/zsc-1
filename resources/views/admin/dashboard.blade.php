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
    </div>
  </div>
@endpush

@section('content')
  <div class="row">
    <div class="col-lg-3 col-12">
      <div class="card bg-teal text-white">
        <div class="card-body">
          <div class="d-flex">
            <h3 class="mb-0">0</h3>
          </div>
          <div>
            Service Type 1
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card bg-teal text-white">
        <div class="card-body">
          <div class="d-flex">
            <h3 class="mb-0">0</h3>
          </div>
          <div>
            Service Type 2
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card bg-teal text-white">
        <div class="card-body">
          <div class="d-flex">
            <h3 class="mb-0">0</h3>
          </div>
          <div>
            Service Type 3
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
