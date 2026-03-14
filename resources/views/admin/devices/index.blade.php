@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Device - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Device</a>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Device</h5>
            @can("devices-read")
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#export-modal">
                        <i class="ph-download-simple me-1"></i> Export CSV
                    </button>
                    @can("devices-create")
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#import-modal">
                            <i class="ph-upload-simple me-1"></i> Import CSV
                        </button>
                    @endcan
                </div>
            @endcan
        </div>

        <div class="card-header">
            List of All Registered Device.
        </div>

        <div id="filter" class="row mt-2 px-3">
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="locations" class="form-label fw-semibold">Locations</label>
                <select class="form-control select" data-placeholder="Select Locations" name="locations[]" id="locations"
                    multiple="multiple">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="buildings" class="form-label fw-semibold">Sub-location</label>
                <select class="form-control select" data-placeholder="Select Sub-location" name="buildings" id="buildings"
                    multiple="multiple">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="rooms" class="form-label fw-semibold">Location-id</label>
                <select class="form-control select" data-placeholder="Select Location-id" name="rooms" id="rooms"
                    multiple="multiple">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 col-sm-12 col-lg-3">
                <label for="device_types" class="form-label fw-semibold">Device Types</label>
                <select class="form-control select" data-placeholder="Select Device Types" name="device_types"
                    id="device_types" multiple="multiple">
                    <option></option>
                </select>
            </div>
        </div>

        <table id="datatable" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Device ID</th>
                    <th>Location</th>
                    <th>Sub-location</th>
                    <th>Location-id</th>
                    <th>Device Type</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- /basic datatable -->

    {{-- ------------------------------------------------------------------ --}}
    {{-- Export CSV Modal                                                     --}}
    {{-- ------------------------------------------------------------------ --}}
    @can("devices-read")
    <div id="export-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ph-download-simple me-2"></i>Export Devices to CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Exports all device data in the standard import-compatible format.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter by Location-id (Room) <span class="text-muted fw-normal">— leave blank to export all</span></label>
                        <select class="form-control" id="export-rooms" multiple="multiple"
                            data-placeholder="Select room(s) to export…">
                        </select>
                        <small class="text-muted">Type to search. Select one or more rooms, or leave blank for all.</small>
                    </div>

                    <div id="export-info" class="alert alert-info py-2 small mb-0" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="export-submit-btn" class="btn btn-primary">
                        <i class="ph-download-simple me-1"></i> Download CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- ------------------------------------------------------------------ --}}
    {{-- Import CSV Modal                                                     --}}
    {{-- ------------------------------------------------------------------ --}}
    @can("devices-create")
    <div id="import-modal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ph-upload-simple me-2"></i>Import Devices from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- Step 1: Template --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-1">Step 1 — Download the template</h6>
                        <p class="text-muted small mb-2">
                            Fill in one device per row. Use the reference sheet at the bottom of the template to look up valid IDs.
                        </p>
                        <a href="{{ route('admin.devices.import.template') }}" class="btn btn-light btn-sm mb-3">
                            <i class="ph-file-csv me-1"></i> Download Template
                        </a>

                        <table class="table table-sm table-bordered small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Required</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td><code>device_id</code></td><td>Yes</td><td>Unique device identifier, e.g. <code>wsid_b1120874-albuzrl1</code></td></tr>
                                <tr><td><code>sensor_id</code></td><td>Yes</td><td>Sensor identifier, e.g. <code>albuzrl1</code></td></tr>
                                <tr><td><code>device_type_id</code></td><td>Yes</td><td>Numeric ID from the Device Type reference sheet included in the template</td></tr>
                                <tr><td><code>branch</code></td><td>Yes</td><td>Location, e.g. <code>poc</code></td></tr>
                                <tr><td><code>building</code></td><td>Yes</td><td>Sub-location, e.g. <code>brinks</code></td></tr>
                                <tr><td><code>room</code></td><td>Yes</td><td>Location-id, e.g. <code>wsid_b1120874</code></td></tr>
                                <tr class="table-warning">
                                    <td><code>expr_expression</code></td><td>No</td>
                                    <td>
                                        Subscribe expressions separated by <code> | </code><br>
                                        e.g. <code>@{{value}} == 'wcamnok' | @{{value}} == 'wcamok'</code>
                                    </td>
                                </tr>
                                <tr class="table-warning">
                                    <td><code>expr_status_type_id</code></td><td>No</td>
                                    <td>Numeric status type ID per expression, separated by <code> | </code><br>
                                        Must match count of <code>expr_expression</code>. See reference sheet in template.</td>
                                </tr>
                                <tr class="table-warning">
                                    <td><code>expr_normal_state</code></td><td>No</td>
                                    <td>
                                        <code>off</code> = Trigger Warning, <code>on</code> = Normal State<br>
                                        Separated by <code> | </code>, e.g. <code>off | on</code>
                                    </td>
                                </tr>
                                <tr class="table-info">
                                    <td><code>action_label</code></td><td>No</td>
                                    <td>Host action button labels separated by <code> | </code><br>
                                        e.g. <code>capture</code></td>
                                </tr>
                                <tr class="table-info">
                                    <td><code>action_value</code></td><td>No</td>
                                    <td>Command values separated by <code> | </code><br>
                                        e.g. <code>cambymcc_@{{log_id}}</code></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-muted small mt-2 mb-0">
                            Leave all 5 expression/action columns blank if the device has no expressions or actions.
                        </p>
                    </div>

                    <hr>

                    {{-- Step 2: Upload --}}
                    <div>
                        <h6 class="fw-semibold mb-2">Step 2 — Upload your filled CSV</h6>
                        <input type="file" id="import-file" class="form-control" accept=".csv,.txt">
                        <small class="text-muted">Max 5 MB. UTF-8 encoding recommended.</small>
                    </div>

                    {{-- Result area (hidden until upload completes) --}}
                    <div id="import-result" class="mt-4" style="display: none;">
                        <div id="import-result-summary" class="alert" role="alert"></div>
                        <div id="import-result-errors" style="display: none;">
                            <h6 class="fw-semibold text-danger">Row errors:</h6>
                            <ul id="import-error-list" class="small text-danger ps-3 mb-0"></ul>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="import-submit-btn" class="btn btn-primary">
                        <i class="ph-upload-simple me-1"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection

