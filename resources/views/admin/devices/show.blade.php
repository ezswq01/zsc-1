@extends('admin.layout.main')

@push('header')
  <div class="page-header page-header-light shadow">
    <div class="page-header-content d-lg-flex">
      <div class="d-flex">
        <h4 class="page-title mb-0">
          Device - <span class="fw-normal">Detail</span>
        </h4>
      </div>
    </div>

    <div class="page-header-content d-lg-flex border-top">
      <div class="d-flex">
        <div class="breadcrumb py-2">
          <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
          <a class="breadcrumb-item" href="#">Device</a>
          <span class="breadcrumb-item active">{{ $data->name }}</span>
        </div>
        <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse" href="#breadcrumb_elements">
          <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
        </a>
      </div>
    </div>
  </div>
@endpush

@section('content')
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Detail Device</h5>
    </div>

    <div class="card-body border-top">
      <div class="row g-lg-5 g-2">
        <div class="col-lg-8 col-12">
          <form action="{{ route('admin.devices.update', $data->id) }}" method="POST">
            @csrf
            @method('put')

            <div class="row mb-3">
              <label class="col-lg-4 col-form-label">Name</label>
              <div class="col-lg-8">
                <input class="form-control" disabled name="name" placeholder="Type Name" required type="text" value="{{ $data->name }}">
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-lg-4 col-form-label">Description</label>
              <div class="col-lg-8">
                <input class="form-control" disabled name="desc" placeholder="Type Description" required type="text" value="{{ $data->desc }}">
              </div>
            </div>

            <div class="text-end">
              <a class="btn btn-primary"
                href="{{ route('admin.devices.edit', $data->id) }}"
                type="submit">Edit<i class="ph-pen ms-2"></i></a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
