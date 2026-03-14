@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Status Type - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route("admin.status_types.index") }}">Status Type</a>
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

@section("content")
    <div class="card shadow-none">
        <div class="card-header">
            <h5 class="mb-0">Detail Status Type</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form action="{{ route("admin.status_types.store") }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Name</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ old("name", $data->name) }}" class="form-control" name="name"
                                    placeholder="Type Name" required type="text">
                            </div>
                        </div>
                        <!-- NEW CATEGORY FIELD -->
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Category</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ old("category", $data->category) }}" class="form-control" name="category"
                                    placeholder="Type Category" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Color</label>
                            <div class="col-lg-8">
                                <input class="form-control form-control-color" disabled
                                    value="{{ old("color", $data->color) }}" type="color" name="color">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Trigger Color</label>
                            <div class="col-lg-8">
                                <input class="form-control form-control-color"
                                    value="{{ old("trigger_color", $data->trigger_color) }}" type="color"
                                    name="trigger_color">
                            </div>
                        </div>
                        @can("status-types-update")
                            <div class="text-end">
                                <a href="{{ route("admin.status_types.edit", $data->id) }}" class="btn btn-primary"
                                    type="submit">Edit</a>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
