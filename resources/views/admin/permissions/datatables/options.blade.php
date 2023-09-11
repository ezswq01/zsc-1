<div class="d-inline-flex">
    <div class="dropdown">
        <a class="text-body" data-bs-toggle="dropdown" href="#">
            <i class="ph-list"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            @can('permissions-read')
                <a class="dropdown-item" href="{{ route('admin.permissions.show', $model->id) }}">
                    <i class="ph-scroll me-2"></i>
                    Show
                </a>
            @endcan

            @can('permissions-update')
                <a class="dropdown-item" href="{{ route('admin.permissions.edit', $model->id) }}">
                    <i class="ph-pen me-2"></i>
                    Edit
                </a>
            @endcan

            @can('permissions-delete')
                <form action="{{ route('admin.permissions.destroy', $model->id) }}" method="POST">
                    @csrf
                    @method('delete')
                    <button class="dropdown-item">
                        <i class="ph-trash me-2"></i>
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </div>
</div>
