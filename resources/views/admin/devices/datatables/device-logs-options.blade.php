<div class="d-inline-flex">
    <div class="dropdown">
        <a class="text-body" data-bs-toggle="dropdown" href="#">
            <i class="ph-list"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            @if ($model->device->publish_action)
                @foreach ($model->device->publish_action as $publish_action)
                    <button class="dropdown-item publish" data-bs-toggle="modal" data-bs-target="#publish"
                        data-publish-action-id={{ $publish_action->id }} data-device-status-id="{{ $model->id }}">
                        {{ $publish_action->label }}
                    </button>
                @endforeach
            @endif
            <button class="dropdown-item open-note" data-bs-toggle="modal" data-bs-target="#open-note"
                data-id="{{ $model->id }}">
                <i class="ph-newspaper me-1"></i> Open Note
            </button>
            <button class="dropdown-item create-note" data-bs-toggle="modal" data-bs-target="#create-note"
                data-id="{{ $model->id }}">
                <i class="ph-note-pencil me-1"></i> Create Note
            </button>
        </div>
    </div>
</div>
