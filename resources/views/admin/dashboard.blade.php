@extends("admin.layout.main")

@push("header")
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    Dashboard
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
            <div class="d-flex flex-fill w-xl-75 w-100">
                <div class="breadcrumb py-2">
                    <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
                    <a class="breadcrumb-item" href="#">Dashboard</a>
                </div>
                <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
                    data-bs-toggle="collapse" href="#breadcrumb_elements">
                    <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
                </a>
            </div>
            <div class="d-flex flex-fill flex-shrink-1 ms-auto py-2 bg-white">
                <select class="form-control select" data-placeholder="All Locations" name="locations" id="locations"
                    multiple="multiple">
                    <option></option>
                    @foreach ($device_locations as $device_location)
                        @php
                            $selected = in_array($device_location->branch, request()->locations ?? []) ? "selected" : "";
                        @endphp
                        <option {{ $selected }} value="{{ $device_location->branch }}">
                            {{ ucfirst($device_location->branch) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endpush

@section("content")
    <div class="row gx-3">
        <div class="col-lg-3 col-12">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h3 class="mb-0 absent_door_request_qty">
                            {{ $absent_received_logs->where("marked_as_read", false)->count() }}
                        </h3>
                        <button onclick="toggleTable('absent-doors')" type="button" class="btn btn-white p-1">
                            <i class="ph-table"></i>
                        </button>
                    </div>
                    <div>
                        Door Request
                    </div>
                </div>
            </div>
        </div>
        @foreach ($status_types as $status_type)
            <div class="col-lg-3 col-12">
                <div class="card text-white" style="background-color: {{ $status_type->color }};">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 class="mb-0">
                                {{ $status_type->count }}
                            </h3>
                            <button onclick="toggleTable('{{ $status_type->widget_id }}')" type="button"
                                class="btn btn-white p-1">
                                <i class="ph-table"></i>
                            </button>
                        </div>
                        <div>
                            {{ $status_type->name }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row gx-3">
        <div class="col-12">
            <button onclick="$('.table-component').hide()" class="btn btn-primary mb-3">
                Hide All Table
            </button>
        </div>
        <div class="col-12 table-component" id="absent-doors">
            <div class="mb-3">
                <div class="bg-white p-4">
                    <h6>User Door Absent Request</h6>
                    <table class="table absent_received_logs">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Device ID</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="open_absent_device" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
        @foreach ($status_type_widgets as $status_type_widget)
            <div class="col-12 table-component" id="{{ $status_type_widget->id }}">
                <div class="mb-3">
                    <div class="bg-white p-4">
                        <h6>{{ $status_type_widget->status_type->name }}</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Device ID</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="open_note" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="create_note" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>
            <div id="publish_action" class="publish_action modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body"></div>
                        <div class="modal-footer"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push("js")
    <script src="/js/app.js"></script>
    <script src="/assets_2/js/dashboard.js"></script>

    {{-- Absent Door --}}
    <script>
        let absent_device_logs = @json($absent_received_logs);
        let status_type_widgets = @json($status_type_widgets);

        $(document).ready(function() {
            initDatatableAbsent(absent_device_logs)
            initDatatableStatusType(status_type_widgets)
        });

        function handleOpenModalAbsent(device_id) {
            const item = absent_device_logs.find(item => item.id == device_id)

            const modalEl = $("#open_absent_device")

            modalEl.find(".modal-title").html(
                `
                  RESPOND REQUEST - ${item.absent_device.absent_device_id}
                `
            )

            modalEl.find(".modal-body").html(
                `
                  <h6 class="fw-semibold">Notes</h6>
                  <textarea ${item.marked_as_read && "disabled"} class="form-control">${
                      item.notes ? item.notes : ""
                  }</textarea>
                `
            )

            modalEl.find(".modal-footer").html(
                `
                  <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                  ${
                      !item.marked_as_read
                      ? `<button type="button" class="btn btn-primary" onclick="handleOpenDoor('${item.id}')">Submit</button>`
                      : ""
                  }
                `
            )
        }

        function handleOpenModalNote(status_type_widget_id, device_status_id) {
            const item = status_type_widgets.find((item) => item.id == status_type_widget_id)
            const device_status = item.status_type?.device_status?.find((item) => item.id == device_status_id)

            const modalEl = $(`#open_note`)

            modalEl.find(".modal-title").html(
                `
                  OPEN NOTE - Device : ${device_status.device.device_id}
                `
            )

            modalEl.find(".modal-body").html(
                `
                  <h6 class="fw-semibold">Notes</h6>
                  <p>${device_status.notes ? device_status.notes : ""}</p>
                `
            )

            modalEl.find(".modal-footer").html(
                `
                  <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                `
            )
        }

        function handleCreateModalNote(status_type_widget_id, device_status_id) {
            const item = status_type_widgets.find((item) => item.id == status_type_widget_id)
            const device_status = item.status_type?.device_status?.find((item) => item.id == device_status_id)

            const modalEl = $(`#create_note`)

            modalEl.find(".modal-title").html(
                `
                  CREATE NOTE - Device : ${device_status.device.device_id}
                `
            )

            modalEl.find(".modal-body").html(
                `
                  <h6 class="fw-semibold">Notes</h6>
                  <textarea class="form-control">${device_status.notes ? device_status.notes : ""}</textarea>
                  <div class="d-flex gap-2 mt-3">
                      <label for="marked_${device_status_id}">Set as marked?</label>
                      <input 
                        type="checkbox"
                        ${device_status.marked_as_read ? "checked" : ""}
                        class="form-check-input" id="marked_${device_status_id}"
                      >
                  </div>
                `
            )

            modalEl.find(".modal-footer").html(
                `
                  <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" onclick="handleSubmitNotes('${device_status_id}')">Submit</button>
                `
            )
        }

        function handleOpenDoor(absent_device_received_log_id) {
            if (!confirm('Are you sure want to publish?')) return false;

            const textarea = $(`#open_absent_device textarea`).val()

            $.ajax({
                url: '/admin/absent_devices/publish',
                type: 'POST',
                data: {
                    notes: textarea,
                    _token: '{{ csrf_token() }}',
                    absent_device_received_log_id: absent_device_received_log_id,
                },
                success: function(response) {
                    $(`.absent_badge_${absent_device_received_log_id}`).html(`
                        <span class="badge bg-success">Opened</span>
                    `);

                    $(`#open_absent_device textarea`).prop(
                        'disabled', true
                    );

                    absent_device_logs = absent_device_logs.map(item => {
                        if (item.id == absent_device_received_log_id) {
                            return {
                                ...item,
                                marked_as_read: true,
                                status: "Open",
                                notes: textarea,
                            }
                        }
                        return {
                            ...item
                        }
                    })

                    const absent_device_logs_requested = absent_device_logs.filter(
                        item => item.status != "Open"
                    )
                    $(`.absent_door_request_qty`).html(absent_device_logs_requested.length)

                    alert(response.message);
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

        function handleSubmitNotes(device_status_id) {
            if (!confirm('Are you sure want to submit this notes?')) return false;
            const textarea = $(`#create_note textarea`).val()
            const is_checked = $(`#marked_${device_status_id}`).is(':checked')

            $.ajax({
                url: '/admin/device_status/notes',
                type: 'POST',
                data: {
                    notes: textarea,
                    _token: '{{ csrf_token() }}',
                    device_status_id: device_status_id,
                    marked_as_read: is_checked,
                },
                success: function(response) {
                    const status_type_widget_id = response.status_type_widget.id
                    status_type_widgets = status_type_widgets.map(item => {
                        if (item.id == status_type_widget_id) {
                            return {
                                ...item,
                                status_type: {
                                    ...item.status_type,
                                    device_status: item.status_type.device_status.map(item => {
                                        if (item.id == device_status_id) {
                                            return {
                                                ...item,
                                                notes: textarea,
                                                marked_as_read: is_checked,
                                            }
                                        }
                                        return {
                                            ...item
                                        }
                                    })
                                }
                            }
                        }
                        return {
                            ...item
                        }
                    })

                    if (is_checked) {
                        $(`#mark_${device_status_id}`).html('<i class="ph-check-circle text-success"></i>');
                    } else {
                        $(`#mark_${device_status_id}`).html('<i class="ph-question text-danger"></i>');
                    }

                    alert(response.message);
                },
                error: function(error) {
                    alert("Something went wrong!");
                    console.log(error);
                }
            })
        }

        function handlePublishModalNote(status_type_widget_id, device_status_id, publish_action_id) {
            const item = status_type_widgets.find((item) => item.id == status_type_widget_id)
            const device_status = item.status_type?.device_status?.find((item) => item.id == device_status_id)

            const modalEl = $(`#publish_action`)

            modalEl.find(".modal-title").html(
                `
                    PUBLISH NOTE - Device : ${device_status.device.device_id}
                `
            )

            modalEl.find(".modal-body").html(
                `
                    <h6 class="fw-semibold">Notes</h6>
                    <textarea class="form-control">${device_status.notes ? device_status.notes : ""}</textarea>
                `
            )

            modalEl.find(".modal-footer").html(
                `
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="handlePublishAction(${device_status_id}, ${publish_action_id})">
                        Submit
                    </button>
                `
            )
        }

        function handlePublishAction(device_status_id, publish_action_id) {
            if (!confirm('Are you sure want to publish?')) return false;
            const textarea = $(`#publish_action textarea`).val()
            console.log(device_status_id, publish_action_id)
            console.log(textarea)

            $.ajax({
                url: '/admin/devices/publish',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: publish_action_id,
                    device_status_id: device_status_id,
                    notes: textarea,
                },
                success: function(response) {
                    $(`#mark_${device_status_id}`).html('<i class="ph-check-circle text-success"></i>');
                    alert(response.message);
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

        window.Echo.channel('laravel_database_new-data-channel').listen('.new-data-event', (e) => {
            const item = e.message;

            if (item.type == "absent_device") {

                absent_device_logs = [
                    item.data,
                    ...absent_device_logs
                ]

                const absent_device_logs_requested = absent_device_logs.filter(
                    item => item.status != "Open"
                )
                $(`.absent_door_request_qty`).html(absent_device_logs_requested.length)

                initDatatableAbsent(absent_device_logs)

                $(`.notification_main`).append(`
                    <div class="d-flex align-items-start mb-3">
                        <div class="flex-fill">
                            New open door request
                            <div class="fs-sm text-muted mt-1">Just now</div>
                        </div>
                    </div> 
                `)
            }
        });
    </script>

    {{-- Device Dynamic --}}
    <script>
        function toggleTable(id) {
            $(`#${id}`).toggle();
        }
    </script>

    <script>
        'use strict';

        $('#locations').select2({
            width: '100%'
        });

        $('#locations').change(function() {
            let locations = $(this).val();
            let url = '{{ route("admin.dashboard.index") }}';
            locations.forEach(element => {
                if (url.indexOf('?') === -1) {
                    url = `${url}?locations[]=${element}`
                } else {
                    url = `${url}&locations[]=${element}`
                }
            });
            window.location = url;
        });
    </script>
@endpush
