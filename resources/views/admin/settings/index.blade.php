@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Setting - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route("admin.settings.index") }}">Setting</a>
                    <span class="breadcrumb-item active">{{ $data->app_name }}</span>
                </div>
                <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse" href="#breadcrumb_elements">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>
        </div>
    </div>
@endpush

@section("content")
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Setting</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route("admin.settings.update", $data->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method("PUT")
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">App Name</label>
                            <div class="col-lg-8">
                                <input class="form-control" value="{{ $data->app_name }}" name="app_name"
                                    placeholder="Type App Name" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Access Device Feature</label>
                            <div class="col-lg-8">
                                <label class="form-check form-check-inline">
                                    <input name="is_access_device" type="checkbox" class="form-check-input"
                                        {{ $data->is_access_device ? "checked" : "" }}>
                                    <span class="form-check-label">On / Off</span>
                                </label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Status Card Active</label>
                            <div class="col-lg-8">
                                <select multiple="multiple" class="form-control select"
                                    data-placeholder="Select Status Card Active" name="status_types[]">
                                    <option></option>
                                    @foreach ($status_types as $status_type)
                                        @php
                                            $selected = in_array($status_type->id, $status_type_widgets->pluck("status_type_id")->toArray()) ? "selected" : "";
                                        @endphp
                                        <option {{ $selected }} value="{{ $status_type->id }}">{{ $status_type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Logo</label>
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                                        <img src="{{ Storage::url($data->logo) }}" alt="logo"
                                            class="rounded img-fluid img-thumbnail"
                                            style="object-fit: cover; object-position: center;">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <input type="file" class="form-control h-auto" name="logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary" type="submit">Submit form <i
                                    class="ph-paper-plane-tilt ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
