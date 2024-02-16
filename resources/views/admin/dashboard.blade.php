@extends("admin.layout.main")

@push("style")
    <style>
        .select2-search__field {
            width: 100% !important
        }
    </style>
@endpush

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
            <div class="d-flex w-100 py-2 bg-white gap-2">
                <input value="" class="form-control w-100" id="device_id" name="device_id"
                    placeholder="Type Device ID" required type="text">
                <select class="form-control select" data-placeholder="All Locations" name="branches" id="branches"
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
                <select class="form-control select" data-placeholder="All Sub-Locations" name="buildings" id="buildings"
                    multiple="multiple">
                    <option></option>
                    @foreach ($device_sub_locations as $device_sub_location)
                        @php
                            $selected = in_array($device_sub_location->building, request()->locations ?? []) ? "selected" : "";
                        @endphp
                        <option {{ $selected }} value="{{ $device_sub_location->building }}">
                            {{ ucfirst($device_sub_location->building) }}</option>
                    @endforeach
                </select>
                <select class="form-control select" data-placeholder="All Location-ID" name="rooms" id="rooms"
                    multiple="multiple">
                    <option></option>
                    @foreach ($device_location_ids as $device_location_id)
                        @php
                            $selected = in_array($device_location_id->room, request()->locations ?? []) ? "selected" : "";
                        @endphp
                        <option {{ $selected }} value="{{ $device_location_id->room }}">
                            {{ ucfirst($device_location_id->room) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endpush

@section("content")
    @php
        $setting = App\Models\Setting::first();
    @endphp
    <div class="row gx-3" id="status_types">
        @if ($setting->is_access_device)
            <div class="col-lg-4 col-12">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 class="mb-0 absent_door_request_qty"></h3>
                            <button onclick="toggleTable('absent-doors')" type="button" class="btn btn-white p-1">
                                <i class="ph-table"></i>
                            </button>
                        </div>
                        <div>
                            Access Request
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row gx-3">
        <div class="col-12">
            <button onclick="$('.table-component').hide()" class="btn btn-primary mb-3">
                Hide All Table
            </button>
        </div>
        @if ($setting->is_access_device)
            <div class="col-12 table-component" id="absent-doors">
                <div class="mb-3">
                    <div class="bg-white p-4">
                        <h6>Access Request</h6>
                        <table class="table absent_received_logs">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Device ID</th>
                                    <th>User</th>
                                    <th>User Code</th>
                                    <th>Job Position</th>
                                    <th>Working Area</th>
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
        @endif
        <div class="col-12">
            <div class="row gx-3" id="status_type_widgets_tables">
            </div>
        </div>
    </div>
@endsection

@push("js")
    <script>
        let absent_device_logs = [];
        let status_type_widgets = [];
        let status_types = [];

        let audio = new Audio('/mcc-notification.wav');

        function initDatatableAbsent(absent_device_logs) {
            $(".absent_received_logs").DataTable().destroy();
            $(".absent_received_logs").DataTable({
                data: absent_device_logs,
                order: [
                    [0, "desc"]
                ],
                columnDefs: [{
                    targets: [0, 1, 2, 3, 4, 5],
                    className: "align-middle",
                }, ],
                columns: [{
                        data: "created_at",
                        render: function(data, type, row) {
                            return moment(data).format("YYYY-MM-DD HH:mm:ss");
                        },
                    },
                    {
                        data: "absent_device",
                        render: function(data, type, row) {
                            return data?.absent_device_id ?
                                data?.absent_device_id :
                                "-";
                        },
                    },
                    {
                        data: "user",
                        render: function(data, type, row) {
                            return data?.name;
                        },
                    },
                    {
                        data: "user",
                        render: function(data, type, row) {
                            return data?.user_code;
                        },
                    },
                    {
                        data: "user",
                        render: function(data, type, row) {
                            return data?.job_position || "-";
                        },
                    },
                    {
                        data: "user",
                        render: function(data, type, row) {
                            return data?.work_area || "-";
                        },
                    },
                    {
                        data: "status",
                        render: function(data, type, row) {
                            return `
                                    <div class="absent_badge_${row.id}">
                                        ${
                                            data == "Open"
                                                ? `<span class="badge bg-success">Opened</span>`
                                                : `<span class="badge bg-danger">Request Open</span>`
                                        }
                                    </div>
                                `;
                        },
                    },
                    {
                        data: "absent_device",
                        render: function(data, type, row) {
                            return data?.branch;
                        },
                    },
                    {
                        data: "id",
                        render: function(data, type, row) {
                            return `
                                    <div class="d-inline-flex">
                                        <div class="dropdown">
                                            <a class="text-body" data-bs-toggle="dropdown" href="#">
                                                <i class="ph-list"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button 
                                                    onclick="handleOpenModalAbsent(${data})" 
                                                    class="dropdown-item" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#open_absent_device"
                                                >
                                                    <i class="ph-newspaper me-1"></i> Open Door
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        },
                    },
                ],
            });
        }

        function initDatatableStatusTypeWidgets(status_type_widgets) {
            const filteredData = status_type_widgets.map((stw) => {
                return {
                    ...stw,
                    status_type: {
                        ...stw.status_type,
                        device_status: stw.status_type.device_status.filter(
                            (item) => item.device
                        ),
                    },
                }
            })
            filteredData.map((status_type_widget) => {
                $(`#${status_type_widget.id} table`).DataTable().destroy();
                $(`#${status_type_widget.id} table`).DataTable({
                    data: status_type_widget.status_type.device_status,
                    order: [
                        [0, "desc"]
                    ],
                    columnDefs: [{
                        targets: [0, 1, 2, 3, 4],
                        className: "align-middle",
                    }, ],
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
                            data: "id",
                            render: function(data, type, row) {
                                return `
                                    <div class="d-inline-flex">
                                        <div class="dropdown">
                                            <a class="text-body" data-bs-toggle="dropdown" href="#">
                                                <i class="ph-list"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                ${
                                                    row?.device &&
                                                    row?.device?.publish_action &&
                                                    row?.device?.publish_action
                                                        .map(
                                                            (item) =>
                                                                `<button 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    onclick="handlePublishModalNote(${status_type_widget.id}, ${data}, ${item.id})" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    class="dropdown-item" 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-bs-toggle="modal"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-bs-target="#publish_action">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ${item.label}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </button>`
                                                        )
                                                        .reduce(
                                                            (prev, curr) => prev + curr
                                                        )
                                                }
                                                <button 
                                                    onclick="handleOpenModalNote(${
                                                        status_type_widget?.id
                                                    }, ${data})" 
                                                    class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#open_note">
                                                    <i class="ph-newspaper me-1"></i> Open Note
                                                </button>
                                                <button
                                                    onclick="handleCreateModalNote(${
                                                        status_type_widget?.id
                                                    }, ${data})" 
                                                    class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#create_note">
                                                    <i class="ph-note-pencil me-1"></i> Create Note
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            },
                        },
                    ],
                });
            });
            return false;
        }

        function status_type_widgets_html(id, status_type_name) {
            return `
                <div class="col-12 table-component" id="${id}">
                    <div class="mb-3">
                        <div class="bg-white p-4">
                            <h6>${status_type_name}</h6>
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
            `
        }

        function status_type_html(color, count, widget_id, name) {
            return `
                <div class="col-lg-4 col-12">
                    <div class="card text-white" style="background-color: ${color};">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h3 class="mb-0 status_type_${widget_id}">
                                    ${count}
                                </h3>
                                <button onclick="toggleTable('${widget_id}')" type="button"
                                    class="btn btn-white p-1">
                                    <i class="ph-table"></i>
                                </button>
                            </div>
                            <div>
                                ${name}
                            </div>
                        </div>
                    </div>
                </div>
            `
        }

        function absent_device_logs_html() {
            return `
                <div class="col-lg-4 col-12">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h3 class="mb-0 absent_door_request_qty"></h3>
                                <button onclick="toggleTable('absent-doors')" type="button" class="btn btn-white p-1">
                                    <i class="ph-table"></i>
                                </button>
                            </div>
                            <div>
                                Access Request
                            </div>
                        </div>
                    </div>
                </div>
            `
        }

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
                    const status_type_widget_id = response.status_type_widget.status_type_id
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
                    const status_type_widget_id = response.status_type_widget.status_type_id
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
                                                marked_as_read: true,
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
                    $(`#mark_${device_status_id}`).html('<i class="ph-check-circle text-success"></i>');
                    alert(response.message);
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

        function toggleTable(id) {
            $(`#${id}`).toggle();
        }

        function statusTypeLoading() {
            $("#status_types").html(`
                <div class="p-5 bg-white m-2 rounded-md d-flex justify-content-center">
                    <div class="spinner-border spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        }

        function statusTypeWidgetLoading() {
            $("#status_type_widgets_tables").html(`
                <div class="p-5 bg-white m-2 rounded-md d-flex justify-content-center">
                    <div class="spinner-border spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        }

        async function getFetch(url = "/api/dashboard") {
            try {
                // reset
                $("#status_types").html("");
                $("#status_type_widgets_tables").html("");

                // loading
                statusTypeLoading();
                statusTypeWidgetLoading();

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                });
                const data = await response.json();

                // erase loading
                $("#status_types").html("");
                $("#status_type_widgets_tables").html("");

                // Absent Device Logs
                @if ($setting->is_access_device)
                    $("#status_types").append(absent_device_logs_html());
                    absent_device_logs = data.absent_received_logs;
                    const countAbsent = absent_device_logs.filter((a) => a.marked_as_read == false).length
                    $(".absent_door_request_qty").html(countAbsent)
                    initDatatableAbsent(absent_device_logs)
                @endif

                // Status Type Widgets
                status_types = [];
                status_type_widgets = data.status_type_widgets;
                let status_type_widget_html = "";
                status_type_widgets.map((stw) => {
                    status_type_widget_html += status_type_widgets_html(
                        stw.id,
                        stw.status_type.name
                    );
                    // status_types
                    status_types.push({
                        widget_id: stw.id,
                        name: stw.status_type.name,
                        color: stw.status_type.color,
                        count: 0,
                    })
                    stw.status_type.device_status.map((ds) => {
                        if (ds.device && ds.marked_as_read == false) {
                            status_types = status_types.map((st) => {
                                if (st.widget_id == stw.id) {
                                    return {
                                        ...st,
                                        count: st.count + 1
                                    }
                                } else {
                                    return st
                                }
                            })
                        }
                    })
                });
                $("#status_type_widgets_tables").html(status_type_widget_html);
                initDatatableStatusTypeWidgets(status_type_widgets)

                // status_types
                let status_type_html_append = "";
                status_types.map((st) => {
                    status_type_html_append += status_type_html(st.color, st.count, st.widget_id, st.name);
                })
                $("#status_types").append(status_type_html_append);

            } catch (error) {
                console.log(error);
                alert("Something went wrong! Please contact admin or try again later.");
            }
        }

        async function triggerFetch() {
            let branches = $('#branches').val();
            let buildings = $('#buildings').val();
            let rooms = $('#rooms').val();
            let search = $('#device_id').val();

            let url = '{{ route("dashboard.ajax") }}';

            if (branches && branches.length > 0) {
                branches.forEach(element => {
                    if (url.indexOf('?') === -1) {
                        url = `${url}?branches[]=${element}`
                    } else {
                        url = `${url}&branches[]=${element}`
                    }
                });
            }

            if (buildings && buildings.length > 0) {
                buildings.forEach(element => {
                    if (url.indexOf('?') === -1) {
                        url = `${url}?buildings[]=${element}`
                    } else {
                        url = `${url}&buildings[]=${element}`
                    }
                });
            }

            if (rooms && rooms.length > 0) {
                rooms.forEach(element => {
                    if (url.indexOf('?') === -1) {
                        url = `${url}?rooms[]=${element}`
                    } else {
                        url = `${url}&rooms[]=${element}`
                    }
                });
            }

            if (search.length >= 3) {
                const searchParams = new URLSearchParams();
                searchParams.append('search', search);
                if (url.indexOf('?') === -1) {
                    url = `${url}?${searchParams.toString()}`;
                } else {
                    url = `${url}&${searchParams.toString()}`;
                }
            }

            await getFetch(url);
        }

        $(document).ready(async function() {
            const data = await getFetch();
        });

        // Websocket
        window.Echo.channel('laravel_database_newDataChannel').listen('.newDataEvent', (e) => {
            const item = e.message;

            @if ($setting->is_access_device)
                if (item.type == "absent_device") {
                    console.log(item.data)

                    // add items to absent_device_logs
                    absent_device_logs = [
                        item.data,
                        ...absent_device_logs.filter(
                            (adl) => adl.absent_device_id != item.data.absent_device_id
                        )
                    ]

                    // filter absent_device_logs
                    const absent_device_logs_requested = absent_device_logs.filter(
                        item => item.status != "Open"
                    )

                    // update absent_device_logs
                    $(`.absent_door_request_qty`).html(absent_device_logs_requested.length)
                    initDatatableAbsent(absent_device_logs)

                    // play sound
                    audio.play();
                }
            @endif

            if (item.type == "dynamic_device" && item.data.length > 0) {
                item.data.map((item) => { // item == device_status

                    // loop all stw
                    status_type_widgets = status_type_widgets.map((status_type_widget) => {
                        if (status_type_widget.id == item.status_type.status_type_widget.id) {

                            // table records update
                            const countDeviceStatus = [
                                ...status_type_widget.status_type.device_status.filter(
                                    (ds) => ds.device_id != item.device_id
                                ),
                                item
                            ].length;

                            // Update Cards
                            status_types = status_types.map((st) => {
                                if (st.widget_id == status_type_widget.id) {
                                    $(`.status_type_${status_type_widget.id}`)
                                        .html(
                                            countDeviceStatus
                                        );
                                    return {
                                        ...st,
                                        count: countDeviceStatus
                                    }
                                } else {
                                    return st
                                }
                            })

                            return {
                                ...status_type_widget,
                                status_type: {
                                    ...status_type_widget.status_type,
                                    device_status: countDeviceStatus
                                }
                            }
                        }
                        return {
                            ...status_type_widget
                        }
                    })

                })
                initDatatableStatusTypeWidgets(status_type_widgets)

                // play sound
                audio.play();
            }
        });
    </script>

    <script>
        'use strict';

        $('#branches').select2({
            width: '100%',
        });

        $('#buildings').select2({
            width: '100%',
        });

        $('#rooms').select2({
            width: '100%',
        });

        $('#device_id').on('input', function() {
            if ($(this).val().length >= 3 || $(this).val().length == 0) {
                triggerFetch()
            }
        })

        $('#branches, #buildings, #rooms').change(function() {
            triggerFetch()
        });
    </script>
@endpush
