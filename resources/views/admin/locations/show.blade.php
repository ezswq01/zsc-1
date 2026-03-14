@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Location - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route('admin.locations.index') }}">Location</a>
                    <span class="breadcrumb-item active">{{ $data->code }}</span>
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
            <h5 class="mb-0">Detail Location</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Location Code</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ $data->code }}" class="form-control" name="code"
                                    placeholder="Location Code" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Company Name</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ $data->company_name ?? '-' }}" class="form-control"
                                    name="company_name" placeholder="Company Name" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Location Name</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ $data->name ?? '-' }}" class="form-control" name="name"
                                    placeholder="Location Name" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Address</label>
                            <div class="col-lg-8">
                                <textarea disabled class="form-control" name="address"
                                    rows="3">{{ $data->address ?? '-' }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">City</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ $data->city ?? '-' }}" class="form-control" name="city"
                                    placeholder="City" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Coordinate</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ $data->coordinate ?? '-' }}" class="form-control"
                                    name="coordinate" placeholder="lat, lng" type="text">
                                @if($data->parsed_coordinate)
                                    <small class="text-muted">
                                        Lat: {{ $data->parsed_coordinate['lat'] }},
                                        Lng: {{ $data->parsed_coordinate['lng'] }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Status</label>
                            <div class="col-lg-8 d-flex align-items-center">
                                @if($data->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Last Updated</label>
                            <div class="col-lg-8 d-flex align-items-center">
                                <span class="text-muted small">
                                    {{ $data->last_updated_at ? $data->last_updated_at->format('Y-m-d H:i:s') : '-' }}
                                    {{ $data->updatedBy ? ' by ' . $data->updatedBy->name : '' }}
                                </span>
                            </div>
                        </div>

                        @can('locations-update')
                            <div class="text-end">
                                <a href="{{ route('admin.locations.edit', $data->id) }}" class="btn btn-primary">
                                    <i class="ph-pen me-1"></i> Edit
                                </a>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
