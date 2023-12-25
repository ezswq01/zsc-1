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
                        <h3 class="mb-0">
                            {{ $absent_received_logs->count() }}
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
                    <table class="table datatable-basic">
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
                        <tbody>
                            @foreach ($absent_received_logs as $key => $absent_received_log)
                                @if ($absent_received_log->absent_device)
                                    <div id="open_absent_device_{{ $absent_received_log->id }}" class="modal fade"
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">RESPOND REQUEST
                                                        - {{ $absent_received_log->absent_device->device_id }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6 class="fw-semibold">Notes</h6>
                                                    <textarea class="form-control">{{ $absent_received_log->notes }}</textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="openDoor('{{ $absent_received_log->id }}')">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <tr>
                                    <td>{{ $absent_received_log->created_at }}</td>
                                    <td>{{ $absent_received_log->absent_device->device_id }}</td>
                                    <td>
                                        {{ \App\Models\User::where("user_code", $absent_received_log->value)->first()->name }}
                                    </td>
                                    <td id="mark_absent_received_log_{{ $absent_received_log->id }}">
                                        {!! $absent_received_log->status == "Open"
                                            ? '<i class="ph-check-circle text-success"></i>'
                                            : '<i class="ph-question text-danger"></i>' !!}
                                    </td>
                                    <td>{{ $absent_received_log->absent_device->branch }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            <div class="dropdown">
                                                <a class="text-body" data-bs-toggle="dropdown" href="#">
                                                    <i class="ph-list"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @if ($absent_received_log->status == "Request Open")
                                                        <button class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#open_absent_device_{{ $absent_received_log->id }}">
                                                            <i class="ph-newspaper me-1"></i> Respond Request
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @foreach ($status_type_widgets as $status_type_widget)
            <div class="col-12 table-component" id="{{ $status_type_widget->id }}">
                <div class="mb-3">
                    <div class="bg-white p-4">
                        <h6>{{ $status_type_widget->status_type->name }}</h6>
                        <table class="table datatable-basic">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Device ID</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($status_type_widget->status_type->device_status as $key => $device_status)
                                    @if ($device_status->device)
                                        <div id="open_{{ $device_status->id }}" class="modal fade" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">OPEN NOTE
                                                            - {{ $device_status->device->device_id }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6 class="fw-semibold">Notes</h6>
                                                        <p>{{ $device_status->notes }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($device_status->device)
                                        <div id="create_{{ $device_status->id }}" class="modal fade" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">CREATE NOTE -
                                                            {{ $device_status->device->device_id }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6 class="fw-semibold">Notes</h6>
                                                        <textarea class="form-control">{{ $device_status->notes }}</textarea>
                                                        <div class="d-flex gap-2 mt-3">
                                                            <label for="marked_{{ $device_status->id }}">Set as
                                                                marked?</label>
                                                            <input {{ $device_status->marked_as_read ? "checked" : "" }}
                                                                type="checkbox" class="form-check-input"
                                                                id="marked_{{ $device_status->id }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary"
                                                            onclick="handleSubmitNotes('{{ $device_status->id }}')">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($device_status->device)
                                        @if ($device_status->device->publish_action)
                                            @foreach ($device_status->device->publish_action as $publish_action)
                                                <div id="publish_{{ $device_status->id }}_{{ $publish_action->id }}"
                                                    class="publish_{{ $device_status->id }} modal fade" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">PUBLISH -
                                                                    {{ $device_status->device->device_id }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h6 class="fw-semibold">Notes</h6>
                                                                <textarea class="form-control">{{ $device_status->notes }}</textarea>
                                                                <div class="d-flex gap-2 mt-3">
                                                                    <label for="marked_{{ $device_status->id }}">Set as
                                                                        marked?</label>
                                                                    <input disabled checked type="checkbox"
                                                                        class="form-check-input"
                                                                        id="marked_{{ $device_status->id }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-link"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="handlePublishAction('{{ $device_status->id }}', '{{ $publish_action->id }}')">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                    @if ($device_status->device)
                                        <tr>
                                            <td>{{ $device_status->updated_at }}</td>
                                            <td>{{ $device_status->device->device_id }}</td>
                                            <td id="mark_{{ $device_status->id }}">{!! $device_status->marked_as_read
                                                ? '<i class="ph-check-circle text-success"></i>'
                                                : '<i class="ph-question text-danger"></i>' !!}</td>
                                            <td>{{ explode("/", $device_status->device->subscribe_topic)[1] }}</td>
                                            <td class="text-center">
                                                <div class="d-inline-flex">
                                                    <div class="dropdown">
                                                        <a class="text-body" data-bs-toggle="dropdown" href="#">
                                                            <i class="ph-list"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            @if ($device_status->device->publish_action)
                                                                @foreach ($device_status->device->publish_action as $publish_action)
                                                                    <button class="dropdown-item"data-bs-toggle="modal"
                                                                        data-bs-target="#publish_{{ $device_status->id }}_{{ $publish_action->id }}">
                                                                        {{ $publish_action->label }}
                                                                    </button>
                                                                @endforeach
                                                            @endif
                                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#open_{{ $device_status->id }}">
                                                                <i class="ph-newspaper me-1"></i> Open Note
                                                            </button>
                                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                                data-bs-target="#create_{{ $device_status->id }}">
                                                                <i class="ph-note-pencil me-1"></i> Create Note
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push("js")
    <script>
        function toggleTable(id) {
            $(`#${id}`).toggle();
        }

        function handlePublishAction(device_status_id, publish_action_id) {
            if (!confirm('Are you sure want to publish?')) return false;

            const textarea = $(`#publish_${device_status_id}_${publish_action_id} textarea`).val()

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
                    // @TODO : Change Icon to marked
                    //
                    //

                    $(`#mark_${device_status_id}`).html('<i class="ph-check-circle text-success"></i>');
                    $(`#open_${device_status_id} p`).html(textarea);
                    $(`#create_${device_status_id} textarea`).val(textarea);
                    $(`.publish_${device_status_id} textarea`).val(textarea);
                    alert(response.message);
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }

        function handleSubmitNotes(device_status_id) {
            if (!confirm('Are you sure want to submit this notes?')) return false;

            const textarea = $(`#create_${device_status_id} textarea`).val()

            $.ajax({
                url: '/admin/device_status/notes',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    device_status_id: device_status_id,
                    notes: textarea,
                    marked_as_read: $(`#marked_${device_status_id}`).is(':checked'),
                },
                success: function(response) {
                    // @TODO : Change Icon to marked
                    //
                    //
                    if ($(`#marked_${device_status_id}`).is(':checked')) {
                        $(`#mark_${device_status_id}`).html('<i class="ph-check-circle text-success"></i>');
                    } else {
                        $(`#mark_${device_status_id}`).html('<i class="ph-question text-danger"></i>');
                    }
                    $(`#open_${device_status_id} p`).html(textarea);
                    alert(response.message);
                },
                error: function(error) {
                    alert("Something went wrong!");
                    console.log(error);
                }
            })
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