@push("js")
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/buttons.min.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

            const exportOption = [0, 1, 2, 3, 4, 5];
            const buttons = [{
          //      extend: 'copyHtml5',
          //      className: 'btn btn-light',
          //      exportOptions: { columns: exportOption }
          //  }, {
                extend: 'excelHtml5',
                className: 'btn btn-light',
                exportOptions: { columns: exportOption },
                filename: function () { return getExportFilename('devices'); }
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: { columns: exportOption },
                filename: function () { return getExportFilename('devices'); }
          //  }, {
          //      extend: 'pdfHtml5',
          //      className: 'btn btn-light',
          //      exportOptions: { columns: exportOption },
          //      filename: function () { return getExportFilename('devices'); }
            }];

            const datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! route("admin.devices.index") !!}',
                    data: function(d) {
                        d.locations = $('#locations').val();
                        d.buildings = $('#buildings').val();
                        d.rooms = $('#rooms').val();
                        d.device_types = $('#device_types').val();
                        d.search = $('.form-control-feedback .form-control').val();
                        return d;
                    }
                },
                autoWidth: false,
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'device_id',
                        name: 'device_id'
                    },
                    {
                        data: 'branch',
                        name: 'branch'
                    },
                    {
                        data: 'building',
                        name: 'building'
                    },
                    {
                        data: 'room',
                        name: 'room'
                    },
                    {
                        data: 'device_type.name',
                        name: 'device_type.name'
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
                    targets: 6
                }],
                order: [
                    [1, 'desc']
                ]
            });

            $('#locations, #buildings, #rooms, #device_types').change(function() {
                datatable.draw();
            });

            $('input[type="search"]').change(function() {
                datatable.draw();
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

            $('#locations').select2({
                ajax: {
                    url: '{!! route("admin.devices.branches") !!}',
                    delay: 500,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        }
                    },
                    cache: true,
                },
                placeholder: 'Minimum input 3 characters to search.',
                minimumInputLength: 3,
            });

            $('#buildings').select2({
                ajax: {
                    url: '{!! route("admin.devices.buildings") !!}',
                    delay: 500,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        }
                    },
                    cache: true,
                },
                placeholder: 'Minimum input 3 characters to search.',
                minimumInputLength: 3,
            });

            $('#rooms').select2({
                ajax: {
                    url: '{!! route("admin.devices.rooms") !!}',
                    delay: 500,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        }
                    },
                    cache: true,
                },
                placeholder: 'Minimum input 3 characters to search.',
                minimumInputLength: 3,
            });

            // Export-modal room filter
            // Rooms are loaded once from the server when the modal opens and passed
            // directly to Select2 as static data — this ensures the dropdown shows
            // all rooms immediately and val() works reliably on download.
            $('#export-modal').on('shown.bs.modal', function () {
                $('#export-info').hide();

                $.ajax({
                    url: '{!! route("admin.devices.rooms") !!}',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    data: { search: '' },
                    success: function(data) {
                        // Destroy previous Select2 instance if it exists
                        if ($('#export-rooms').hasClass('select2-hidden-accessible')) {
                            $('#export-rooms').select2('destroy');
                        }

                        // Rebuild native <option> elements first
                        $('#export-rooms').empty();
                        data.forEach(function(r) {
                            $('#export-rooms').append(new Option(r.text, r.id, false, false));
                        });

                        // Init Select2 WITHOUT ajax — use the already-populated <option> list
                        $('#export-rooms').select2({
                            dropdownParent: $('#export-modal'),
                            placeholder: 'Select room(s) to export, or leave blank for all…',
                            allowClear: true,
                            width: '100%',
                        });
                    }
                });
            });

            // Clear selection when modal closes
            $('#export-modal').on('hidden.bs.modal', function () {
                if ($('#export-rooms').hasClass('select2-hidden-accessible')) {
                    $('#export-rooms').val(null).trigger('change');
                }
                $('#export-info').hide();
            });

            // Download CSV on button click
            $('#export-submit-btn').on('click', function() {
                // Read selected values directly from the underlying <select> element
                var selectedOptions = $('#export-rooms')[0].selectedOptions;
                var rooms = Array.from(selectedOptions).map(function(opt) { return opt.value; });

                var url = '{{ route("admin.devices.export.csv") }}';

                if (rooms.length > 0) {
                    url += '?' + rooms.map(function(r) {
                        return 'rooms[]=' + encodeURIComponent(r);
                    }).join('&');
                }

                $('#export-info')
                    .removeClass('alert-danger')
                    .addClass('alert-info')
                    .text('Preparing download…')
                    .show();

                var a = document.createElement('a');
                a.href = url;
                a.download = '';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);

                setTimeout(function() { $('#export-info').hide(); }, 2000);
            });

            $('#device_types').select2({
                ajax: {
                    url: '{!! route("admin.devices.device_types") !!}',
                    delay: 250,
                    dataType: 'json',
                    data: function(params) {
                        return {
                            search: params.term
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data,
                        }
                    },
                    cache: true,
                },
                placeholder: 'Minimum input 3 characters to search.',
                minimumInputLength: 3,
            });

            // ----------------------------------------------------------------
            // Import CSV
            // ----------------------------------------------------------------
            $('#import-submit-btn').on('click', function() {
                const fileInput = document.getElementById('import-file');
                if (!fileInput.files.length) {
                    alert('Please select a CSV file first.');
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Uploading…');

                $('#import-result').hide();
                $('#import-error-list').empty();

                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url:         '{{ route("admin.devices.import") }}',
                    type:        'POST',
                    data:        formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        var parts = [];
                        if (res.created > 0) parts.push(res.created + ' device(s) created');
                        if (res.updated > 0) parts.push(res.updated + ' device(s) updated');
                        if (res.skipped > 0) parts.push(res.skipped + ' row(s) skipped');

                        const summary = parts.length ? parts.join(', ') + '.' : 'No rows processed.';

                        const $resultArea = $('#import-result');
                        const $alert      = $('#import-result-summary');

                        $alert
                            .removeClass('alert-success alert-warning alert-danger')
                            .addClass(res.skipped > 0 && (res.created + res.updated) === 0
                                ? 'alert-danger'
                                : res.skipped > 0 ? 'alert-warning' : 'alert-success')
                            .text(summary);

                        $('#import-error-list').empty();
                        if (res.errors && res.errors.length) {
                            res.errors.forEach(function(e) {
                                $('#import-error-list').append('<li>' + e + '</li>');
                            });
                            $('#import-result-errors').show();
                        } else {
                            $('#import-result-errors').hide();
                        }

                        $resultArea.show();

                        if (res.created > 0 || res.updated > 0) {
                            $('#datatable').DataTable().draw();
                        }
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message ?? 'Upload failed. Check the file format and try again.';
                        $('#import-result-summary')
                            .removeClass('alert-success alert-warning')
                            .addClass('alert-danger')
                            .text(msg);
                        $('#import-result').show();
                        $('#import-result-errors').hide();
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html('<i class="ph-upload-simple me-1"></i> Import');
                        fileInput.value = '';
                    }
                });
            });

            // Clear result when modal is closed / reopened
            $('#import-modal').on('hidden.bs.modal', function() {
                $('#import-result').hide();
                $('#import-error-list').empty();
                $('#import-file').val('');
            });
        });
    </script>
@endpush