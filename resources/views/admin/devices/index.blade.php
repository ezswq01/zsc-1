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
        <div class="card-header">
            <h5 class="mb-0">Device</h5>
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
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
    <!-- /basic datatable -->
@endsection

@push("js")
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/buttons.min.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

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
                    return getExportFilename('devices')
                },
            }, {
                extend: 'csvHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('devices')
                },
            }, {
                extend: 'pdfHtml5',
                className: 'btn btn-light',
                exportOptions: {
                    columns: exportOption
                },
                filename: function() {
                    return getExportFilename('devices')
                },
            }, ];

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
                    targets: 5
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
        });
    </script>
@endpush
