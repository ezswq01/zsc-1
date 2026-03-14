@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Device Type - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route('admin.device_types.index') }}">Device Type</a>
                    <span class="breadcrumb-item active">{{ $data->name }}</span>
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
    <div class="card shadow-none">
        <div class="card-header">
            <h5 class="mb-0">Detail Device Type</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route('admin.device_types.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Name</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ old('name', $data->name) }}" class="form-control" name="name"
                                    placeholder="Type Name" required type="text">
                            </div>
                        </div>
                        @can('device-types-update')
                            <div class="text-end">
                                <a href="{{ route('admin.device_types.edit', $data->id) }}" class="btn btn-primary"
                                    type="submit">Edit</a>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
