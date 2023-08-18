@extends('admin.layout.main')

@push('header')
  <div class="page-header page-header-light shadow">
    <div class="page-header-content d-lg-flex">
      <div class="d-flex">
        <h4 class="page-title mb-0">
          Device - <span class="fw-normal">All</span>
        </h4>
      </div>
    </div>

    <div class="page-header-content d-lg-flex border-top">
      <div class="d-flex">
        <div class="breadcrumb py-2">
          <a class="breadcrumb-item"
             href="/admin/dashboard"><i class="ph-house"></i></a>
          <a class="breadcrumb-item"
             href="#">Device</a>
          <span class="breadcrumb-item active">All</span>
        </div>
        <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
           data-bs-toggle="collapse"
           href="#breadcrumb_elements">
          <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
        </a>
      </div>
    </div>
  </div>
@endpush

@section('content')
  <!-- Basic datatable -->
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Device</h5>
    </div>

    <div class="card-header">
      List of All Registered Device.
    </div>

    <table class="table datatable-basic">
      <thead>
        <tr>
          <th>No</th>
          <th>Device ID</th>
          <th>Subscribe Topic</th>
          <th>Publish Topic</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($datas as $key => $data)
          <tr>
            <td>{{ $key + 1 }}</td>
            <td><a href="{{ route('admin.devices.show', $data->id) }}">{{ $data->device_id }}</a></td>
            <td>{{ $data->publish_topic }}</td>
            <td>{{ $data->subscribe_topic }}</td>
            <td class="text-center">
              <div class="d-inline-flex">
                <div class="dropdown">
                  <a class="text-body"
                     data-bs-toggle="dropdown"
                     href="#">
                    <i class="ph-list"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item"
                       href="{{ route('admin.devices.show', $data->id) }}">
                      <i class="ph-scroll me-2"></i>
                      Show
                    </a>
                    <a class="dropdown-item"
                       href="{{ route('admin.devices.edit', $data->id) }}">
                      <i class="ph-pen me-2"></i>
                      Edit
                    </a>
                    <form action="{{ route('admin.devices.destroy', $data->id) }}"
                          method="POST">
                      @csrf
                      @method('delete')
                      <button class="dropdown-item">
                        <i class="ph-trash me-2"></i>
                        Delete
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!-- /basic datatable -->
@endsection
