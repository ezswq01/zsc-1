@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Location - <span class="fw-normal">Edit</span>
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
            <h5 class="mb-0">Edit Location</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route('admin.locations.update', $data->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Location Code <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <input value="{{ old('code', $data->code) }}" class="form-control" name="code"
                                    placeholder="e.g. wsid_b1120874" required type="text">
                                <small class="text-muted">Must match the Room / Location-ID used in devices.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Company Name</label>
                            <div class="col-lg-8">
                                <input value="{{ old('company_name', $data->company_name) }}" class="form-control"
                                    name="company_name" placeholder="Type Company Name" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Location Name</label>
                            <div class="col-lg-8">
                                <input value="{{ old('name', $data->name) }}" class="form-control" name="name"
                                    placeholder="Type Location Name" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Address</label>
                            <div class="col-lg-8">
                                <textarea class="form-control" name="address" rows="3"
                                    placeholder="Type Address">{{ old('address', $data->address) }}</textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">City</label>
                            <div class="col-lg-8">
                                <input value="{{ old('city', $data->city) }}" class="form-control" name="city"
                                    placeholder="Type City" type="text">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Coordinate</label>
                            <div class="col-lg-8">
                                <input value="{{ old('coordinate', $data->coordinate) }}" class="form-control"
                                    name="coordinate" placeholder="e.g. -6.2000, 106.8166" type="text">
                                <small class="text-muted">Format: <code>lat, lng</code>. Used as fallback when device reports invalid coordinate (0,0 or 0,1). Leave blank if not applicable.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Status</label>
                            <div class="col-lg-8 d-flex align-items-center">
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1" {{ old('is_active', $data->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        @if($data->last_updated_at || $data->updatedBy)
                            <div class="row mb-3">
                                <label class="col-lg-4 col-form-label text-muted">Last Updated</label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <small class="text-muted">
                                        {{ $data->last_updated_at ? $data->last_updated_at->format('Y-m-d H:i:s') : '-' }}
                                        {{ $data->updatedBy ? ' by ' . $data->updatedBy->name : '' }}
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="text-end">
                            <a href="{{ route('admin.locations.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button class="btn btn-primary" type="submit">Submit form <i class="ph-paper-plane-tilt ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
