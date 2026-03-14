<div id="create-note" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <form id="create-note-form" action="{{ route('admin.device_status.notes') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        CREATE NOTE - <span id="device-id"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-semibold">Notes</h6>
                    <input type="hidden" name="device_status_id" id="device_status_id">
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                    <div class="d-flex gap-2 mt-3">
                        <label for="marked_as_read">Set as
                            marked?</label>
                        <input type="checkbox" name="marked_as_read" id="marked_as_read" class="form-check-input">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
