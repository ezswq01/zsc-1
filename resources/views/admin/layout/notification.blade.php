<div class="d-none">
    @if (session("success"))
        <button class="btn btn-light" id="sweet_success" msg="{{ session("success") }}" type="button">
            Launch <i class="icon-play3 ml-2"></i>
        </button>
        <script>
            $(document).ready(function() {
                $('#sweet_success').click();
            });
        </script>
    @endif
    @if (session("error"))
        <button class="btn btn-light" id="sweet_error" msg="{{ session("error") }}" type="button">
            Launch <i class="icon-play3 ml-2"></i>
        </button>
        <script>
            $(document).ready(function() {
                $('#sweet_error').click();
            });
        </script>
    @endif
</div>

<div class="position-absolute top-0 start-50 translate-middle-x mt-5" style="z-index: 9999">
    @if (session("errors"))
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="m-0">
                @foreach (session("errors")->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
            <button class="btn-close" data-bs-dismiss="alert" type="button"></button>
        </div>
    @endif
</div>

<!-- Notifications -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="notifications">
    <div class="offcanvas-header py-0">
        <h5 class="offcanvas-title py-3">Activity</h5>
        <button type="button" class="btn btn-light btn-sm btn-icon border-transparent rounded-pill"
            data-bs-dismiss="offcanvas">
            <i class="ph-x"></i>
        </button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="bg-light fw-medium py-2 px-3">Notifications</div>
        <div class="p-3 notification_main"></div>
    </div>
</div>
<!-- /notifications -->

<script>
    let limit = 5;
    let dataNotifications = [];

    function handleMore() {
        limit += 5;
        $(`.load-more`).html(`<i class="ph-spinner spinner"></i>`);
        getData(limit);
    }

    function handleRead(notification_id) {
        $(`.notif-id-${notification_id}`).html(`<i class="ph-spinner spinner"></i>`);

        $.ajax({
            url: `/admin/notifications/${notification_id}/read`,
            type: "GET",
            dataType: "json",
            success: function(res) {
                getData(limit);
            },
            error: function(error) {
                // console.log(error)
            }
        });
    }

    function getData() {
        $.ajax({
            url: `/admin/notifications?limit=${limit}`,
            type: "GET",
            dataType: "json",
            success: function(res) {
                $(`.notification_main`).html(``);

                dataNotifications = res.data.data.map((item) => ({
                    id: item.id,
                    message: item.message,
                    notif_type: item.notif_type,
                    notif_status: item.notif_status,
                    device: item.absent_device_id ? item.absent_device : item.device,
                    created_at: moment(item.created_at).format("DD/MM/YYYY HH:mm:ss"),
                }));

                $(`.notification-count`).html(`${res.data.total}`);

                dataNotifications.map((item) => {
                    $(`.notification_main`).append(`
                        <div class="d-flex flex-column gap-3 align-items-start mb-3">
                            <div class="flex-fill">
                                ${item.message}
                                <div class="fs-sm text-muted mt-1">${item.created_at}</div>
                            </div>
                            <div class="d-flex gap-2">
                              ${
                                item.notif_status == "unread"
                                  ? `<button onclick="handleRead('${item.id}')" class="btn btn-success notif-id-${item.id}">Mark as read</button>`
                                  : `<button class="btn btn-danger">Already read</button>`
                              }
                            </div>
                        </div> 
                    `)
                });

                if (res.data.current_page < res.data.last_page) {
                    $(`.notification_main`).append(`
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-light load-more" onclick="handleMore()">
                                Load more <i class="icon-play3 ml-2"></i>
                            </button>
                        </div>
                    `)
                }
            },
            error: function(error) {
                // console.log(error)
            }
        });
    }

    $(document).ready(function() {
        getData(limit);
    });

    window.Echo.channel('laravel_database_newDataChannel').listen('.newDataEvent', (e) => {
        // console.log("notif received");
        getData(limit);
    });
</script>
