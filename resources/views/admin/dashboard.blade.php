@extends('admin.layout.main')

@push('header')
  <div class="page-header page-header-light shadow">
    <div class="page-header-content d-lg-flex">
      <div class="d-flex">
        <h4 class="page-title mb-0">
          Dashboard
        </h4>
      </div>
    </div>

    <div class="page-header-content d-lg-flex border-top">
      <div class="d-flex">
        <div class="breadcrumb py-2">
          <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
          <a class="breadcrumb-item" href="#">Dashboard</a>
        </div>
        <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
          data-bs-toggle="collapse" href="#breadcrumb_elements">
          <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
        </a>
      </div>
      <div class="ms-auto d-flex gap-3 py-3 bg-white">
        <a href="{{ route('admin.dashboard.index') }}">All</a>
        <span>|</span>
        <a href="{{ route('admin.dashboard.index') }}?area=jakarta">Jakarta</a>
        <span>|</span>
        <a href="{{ route('admin.dashboard.index') }}?area=bandung">Bandung</a>
        <span>|</span>
        <a href="{{ route('admin.dashboard.index') }}?area=depok">Depok</a>
      </div>
    </div>
  </div>
@endpush

@section('content')
  <div class="row gx-3">
    @foreach ($status_type_widgets as $status_type_widget)
      <div class="col-lg-3 col-12">
        <div class="card text-white" style="background-color: {{ $status_type_widget->status_type->color }};">
          <div class="card-body">
            <div class="d-flex">
              <h3 class="mb-0">
                {{ $status_type_widget->status_type->device_status->where('device', '!=', null)->count() }}
              </h3>
            </div>
            <div>
              {{ $status_type_widget->status_type->name }}
            </div>
            <button onclick="toggleTable('{{ $status_type_widget->id }}')" type="button" class="btn btn-white mt-4 p-2">
              <i class="ph-table"></i>
            </button>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div class="row gx-3">
    @foreach ($status_type_widgets as $status_type_widget)
      <div class="col-md-6 col-12 table-component" id="{{ $status_type_widget->id }}">
        <div class="mb-3">
          <div class="bg-white p-4">
            <h6>{{ $status_type_widget->status_type->name }}</h6>
            <table class="table datatable-basic">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Device ID</th>
                  <th>Location</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($status_type_widget->status_type->device_status as $key => $device_status)
                  @if ($device_status->device)
                    <tr>
                      <td>{{ $device_status->updated_at }}</td>
                      <td>{{ $device_status->device->device_id }}</td>
                      <td>{{ explode('/', $device_status->device->subscribe_topic)[1] }}</td>
                      <td class="text-center">
                        @if ($device_status->device->publish_action)
                          <div class="d-inline-flex">
                            <div class="dropdown">
                              <a class="text-body" data-bs-toggle="dropdown" href="#">
                                <i class="ph-list"></i>
                              </a>
                              <div class="dropdown-menu dropdown-menu-end">
                                @foreach ($device_status->device->publish_action as $publish_action)
                                  <button onclick="handlePublishAction('{{ $publish_action->id }}')"
                                    class="dropdown-item">
                                    {{ $publish_action->label }}
                                  </button>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        @else
                          <span>No Publish Command Available</span>
                        @endif
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

@push('js')
  <script>
    function toggleTable(id) {
      $(`#${id}`).toggle();
    }

    function handlePublishAction(id) {
      $.ajax({
        url: '/admin/devices/publish',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          id: id,
        },
        success: function(response) {
          alert(response.message);
        },
        error: function(error) {
          console.log(error);
        }
      })
    }
  </script>
@endpush
