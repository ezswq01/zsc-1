@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Role - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Role</a>
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
            <h5 class="mb-0">Role</h5>
        </div>

        <div class="card-header">
            List of All Registered Role.
        </div>

        <table id="datatable" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th class="text-center">Actions</th>
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
            const exportOption = [0, 1];
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
                    return getExportFilename('roles')
                },
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('roles')
                },
            }, {
                extend: 'pdfHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('roles')
                },
            }, ];

            const datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.roles.index') !!}',
                autoWidth: false,
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'permissions',
                        name: 'permissions'
                    },
                    {
                        data: 'options',
                        name: 'options',
                        class: 'text-center'
                    },
                ],
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: 0
                }, {
                    orderable: false,
                    targets: [2, 3]
                }],
                order: []
            });
        });
    </script>
@endpush
