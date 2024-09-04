<div class="row gx-3" id="card-widgets"></div>

<!-- HTMLS -->
<div id="card-widget-example" class="col-lg-4 col-12" style="display:none;">
    <div class="card text-white shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <h3 class="mb-0 display-3"></h3>
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <button type="button" class="btn btn-white p-1">
                        <i class="ph-table"></i>
                    </button>
                    <a class="btn btn-white p-1">
                        <i class="ph-clock"></i>
                    </a>
                </div>
            </div>
            <h6></h6>
        </div>
    </div>
</div>

<!-- HTMLS -->
<div id="card-widget-modal-example" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body overflow-auto text-nowrap">
                <table class="table table-center">
                    <thead>
                        <tr>
                            <th class="">Actions</th>
                            <th>Time</th>
                            <th>Log ID</th>
                            <th>Device ID</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Sub-Location</th>
                            <th>Room</th>
                            <th>Cams</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- HTMLS -->
<div id="card-widget-note-modal-example" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="5" style="resize: none;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link ms-auto" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success card-widget-note-modal-submit-note" >Submit Note</button>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        const absent_received_widgets_modal_print = (html, data) => {}
        const absent_received_widgets = (html, data) => {
            var cloned_card_widget_html = html.clone();
            var count_not_marked_as_read = data.length;

            // display block
            cloned_card_widget_html.css(
                'display', 'block'
            ).attr(
                'id', `card-widget-absent-device`
            );

            // modal button
            cloned_card_widget_html.find('button').attr(
                'data-bs-target', `#card-widget-modal-absent-device`
            ).attr(
                'data-bs-toggle', 'modal'
            );

            // a tag history
            cloned_card_widget_html.find('a').remove();

            // background color
            cloned_card_widget_html.find('.card').css(
                'background-color', count_not_marked_as_read == 0 
                    ? 'rgb(0, 100, 0)' 
                    : 'rgb(200, 0, 0)'
            ).attr(
                'id', `card-widget-card-absent-device`
            );

            // title count
            cloned_card_widget_html.find('h3').text(
                count_not_marked_as_read
            ).attr(
                'id', `card-widget-title-absent-device`
            );

            // widget name
            cloned_card_widget_html.find('h6').text(
                'Absent Devices'
            ).attr(
                'id', `card-widget-name-absent-device`
            );

            $('#card-widgets').append(cloned_card_widget_html);
        }
        const status_type_widgets_modal_print = (html, data) => {
            
            // vars
            var cloned_card_widget_modal_html = html.clone();
            var count_not_marked_as_read = data.status_type.device_status.length;
            var status_type_id = data.status_type.id;
            var status_logs = data.status_type.device_status;

            // id
            cloned_card_widget_modal_html.attr(
                'id', `card-widget-modal-${data.status_type_id}`
            );

            // modal title
            cloned_card_widget_modal_html.find('.modal-title').text(
                data.status_type.name
            );

            var tbody_html = '';
            status_logs.forEach(
                device_status => {
                    // tbody html
                    tbody_html += `<tr>`;
                    tbody_html += `<td class="align-middle d-flex gap-2"><button data-bs-target="#card-widget-note-modal-${device_status.id}" data-bs-toggle="modal" class="btn btn-sm btn-success"><i class="ph-eye"></i></button></td>`;
                    tbody_html += `<td>${moment(device_status.created_at).format('YYYY-MM-DD, HH:mm:ss')}</td>`;
                    tbody_html += `<td>${device_status.device_log.id}</td>`;
                    tbody_html += `<td>${device_status.device_id}</td>`;
                    tbody_html += `<td><div id="mark_${device_status.id}">${device_status.marked_as_read ? `<i class="ph-check-circle text-success"></i>` : `<i class="ph-question text-danger"></i>`}</div></td>`;
                    tbody_html += `<td>${device_status.device?.branch}</td>`;
                    tbody_html += `<td>${device_status.device?.building}</td>`;
                    tbody_html += `<td>${device_status.device?.room}</td>`;
                    tbody_html += `<td><ul class="mb-0">${device_status.device_log?.cam_payloads?.map((cam) => `<li><a target="_blank" href="/storage/${cam.file}">${cam.file_name}-id: ${cam.id}</a></li>`)?.join("") || "No Image Available"}</ul></td>`;
                    tbody_html += `</tr>`;

                    // html
                    cloned_card_widget_modal_html.find('tbody').html(tbody_html);

                    // open note modal
                    var cloned_card_widget_note_modal_html = $('#card-widget-note-modal-example').clone();

                    // modal id
                    cloned_card_widget_note_modal_html.attr(
                        'id', 'card-widget-note-modal-' + device_status.id
                    )

                    // modal title
                    cloned_card_widget_note_modal_html.find('.modal-title').text(
                        'Note for LOG ID: ' + device_status.id + ' - DEVICE ID: ' + device_status.device_id
                    );
                    
                    // textarea
                    cloned_card_widget_note_modal_html.find('textarea').val(device_status.notes).attr(
                        'disabled', device_status.noted ? true : false
                    );

                    // modal body / publish actions
                    if (device_status.device?.publish_action?.length > 0) {
                        device_status.device.publish_action.forEach(
                            action => {
                                cloned_card_widget_note_modal_html.find('.modal-footer').prepend(
                                    '<button data-device-status-id=' 
                                    + device_status.id 
                                    + " data-log-id="
                                    + device_status.device_log_id
                                    + ' data-publish-action-id="' 
                                    + action.id 
                                    +'" class="btn btn-sm btn-primary card-widget-note-modal-publish-action">' 
                                    + action.label 
                                    + '</button>'
                                );
                            }
                        )
                    }

                    // append
                    $('#card-widgets').append(cloned_card_widget_note_modal_html);

                    // event listener for publish modal
                    var publish_actions = document.querySelectorAll(
                        '#card-widget-note-modal-' 
                            + device_status.id 
                            + ' .card-widget-note-modal-publish-action'
                    );
                    publish_actions.forEach(
                        action_html => {
                            var publish_action_id = action_html.getAttribute('data-publish-action-id');
                            var device_status_id = action_html.getAttribute('data-device-status-id');
                            var log_id = action_html.getAttribute('data-log-id');
                            action_html.addEventListener('click', () => {
                                if (!confirm('Are you sure you want to publish this note?')) return;
                                const textarea = document.querySelector('#card-widget-note-modal-' + device_status.id + ' textarea').value;
                                const data = {
                                    _token: '{{ csrf_token() }}',
                                    id: publish_action_id,
                                    device_status_id: device_status_id,
                                    log_id: log_id,
                                    notes: textarea
                                };
                                $.ajax({
                                    url: '/admin/devices/publish',
                                    type: 'POST',
                                    data: data,
                                    success: async function(response) {
                                        alert(response.message);
                                        await getFetch();
                                        $('.modal-backdrop').remove();
                                    },
                                    error: function(error) {
                                        console.log(error);
                                        alert('An error occured while publishing note!');
                                    }
                                })
                            })
                        }
                    )

                    // event listener for note modal
                    document.querySelector(
                            '#card-widget-note-modal-' 
                                + device_status.id 
                                + ' .card-widget-note-modal-submit-note'
                    ).addEventListener('click', () => {
                        if (device_status.noted) return alert('Note already published!');
                        if (!confirm('Are you sure you want to publish this note?')) return;
                        const textarea = document.querySelector('#card-widget-note-modal-' + device_status.id + ' textarea').value;
                        const data = {
                            _token: '{{ csrf_token() }}',
                            device_status_id: device_status.id,
                            notes: textarea,
                        };
                        $.ajax({
                            url: '/admin/device_status/notes',
                            type: 'POST',
                            data: data,
                            success: async function(response) {
                                alert(response.message);
                                await getFetch();
                                $('.modal-backdrop').remove();
                            },
                            error: function(error) {
                                console.log(error);
                                alert('An error occured while publishing note!');
                            }
                        })
                    })
                }
            )

            // append
            $('#card-widgets').append(cloned_card_widget_modal_html);
        }
        const status_type_widgets_print = (html, data) => {

            // vars
            var cloned_card_widget_html = html.clone();
            var count_not_marked_as_read = data.status_type.device_status.length;
            var status_type_id = data.status_type.id;

            // display block
            cloned_card_widget_html.css(
                'display', 'block'
            ).attr(
                'id', `card-widget-${data.status_type_id}`
            );

            // modal button
            cloned_card_widget_html.find('button').attr(
                'data-bs-target', `#card-widget-modal-${data.status_type_id}`
            ).attr(
                'data-bs-toggle', 'modal'
            );

            // background color
            cloned_card_widget_html.find('.card').css(
                'background-color', count_not_marked_as_read == 0 
                    ? data.status_type.color 
                    : data.status_type.trigger_color
            ).attr(
                'id', `card-widget-card-${data.status_type_id}`
            );

            // title count
            cloned_card_widget_html.find('h3').text(
                count_not_marked_as_read
            ).attr(
                'id', `card-widget-title-${data.status_type_id}`
            );

            // a tag history
            cloned_card_widget_html.find('a').attr(
                'href', `/admin/status_types/${data.status_type.id}/history`
            ).attr(
                'id', `card-widget-history-${data.status_type_id}`
            );

            // widget name
            cloned_card_widget_html.find('h6').text(
                data.status_type.name
            ).attr(
                'id', `card-widget-name-${data.status_type_id}`
            );

            $('#card-widgets').append(cloned_card_widget_html);
        }
    </script>
@endpush