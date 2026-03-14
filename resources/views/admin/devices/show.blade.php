@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Device - <span class="fw-normal">Detail</span>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="{{ route("admin.devices.index") }}">Device</a>
                    <span class="breadcrumb-item active">{{ $data->device_id }}</span>
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
    <div class="row">
        <div class="col-xl-8 col-12">
            <div class="card shadow-none">
                <div class="card-header">
                    <h5 class="mb-0">Detail Device</h5>
                </div>

                <div class="card-body border-top">
                    <div class="row g-lg-5 g-2">
                        <div class="col-12">
                            <form action="{{ route("admin.devices.store") }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Device ID</label>
                                    <div class="col-lg-8">
                                        <input disabled class="form-control" value="{{ $data->device_id }}" name="device_id"
                                            placeholder="Type Device ID" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Sensor ID</label>
                                    <div class="col-lg-8">
                                        <input disabled class="form-control" value="{{ $data->sensor_id }}" name="sensor_id"
                                            placeholder="Type Sensor ID" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Location</label>
                                    <div class="col-lg-8">
                                        <input disabled value="{{ old("branch", $data->branch) }}" class="form-control" name="branch"
                                            placeholder="Type Location" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Sub-Location</label>
                                    <div class="col-lg-8">
                                        <input disabled value="{{ old("building", $data->building) }}" class="form-control" name="building"
                                            placeholder="Type Sub-Location" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Location ID</label>
                                    <div class="col-lg-8">
                                        <input disabled value="{{ old("room", $data->room) }}" class="form-control" name="room"
                                            placeholder="Type Location ID" required type="text">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-lg-4 col-form-label">Device Type</label>
                                    <div class="col-lg-8">
                                        <select disabled class="form-control select" data-placeholder="Select Device Type"
                                            name="device_type_id">
                                            <option></option>
                                            @foreach ($device_types as $device_type)
                                                <option {{ $data->device_type_id == $device_type->id ? "selected" : "" }}
                                                    value="{{ $device_type->id }}">{{ $device_type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="subscribe_expression">
                                    <div class="text-start">
                                        <button class="btn btn-light" type="button">Add Device
                                            Expression</button>
                                    </div>
                                </div>
                                <hr>
                                <div class="publish_button">
                                    <div class="text-start">
                                        <button class="btn btn-light" type="button">Add Host Action Button
                                            Action</button>
                                    </div>
                                </div>
                                <hr>
                                @can("devices-update")
                                    <div class="text-end">
                                        <a href="{{ route("admin.devices.edit", $data->id) }}" class="btn btn-primary"
                                            type="submit">Edit</a>
                                    </div>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-12">
            <div class="card shadow-none">

                <div class="card-header">
                    <div class="d-flex flex-lg-row flex-column gap-2 justify-content-between">
                        List of All Device Logs.
                        <div class="">
                            <input type="text" class="form-control datepicker-basic @error('date') is-invalid @enderror"
                                placeholder="Pick Start & End Date" name="date">
                        </div>
                    </div>
                </div>

                <table id="datatable" class="table" data-id="{{ $data->id }}">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Command</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="publish_html" style="display: none;">
        <div class="publish_element">
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label number">1. </label>
            </div>
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label">Action Label</label>
                <div class="col-lg-8">
                    <input disabled class="form-control" name="publish_actions[label][]" placeholder="Type Action Label"
                        required type="text">
                </div>
            </div>
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label">Action Comment</label>
                <div class="col-lg-8">
                    <input disabled class="form-control" name="publish_actions[value][]" placeholder="Type Action Comment"
                        required type="text">
                </div>
            </div>
            <div class="text-end mt-3">
                <button data-id="" class="btn btn-danger" type="button" onclick="handlePublish(this)">
                    Test Host Action
                </button>
            </div>
        </div>
    </div>

    <div class="subscribe_html" style="display: none;">
        <div class="subscribe_element">
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label number">1. </label>
            </div>
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label">Expression</label>
                <div class="col-lg-8">
                    <input disabled class="form-control" name="subscribe_expressions[expression][]"
                        placeholder="Type Expression, e.g : &#123;&#123;value&#125;&#125; > 10" required type="text">
                </div>
            </div>
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label">Status Type</label>
                <div class="col-lg-8">
                    <select disabled class="form-control select2modify" data-placeholder="Select Status Type"
                        name="subscribe_expressions[status_type][]">
                        <option></option>
                        @foreach ($status_types as $status_type)
                            <option value="{{ $status_type->id }}">{{ $status_type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <label class="col-lg-4 col-form-label">Normal State</label>
                <div class="col-lg-8 d-flex align-items-center">
                    <select disabled class="form-control select2modify" data-placeholder="Select State"
                        name="subscribe_expressions[normal_state][]">
                        <option></option>
                        <option value="on">NORMAL STATE</option>
                        <option value="off">TRIGGER WARNING</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @include("admin.components.modals.publish")
    @include("admin.components.modals.open-note")
    @include("admin.components.modals.create-note")
@endsection

@push("js")
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/pdfmake.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/pdfmake/vfs_fonts.min.js") }}"></script>
    <script src="{{ asset("assets/js/vendor/tables/datatables/extensions/buttons.min.js") }}"></script>

    <script>
        const model = @json($data);
        const dataSubscribeExpressions = model.subscribe_expression;
        const dataPublishActions = model.publish_action;

        $(document).ready(function() {
            if (dataSubscribeExpressions.length > 0) {
                dataSubscribeExpressions.forEach((item, index) => {
                    hanldeAddSubsribeExpression();
                    // console.log(item.normal_state)
                    $(`.subscribe_expression_${index + 1} select[name="subscribe_expressions[normal_state][]`)
                        .val(item.normal_state ? "on" : "off").change();
                    $(`.subscribe_expression_${index + 1} input[name="subscribe_expressions[expression][]"]`)
                        .val(item.expression);
                    $(`.subscribe_expression_${index + 1} select[name="subscribe_expressions[status_type][]"]`)
                        .val(item.status_type_id).change();
                })
            }
            if (dataPublishActions.length > 0) {
                dataPublishActions.forEach((item, index) => {
                    handleAddPublishButtonAction();
                    $(`.publish_button_${index + 1} input[name="publish_actions[label][]"]`).val(item
                        .label);
                    $(`.publish_button_${index + 1} input[name="publish_actions[value][]"]`).val(item
                        .value);
                    $(`.publish_button_${index + 1} .btn-danger`).attr('data-id', item.id);
                })
            }
        })

        let subscribeExpressionCount = 0;
        let publishActionsCount = 0;

        const hanldeAddSubsribeExpression = () => {
            const html = $('.subscribe_html').html();
            const htmlAppend = $(html).clone();

            // Add Number
            htmlAppend.find('.number').text(`${subscribeExpressionCount + 1}. `);
            htmlAppend.show();

            // Delete Logic
            htmlAppend.addClass(`subscribe_expression_${subscribeExpressionCount + 1}`);
            htmlAppend.find('.btn-danger').attr('data-subscribe_expression', subscribeExpressionCount + 1);

            // Count Logic
            subscribeExpressionCount++;

            // Select2
            htmlAppend.find('.select2modify').select2();

            $('.subscribe_expression').append(htmlAppend);
        }

        const handleDeleteSubscribeExpression = (el) => {
            const subscribeExpression = $(el).data('subscribe_expression');
            $(`.subscribe_expression_${subscribeExpression}`).remove();
        }

        const handleAddPublishButtonAction = () => {
            const html = $('.publish_html').html();
            const htmlAppend = $(html).clone();

            // Add Number
            htmlAppend.find('.number').text(`${publishActionsCount + 1}. `);
            htmlAppend.show();

            // Delete Logic
            htmlAppend.addClass(`publish_button_${publishActionsCount + 1}`);
            htmlAppend.find('.btn-danger').attr('data-publish_actions', publishActionsCount + 1);

            // Count Logic
            publishActionsCount++;

            // Select2
            htmlAppend.find('.select2modify').select2();

            $('.publish_button').append(htmlAppend);
        }

        const handleDeletePublishActions = (el) => {
            const publishActions = $(el).data('publish_actions');
            $(`.publish_button_${publishActions}`).remove();
        }

        const handlePublish = (el) => {
            $.ajax({
                url: '/admin/devices/publish',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: $(el).data('id'),
                    is_testing: true,
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function(error) {
                    // console.log(error);
                }
            })
        }
    </script>

    <script type="text/javascript">
        let url = "{!! route("admin.devices.show", ":device_id") !!}";
        let _datatable;

        $(document).ready(function() {
            const exportOption = [0, 1, 2];
            const buttons = [{
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
            }, ];

            url = url.replace(':device_id', $('#datatable').data('id'));

            const datatable = $('#datatable');
            _datatable = datatable.DataTable({
                processing: true,
                serverSide: true,
                ajax: url,
                autoWidth: false,
                dom: '<"datatable-header"fBl><"datatable-scroll"t><"datatable-footer"ip>',
                buttons,
                columns: [{
                        data: {
                            '_': 'created_at.display',
                            'sort': 'created_at.timestamp'
                        },
                        name: 'created_at'
                    },
                    {
                        data: 'command',
                        name: 'device_log.value'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'location',
                        name: 'device.branch'
                    },
                    {
                        data: 'options',
                        name: 'options',
                        class: 'text-center'
                    }
                ],
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: 2
                }, {
                    orderable: false,
                    targets: [4]
                }],
                order: []
            });

            datatable.delegate('.open-note', 'click', function(e) {
                let id = $(this).data('id');

                let url = "{!! route("admin.device_status.get_device_status", ":id") !!}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        const data = response.data;

                        $('#open-note #device-id').text(data.device.device_id);
                        $('#open-note #device-note').text(data.notes);
                    },
                    error: function(error) {
                        // console.log(error);
                    }
                });
            });

            datatable.delegate('.create-note', 'click', function(e) {
                let id = $(this).data('id');

                let url = "{!! route("admin.device_status.get_device_status", ":id") !!}";
                url = url.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        const data = response.data;

                        $('#create-note #device-id').text(data.device.device_id);
                        $('#create-note #device_status_id').val(data.id);
                        $('#create-note #notes').val(data.notes);
                        data.marked_as_read ?
                            $('#create-note #marked_as_read').attr('checked', 'checked') :
                            $('#create-note #marked_as_read').removeAttr('checked');
                    },
                    error: function(error) {
                        // console.log(error);
                    }
                });
            });

            $('#create-note-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(e.target);

                $.ajax({
                    url: '{!! route("admin.device_status.notes") !!}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // @TODO : Change Icon to marked
                        //
                        //

                        alert(response.message);
                        _datatable.draw();
                    },
                    error: function(error) {
                        alert("Something went wrong!");
                        // console.log(error);
                    }
                });
            });

            datatable.delegate('.publish', 'click', function(e) {
                let publishActionId = $(this).data('publishActionId');
                let deviceStatusId = $(this).data('deviceStatusId');

                $('#id').val(publishActionId);
                $('#device_status_id').val(deviceStatusId);

                let url = "{!! route("admin.device_status.get_device_status", ":id") !!}";
                url = url.replace(':id', deviceStatusId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        const data = response.data;

                        $('#publish #device-id').text(data.device.device_id);
                    },
                    error: function(error) {
                        // console.log(error);
                    }
                });
            });

            $('#publish-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(e.target);

                $.ajax({
                    url: '{!! route("admin.devices.publish") !!}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // @TODO : Change Icon to marked
                        //
                        //

                        alert(response.message);
                        _datatable.draw();
                    },
                    error: function(error) {
                        alert("Something went wrong!");
                        // console.log(error);
                    }
                });
            });
        });
    </script>

    @php
        $oldDate = old('date');
        $dates = $oldDate ? explode(' - ', $oldDate) : null;
        $startDate = $oldDate ? $dates[0] : now()->startOf('hour')->format('Y-m-d H:i:s');
        $endDate = $oldDate ? $dates[1] : now()->startOf('hour')->add(32, 'hour')->format('Y-m-d H:i:s');
    @endphp

    <script>
        const startDate = '{{ $startDate }}';
        const endDate = '{{ $endDate }}';
        $('.datepicker-basic').daterangepicker({
            timePicker: true,
            drops: 'auto',
            showDropdowns: true,
            startDate: moment(startDate),
            endDate: moment(endDate),
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            _datatable.ajax.url(
                url
                    + "?date=" 
                    + picker.startDate.format('YYYY-MM-DD HH:mm:ss') 
                    + " - " 
                    + picker.endDate.format('YYYY-MM-DD HH:mm:ss')
            ).load();
        });
    </script>
@endpush
