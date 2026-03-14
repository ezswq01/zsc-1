@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Access Device - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Access Device</a>
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

@section("content")
    <!-- Basic datatable -->
    <div class="card shadow-none">
        <div class="card-header">
            <h5 class="mb-0">Access Device</h5>
        </div>

        <div class="card-header">
            List of All Registered Access Device.
        </div>

        <table class="table datatable-basic">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Access Device ID</th>
                    <th>Subscribe Topic</th>
                    <th>Publish Topic</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $value)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $value->absent_device_id }}</td>
                        <td>{{ $value->subscribe_topic }}</td>
                        <td>{{ $value->publish_topic }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex">
                                <div class="dropdown">
                                    <a class="text-body" data-bs-toggle="dropdown" href="#">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @can("devices-read")
                                            <a class="dropdown-item" href="{{ route("admin.absent_devices.show", $value->id) }}">
                                                <i class="ph-scroll me-2"></i>
                                                Show
                                            </a>
                                        @endcan

                                        @can("devices-update")
                                            <a class="dropdown-item" href="{{ route("admin.absent_devices.edit", $value->id) }}">
                                                <i class="ph-pen me-2"></i>
                                                Edit
                                            </a>
                                        @endcan

                                        @can("devices-delete")
                                            <form action="{{ route("admin.absent_devices.destroy", $value->id) }}" method="POST">
                                                @csrf
                                                @method("delete")
                                                <button class="dropdown-item">
                                                    <i class="ph-trash me-2"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
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
