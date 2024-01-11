function initDatatableAbsent(absent_device_logs) {
    $(".absent_received_logs").DataTable().destroy();
    $(".absent_received_logs").DataTable({
        data: absent_device_logs,
        order: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1, 2, 3, 4, 5],
                className: "align-middle",
            },
        ],
        columns: [
            {
                data: "created_at",
                render: function (data, type, row) {
                    return moment(data).format("YYYY-MM-DD HH:mm:ss");
                },
            },
            {
                data: "absent_device",
                render: function (data, type, row) {
                    return data?.absent_device_id
                        ? data?.absent_device_id
                        : "-";
                },
            },
            {
                data: "value",
                render: function (data, type, row) {
                    return data;
                },
            },
            {
                data: "status",
                render: function (data, type, row) {
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
                render: function (data, type, row) {
                    return data?.branch;
                },
            },
            {
                data: "id",
                render: function (data, type, row) {
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

function initDatatableStatusType(status_type_widgets) {
    status_type_widgets.map((status_type_widget) => {
        $(`#${status_type_widget.id} table`).DataTable().destroy();
        $(`#${status_type_widget.id} table`).DataTable({
            data: status_type_widget.status_type.device_status,
            order: [[0, "desc"]],
            columnDefs: [
                {
                    targets: [0, 1, 2, 3, 4],
                    className: "align-middle",
                },
            ],
            columns: [
                {
                    data: "created_at",
                    render: function (data, type, row) {
                        return moment(data).format("YYYY-MM-DD HH:mm:ss");
                    },
                },
                {
                    data: "device",
                    render: function (data, type, row) {
                        return data?.device_id ? data?.device_id : "-";
                    },
                },
                {
                    data: "marked_as_read",
                    render: function (data, type, row) {
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
                    render: function (data, type, row) {
                        return data.branch;
                    },
                },
                {
                    data: "id",
                    render: function (data, type, row) {
                        return `
                            <div class="d-inline-flex">
                                <div class="dropdown">
                                    <a class="text-body" data-bs-toggle="dropdown" href="#">
                                        <i class="ph-list"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        ${
                                            row.device &&
                                            row.device.publish_action &&
                                            row.device.publish_action
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
                                                status_type_widget.id
                                            }, ${data})" 
                                            class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#open_note">
                                            <i class="ph-newspaper me-1"></i> Open Note
                                        </button>
                                        <button
                                            onclick="handleCreateModalNote(${
                                                status_type_widget.id
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
