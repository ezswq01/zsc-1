 <div class="d-inline-flex">
     <div class="dropdown">
         <a class="text-body" data-bs-toggle="dropdown" href="#">
             <i class="ph-list"></i>
         </a>
         <div class="dropdown-menu dropdown-menu-end">
             @can('devices-read')
                 <a class="dropdown-item" href="{{ route('admin.devices.show', $model->id) }}">
                     <i class="ph-scroll me-2"></i>
                     Show
                 </a>
             @endcan

             @can('devices-update')
                 <a class="dropdown-item" href="{{ route('admin.devices.edit', $model->id) }}">
                     <i class="ph-pen me-2"></i>
                     Edit
                 </a>
             @endcan

             @can('devices-delete')
                 <form action="{{ route('admin.devices.destroy', $model->id) }}" method="POST">
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
