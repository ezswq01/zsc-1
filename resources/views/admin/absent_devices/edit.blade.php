@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Access Devices - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route("admin.absent_devices.index") }}">Access Devices</a>
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

@section("content")
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Access Devices</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route("admin.absent_devices.update", $data->id) }}" method="POST">
                        @csrf
                        @method("PUT")
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Access Devices ID</label>
                            <div class="col-lg-8">
                                <input class="form-control" value="{{ $data->absent_device_id }}" name="absent_device_id"
                                    placeholder="Type Access Devices ID" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Subscribe Topic</label>
                            <div class="col-lg-8">
                                <input class="form-control" value="{{ $data->subscribe_topic }}" name="subscribe_topic"
                                    placeholder="Type Subscribe Topic" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Publish Topic</label>
                            <div class="col-lg-8">
                                <input class="form-control" value="{{ $data->publish_topic }}" name="publish_topic"
                                    placeholder="Type Publish Topic" required type="text">
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
