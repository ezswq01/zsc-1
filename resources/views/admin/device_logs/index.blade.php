@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Log and Report - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Log and Report</a>
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

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Log and Report</h5>
        </div>

        <div class="card-header">
            List of All Device Logs.
        </div>

        <table id="datatable" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Device ID</th>
                    <th>Command</th>
                    <th>Type</th>
                    <th>Time</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection

@push('js')
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/buttons.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            const exportOption = [0, 1, 2, 3];
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
                    return getExportFilename('device_logs')
                },
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('device_logs')
                },
            }, {
                extend: 'pdfHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('device_logs')
                },
            }, ];

            const datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.device_logs.index') !!}',
                autoWidth: false,
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'device_id',
                        name: 'device.device_id'
                    },
                    {
                        data: 'value',
                        name: 'value'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: {
                            '_': 'created_at.display',
                            'sort': 'created_at.timestamp'
                        },
                        name: 'created_at'
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: 0
                }],
                order: []
            });
        });
    </script>
@endpush
