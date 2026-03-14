@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Location - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Location</a>
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
    <div class="card shadow-none">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Location</h5>
            <div class="d-flex gap-2">
                @can('locations-read')
                    <button class="btn btn-light btn-sm" id="btn-export-csv">
                        <i class="ph-download-simple me-1"></i> Export CSV
                    </button>
                @endcan
                @can('locations-create')
                    <a href="{{ route('admin.locations.import-template') }}" class="btn btn-light btn-sm">
                        <i class="ph-file-arrow-down me-1"></i> Download Template
                    </a>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#import-modal">
                        <i class="ph-upload-simple me-1"></i> Import CSV
                    </button>
                    <!-- <a href="{{ route('admin.locations.create') }}" class="btn btn-primary btn-sm"> -->
                    <!--     <i class="ph-plus me-1"></i> Add Location -->
                    <!-- </a> -->
                @endcan
            </div>
        </div>

        <div class="card-header">
            List of All Registered Locations.
        </div>

        <table id="datatable" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Company Name</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Coordinate</th>
                    <th class="text-center">Status</th>
                    <th>Last Updated</th>
                    <th>Updated By</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- /basic datatable -->

    <!-- =====================================================================
         Import Modal
    ====================================================================== -->
    @can('locations-create')
    <div class="modal fade" id="import-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Locations CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Upload a CSV file to bulk create or update locations.
                        <strong>Upsert key: <code>code</code></strong> — existing records with the same code will be updated; new codes will be inserted.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="import-file" accept=".csv,.txt">
                    </div>
                    <div id="import-result" class="d-none">
                        <hr>
                        <div id="import-summary"></div>
                        <div id="import-errors" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-import-submit">
                        <span id="btn-import-text"><i class="ph-upload-simple me-1"></i> Upload & Import</span>
                        <span id="btn-import-loading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-1"></span> Importing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection

@push('js')
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/tables/datatables/extensions/buttons.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            'use strict';

            const exportOption = [0, 1, 2, 3, 4, 5, 6, 7, 8];
            const buttons = [{
          //      extend: 'copyHtml5',
          //      className: 'btn btn-light',
          //      exportOptions: { columns: exportOption }
          //  }, {
                extend: 'excelHtml5',
                className: 'btn btn-light',
                exportOptions: { columns: exportOption },
                filename: function () { return getExportFilename('locations'); }
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: { columns: exportOption },
                filename: function () { return getExportFilename('locations'); }
          //  }, {
          //      extend: 'pdfHtml5',
          //      className: 'btn btn-light',
          //      exportOptions: { columns: exportOption },
          //      filename: function () { return getExportFilename('locations'); }
            }];

            const datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('admin.locations.index') !!}',
                autoWidth: false,
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [
                    { data: 'DT_RowIndex',     name: 'DT_RowIndex' },
                    { data: 'code',            name: 'code' },
                    { data: 'company_name',    name: 'company_name' },
                    { data: 'name',            name: 'name' },
                    { data: 'city',            name: 'city' },
                    { data: 'coordinate',      name: 'coordinate' },
                    { data: 'is_active',       name: 'is_active', class: 'text-center' },
                    { data: 'last_updated_at', name: 'last_updated_at' },
                    { data: 'last_updated_by', name: 'last_updated_by' },
                    { data: 'options',         name: 'options', class: 'text-center' },
                ],
                columnDefs: [
                    { orderable: false, searchable: false, targets: 0 },
                    { orderable: false, targets: 9 },
                ],
                order: [[1, 'asc']]
            });

            // -----------------------------------------------------------------
            // Export CSV — server-side streaming
            // -----------------------------------------------------------------
            $('#btn-export-csv').on('click', function () {
                window.location.href = '{!! route('admin.locations.export-csv') !!}';
            });

            // -----------------------------------------------------------------
            // Import CSV
            // -----------------------------------------------------------------
            $('#import-modal').on('hidden.bs.modal', function () {
                $('#import-file').val('');
                $('#import-result').addClass('d-none');
                $('#import-summary').html('');
                $('#import-errors').html('');
            });

            $('#btn-import-submit').on('click', function () {
                const file = $('#import-file')[0].files[0];
                if (!file) {
                    alert('Please select a CSV file first.');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Show loading state
                $('#btn-import-text').addClass('d-none');
                $('#btn-import-loading').removeClass('d-none');
                $('#btn-import-submit').prop('disabled', true);
                $('#import-result').addClass('d-none');

                $.ajax({
                    url: '{{ route('admin.locations.import') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        $('#import-result').removeClass('d-none');
                        $('#import-summary').html(
                            '<div class="alert alert-success py-2 mb-2">' +
                            '<strong>Import complete.</strong> ' +
                            'Created: <strong>' + res.created + '</strong> &nbsp;|&nbsp; ' +
                            'Updated: <strong>' + res.updated + '</strong> &nbsp;|&nbsp; ' +
                            'Skipped: <strong>' + res.skipped + '</strong>' +
                            '</div>'
                        );

                        if (res.errors && res.errors.length > 0) {
                            let errorHtml = '<div class="alert alert-warning py-2"><strong>Warnings:</strong><ul class="mb-0 mt-1">';
                            res.errors.forEach(function (e) {
                                errorHtml += '<li class="small">' + e + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#import-errors').html(errorHtml);
                        }

                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        $('#import-result').removeClass('d-none');
                        let msg = 'Import failed.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg += ' ' + xhr.responseJSON.message;
                        }
                        $('#import-summary').html('<div class="alert alert-danger py-2">' + msg + '</div>');
                    },
                    complete: function () {
                        $('#btn-import-text').removeClass('d-none');
                        $('#btn-import-loading').addClass('d-none');
                        $('#btn-import-submit').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush