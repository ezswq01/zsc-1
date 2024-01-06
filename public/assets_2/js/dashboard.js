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
                data: "id",
                render: function (data, type, row) {
                    return data;
                },
            },
            {
                data: "created_at",
                render: function (data, type, row) {
                    return moment(data).format("YYYY MM DD HH:mm:ss");
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
                                        <i class="ph-newspaper me-1"></i> Open Note
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
