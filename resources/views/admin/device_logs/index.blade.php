@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Log and Report - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Log and Report</a>
                    <span class="breadcrumb-item active">All</span>
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
    <!-- Basic datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Log and Report</h5>
        </div>

        <div class="card-header">
            List of All Device Logs.
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Device ID</th>
                    <th>Command</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($device_logs as $key => $device_log)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $device_log->device->device_id }}</td>
                        <td>{{ $device_log->value }}</td>
                        <td>{{ $device_log->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection
