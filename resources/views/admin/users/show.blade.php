@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    User - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route("admin.users.index") }}">User</a>
                    <span class="breadcrumb-item active">{{ $user->name }}</span>
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
            <h5 class="mb-0">Detail User</h5>
        </div>

        <div class="card-body border-top">
            <div class="row g-lg-5 g-2">
                <div class="col-lg-8 col-12">
                    <form>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Name</label>
                            <div class="col-lg-8">
                                <input value="{{ old("name", $user->name) }}" class="form-control" name="name"
                                    placeholder="Type Name" required disabled type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Email</label>
                            <div class="col-lg-8">
                                <input value="{{ old("email", $user->email) }}" class="form-control" name="email"
                                    placeholder="Type Email" required disabled type="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Role</label>
                            <div class="col-lg-8">
                                <select class="form-control select" data-placeholder="Select Role" name="role" disabled>
                                    @foreach ($roles as $role)
                                        <option {{ $user->roles[0]->name == $role ? "selected" : "" }}
                                            value="{{ $role }}">
                                            {{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">User Code</label>
                            <div class="col-lg-8">
                                <input value="{{ old("user_code", $user->user_code) }}" class="form-control"
                                    name="user_code" placeholder="Type User Code" required disabled type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Access Device</label>
                            <div class="col-lg-8">
                                <select disabled class="form-control select" data-placeholder="Select Access Device Type"
                                    name="absent_device_id">
                                    <option></option>
                                    @foreach ($absent_devices as $absent_device)
                                        <option
                                            {{ old("absent_device_id", $user->absent_device_id) == $absent_device->id ? "selected" : "" }}
                                            value="{{ $absent_device->id }}">{{ $absent_device->absent_device_id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Job Position</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ old("job_position", $user->job_position) }}" class="form-control" name="job_position"
                                    placeholder="Type Job Position" required type="text">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-lg-4 col-form-label">Work Area</label>
                            <div class="col-lg-8">
                                <input disabled value="{{ old("work_area", $user->work_area) }}" class="form-control" name="work_area"
                                    placeholder="Type Work Area" required type="text">
                            </div>
                        </div>
                        @can("users-update")
                            <div class="text-end">
                                <a href="{{ route("admin.users.edit", $user->id) }}" class="btn btn-primary"
                                    type="submit">Edit</a>
                            </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
