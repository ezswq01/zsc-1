@extends('admin.layout.main')

@push('header')
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Device - <span class="fw-normal">All</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
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

@section('content')
    <!-- Basic datatable -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Device</h5>
        </div>

        <div class="card-header">
            List of All Registered Device.
        </div>

        <div id="filter" class="row mt-2 px-3">
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="branch" class="form-label fw-semibold">Branch</label>
                <select class="form-control select" data-placeholder="Select Branch" name="branch" id="branch">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="building" class="form-label fw-semibold">Building</label>
                <select class="form-control select" data-placeholder="Select Building" name="building" id="building">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 mb-3 col-sm-12 col-lg-3">
                <label for="room" class="form-label fw-semibold">Room</label>
                <select class="form-control select" data-placeholder="Select Room" name="room" id="room">
                    <option></option>
                </select>
            </div>
            <div class="mb-lg-0 col-sm-12 col-lg-3">
                <label for="device_type" class="form-label fw-semibold">Device Type</label>
                <select class="form-control select" data-placeholder="Select Device Type" name="device_type"
                    id="device_type">
                    <option></option>
                </select>
            </div>
        </div>

        <table id="datatable" class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Device ID</th>
                    <th>Subscribe Topic</th>
                    <th>Publish Topic</th>
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
                    url: '{!! route('admin.devices.index') !!}',
                    data: function(d) {
                        d.branch = $('#branch').val();
                        d.building = $('#building').val();
                        d.room = $('#room').val();
                        d.device_type = $('#device_type').val();
                        d.search = $('input[type="search"]').val();
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
                        data: 'subscribe_topic',
                        name: 'subscribe_topic'
                    },
                    {
                        data: 'publish_topic',
                        name: 'publish_topic'
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
                    targets: 4
                }],
                order: []
            });

            $('#branch, #building, #room, #device_type').change(function() {
                datatable.draw();
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

            $('#branch').select2({
                allowClear: true,
                ajax: {
                    url: '{!! route('admin.devices.branches') !!}',
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
            });

            $('#building').select2({
                allowClear: true,
                ajax: {
                    url: '{!! route('admin.devices.buildings') !!}',
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
            });

            $('#room').select2({
                allowClear: true,
                ajax: {
                    url: '{!! route('admin.devices.rooms') !!}',
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
            });

            $('#device_type').select2({
                allowClear: true,
                ajax: {
                    url: '{!! route('admin.devices.device_types') !!}',
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
            });
        });
    </script>
@endpush
