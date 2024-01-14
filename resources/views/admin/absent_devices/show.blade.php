@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Access Device - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route('admin.absent_devices.index') }}">Access Device</a>
                    <span class="breadcrumb-item active">{{ $data->absent_device_id }}</span>
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
        <div class="col-xl-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detail Access Device</h5>
                </div>

                <div class="card-body border-top">
                    <div class="row g-lg-5 g-2">
                        <div class="col-12">
                            <form action="{{ route('admin.absent_devices.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Access Device ID</label>
                                    <div class="col-lg-8">
                                        <input disabled class="form-control" value="{{ $data->absent_device_id }}" name="absent_device_id"
                                            placeholder="Type Access Device ID" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Subscribe Topic</label>
                                    <div class="col-lg-8">
                                        <input disabled class="form-control" value="{{ $data->subscribe_topic }}"
                                            name="subscribe_topic" placeholder="Type Subscribe Topic" required
                                            type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Publish Topic</label>
                                    <div class="col-lg-8">
                                        <input disabled class="form-control" value="{{ $data->publish_topic }}"
                                            name="publish_topic" placeholder="Type Publish Topic" required type="text">
                                    </div>
                                </div>
                                @can('absent_devices-update')
                                    <div class="text-end">
                                        <a href="{{ route('admin.absent_devices.edit', $data->id) }}" class="btn btn-primary"
                                            type="submit">Edit</a>
                                    </div>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
