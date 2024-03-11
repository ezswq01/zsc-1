@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Device Statuses - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Device Statuses</a>
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
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Device Statuses</h5>
        </div>

        <div class="card-header">
            List of All Device Logs.
        </div>

        <div style="overflow-x:auto">
            <table id="datatable" class="table text-nowrap">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Device ID</th>
                        <th>Status</th>
                        <th>Locations</th>
                        <th>Sub Location</th>
                        <th>Location-id</th>
                        <th>Notes</th>
                        <th>Marked as Normal</th>
                        <th>Noted</th>
                        <th>Updated By</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- /basic datatable -->
@endsection

@push("js")
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/buttons.min.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let data = @json($device_statuses);
            data = data.map((item) => ({
                ...item,
                user_name: item?.user?.name || "-",
            }))
            const exportOption = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            const buttons = [{
                extend: 'copyHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                }
            }, {
                extend: 'excelHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('device_statuses')
                },
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('device_statuses')
                },
            }, {
                extend: 'pdfHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('device_statuses')
                },
            }];
            $(`#datatable`).DataTable({
                data: data,
                order: [
                    [0, "desc"]
                ],
                columnDefs: [{
                    targets: [0, 1, 2, 3, 4],
                    className: "align-middle",
                }],
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [{
                        data: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format("YYYY-MM-DD HH:mm:ss");
                        },
                    },
                    {
                        data: "device",
                        render: function(data, type, row) {
                            return data?.device_id ? data?.device_id : "-";
                        },
                    },
                    {
                        data: "marked_as_read",
                        render: function(data, type, row) {
                            return `
                                <div id="mark_${row.id}">
                                    ${
                                        data
                                            ? `<i class="ph-check-circle text-success"></i>`
                                            : `<i class="ph-question text-danger"></i>`
                                    }
                                </div>
                            `;
                        },
                    },
                    {
                        data: "device",
                        render: function(data, type, row) {
                            return data?.branch;
                        },
                    },
                    {
                        data: "device",
                        render: function(data, type, row) {
                            return data?.building;
                        },
                    },
                    {
                        data: "device",
                        render: function(data, type, row) {
                            return data?.room;
                        },
                    },
                    {
                        data: "notes",
                        render: function(data, type, row) {
                            return data;
                        },
                    },
                    {
                        data: "marked_as_read",
                        render: function(data, type, row) {
                            return `
                                <div class="form-check form-switch">
                                    ${
                                        data ? 
                                        `<span class="badge bg-success">TRUE</span>` : 
                                        `<span class="badge bg-danger">FALSE</span>`
                                    }
                                </div>
                            `;
                        }
                    },
                    {
                        data: "noted",
                        render: function(data, type, row) {
                            return `
                                <div class="form-check form-switch">
                                    ${
                                        data ? 
                                        `<span class="badge bg-success">TRUE</span>` : 
                                        `<span class="badge bg-danger">FALSE</span>`
                                    }
                                </div>
                            `;
                        }
                    },
                    {
                        data: "user_name",
                        render: function(data, type, row) {
                            return data;
                        },
                    },
                    {
                        data: "updated_at",
                        render: function(data, type, row) {
                            return moment(data).format("YYYY-MM-DD HH:mm:ss");
                        },
                    }
                ],
            });
        });
    </script>
@endpush
