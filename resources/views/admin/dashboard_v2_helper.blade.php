@push('js')
    <script>
        // HELPER
        var audio = new Audio('/mcc-notification.wav');
        var data = {absent_received_logs: [], status_type_widgets: []};
        const status_type_widgets_convert = (res) => {
            return {
                absent_received_logs: res.absent_received_logs.filter(
                    (adl) => adl.marked_as_read == false
                ),
                status_type_widgets: res.status_type_widgets.map(
                    (stw) => {
                        const last_status_not_marked_as_read = 
                        stw?.status_type?.device_status?.at(0)?.marked_as_read == false;

                        const status_not_marked_as_read = 
                        stw?.status_type?.device_status?.filter(
                            (item) => item.marked_as_read == false
                        );

                        return {
                            ...stw,
                            status_type: {
                                ...stw.status_type,
                                device_status: last_status_not_marked_as_read 
                                ? status_not_marked_as_read 
                                : [],
                            },
                        }
                    }
                )
            }
        }

        // FETCH & RENDER
        const triggerFetch = async () => {
            let branches = $('#branches').val();
            let buildings = $('#buildings').val();
            let rooms = $('#rooms').val();
            let search = $('#device_id').val();
            let date = $('.datepicker-basic').val();
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
            if (date) {
                const dateParams = new URLSearchParams();
                dateParams.append('date', date);
                if (url.indexOf('?') === -1) {
                    url = `${url}?${dateParams.toString()}`;
                } else {
                    url = `${url}&${dateParams.toString()}`;
                }
            }
            await getFetch(url);
        }
        const getFetch = async (url = "/api/dashboard") => {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });
            const res = await response.json();
            data = status_type_widgets_convert(res);
            print();
        }
        const print = () => {
            $('#card-widgets').html('');
            const card_widget_html = $('#card-widget-example');
            const card_widget_modal_html = $('#card-widget-modal-example');

            // absent_received_widgets
            @if ($setting->is_access_device)
                absent_received_widgets(card_widget_html, data.absent_received_logs);
            @endif

            // status_type_widgets
            data.status_type_widgets.forEach(
                data => {
                    status_type_widgets_print(card_widget_html, data);
                    status_type_widgets_modal_print(card_widget_modal_html, data);
                }
            );

            // modal reset
            $('.modal-backdrop').remove();
        }

        // WEBSOCKET
        window.Echo.channel('laravel_database_newDataChannel').listen('.newDataEvent', (e) => {
            const item = e.message;
            if (item.type == "absent_device") {

                // add items to absent_received_logs
                data['absent_received_logs'] = [item.data, ...data['absent_received_logs'].filter(
                    (adl) => adl.absent_device_id != item.data.absent_device_id
                )];

                // filter absent_received_logs
                data['absent_received_logs'] = data['absent_received_logs'].filter(
                    item => item.status != "Open"
                )

                // play sound
                audio.play();
            }
            if (item.type == "dynamic_device" && item.data.length > 0) {
                item.data.map((item) => { // item == device_status
                    data['status_type_widgets'] = data['status_type_widgets'].map((status_type_widget) => {
                        if (status_type_widget.id == item.status_type.status_type_widget.id) {
                            const device_id = item.device_id;
                            const device_status_to_be = status_type_widget.status_type.device_status.filter(
                                (ds) => ds.device_id != device_id
                            );
                            status_type_widget['status_type']['device_status'] = [item, ...device_status_to_be];
                            return status_type_widget;
                        }
                        return status_type_widget;
                    })
                })
                audio.play();
            }
            print();
        });
    </script>
@endpush