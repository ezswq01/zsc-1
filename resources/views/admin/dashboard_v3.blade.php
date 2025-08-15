@extends("admin.layout.main")

@push("style")
<style>
  .select2-search__field {
    width: 100% !important
  }
</style>
@endpush

@push("header")
<div class="page-header page-header-light">
  <div class="page-header-content d-lg-flex">
    <div class="d-flex">
      <h4 class="page-title mb-0">
        Dashboard
      </h4>
    </div>
  </div>
  <div class="page-header-content d-lg-flex">
    <div class="d-flex flex-fill w-xl-75 w-100">
      <div class="breadcrumb py-2">
        <a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
        <a class="breadcrumb-item" href="#">Dashboard</a>
      </div>
      <a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
        data-bs-toggle="collapse" href="#breadcrumb_elements">
        <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
      </a>
    </div>
    <div class="d-flex w-100 py-2 bg-white gap-2">
      <select
        class="form-control select"
        data-placeholder="All Locations"
        name="branches"
        id="branches"
        multiple="multiple">
        <option></option>
        @foreach ($device_locations as $device_location)
        @php
        $selected = in_array($device_location->branch, request()->locations ?? [])
        ? "selected"
        : "";
        @endphp
        <option {{ $selected }} value="{{ $device_location->branch }}">
          {{ ucfirst($device_location->branch) }}
        </option>
        @endforeach
      </select>
      <select
        class="form-control select"
        data-placeholder="All Sub-Locations"
        name="buildings"
        id="buildings"
        multiple="multiple">
        <option></option>
        @foreach ($device_sub_locations as $device_sub_location)
        @php
        $selected = in_array($device_sub_location->building, request()->locations ?? [])
        ? "selected"
        : "";
        @endphp
        <option {{ $selected }} value="{{ $device_sub_location->building }}">
          {{ ucfirst($device_sub_location->building) }}
        </option>
        @endforeach
      </select>
      <select
        class="form-control select"
        data-placeholder="All Location-ID"
        name="rooms"
        id="rooms"
        multiple="multiple">
        <option></option>
        @foreach ($device_location_ids as $device_location_id)
        @php
        $selected = in_array($device_location_id->room, request()->locations ?? [])
        ? "selected"
        : "";
        @endphp
        <option {{ $selected }} value="{{ $device_location_id->room }}">
          {{ ucfirst($device_location_id->room) }}
        </option>
        @endforeach
      </select>
      <input
        type="text"
        class="form-control datepicker-basic"
        placeholder="Pick Start & End Date"
        name="date">
    </div>
  </div>
</div>
@endpush

@section("content")

<!-- 
	*
	* DEVELOPER NOTE
	*
	*
	* Status Type Widgets   = Every Each Card Widget.
	* Device Status         = Every Each Log, represented.
	* Status Type Widgets   = Need to be filtered, using status_type_widgets_convert, 
	*                       = because it's only showing the FALSY marked as read.
	*
	* Goodluck.
	* dirait.com
	-->

@php
$oldDate = old('date');
$dates = $oldDate ? explode(' - ', $oldDate) : null;
$startDate = $oldDate ? $dates[0] : now()->subYears(1)->startOf('hour')->format('Y-m-d H:i:s');
$endDate = $oldDate ? $dates[1] : now()->startOf('hour')->add(32, 'hour')->format('Y-m-d H:i:s');
$setting = App\Models\Setting::first();
@endphp
<div x-data="dashboard">
  <div
    class="modal fade"
    tabindex="-1"
    x-bind:id="'card-widget-modal'"
    x-bind:aria-labelledby="'card-widget-modal'"
    aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" x-text="data?.status_type_widgets?.find(item => item.status_type.id == selectedStatusType)?.status_type?.name?.toUpperCase()"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body overflow-auto text-nowrap">
          <table class="table table-center">
            <thead>
              <tr>
                <th class="">Actions</th>
                <th>Status</th>
                <th>Time</th>
                <th>Identity</th>
                <th>Location</th>
                <th>Cams</th>
                <th>LatLong</th>
              </tr>
            </thead>
            <tbody>
              <template x-for="item in data?.status_type_widgets?.find(item => item.status_type.id == selectedStatusType)?.status_type?.device_status">
                <tr>
                  <td class="align-middle">
                    <div class="d-flex align-items-center gap-2">
                      <button
                        data-bs-target="#card-widget-note-modal"
                        data-bs-toggle="modal"
                        @click="selectedDeviceStatus = item"
                        class="btn btn-success">
                        <i class="ph-eye"></i>
                      </button>
                    </div>
                  </td>
                  <td class="align-middle">
                    <template x-if="item.marked_as_read">
                      <button class="btn btn-success">
                        <i class="ph-check-circle"></i>
                      </button>
                    </template>
                    <template x-if="!item.marked_as_read">
                      <button class="btn btn-danger">
                        <i class="ph-question"></i>
                      </button>
                    </template>
                  </td>
                  <td class="align-middle">
                    <span x-text="moment(item.created_at).format('YYYY-MM-DD HH:mm:ss')"></span>
                  </td>
                  <td class="align-middle">
                    <ul class="mb-0">
                      <li>LOG ID: <span x-text="item.device_log.id"></span></li>
                      <li>DEVICE ID: <span x-text="item.device_id"></span></li>
                    </ul>
                  </td>
                  <td class="align-middle">
                    <ul class="mb-0">
                      <li><span x-text="item.device?.branch?.toUpperCase()"></span></li>
                      <li><span x-text="item.device?.building?.toUpperCase()"></span></li>
                      <li><span x-text="item.device?.room?.toUpperCase()"></span></li>
                    </ul>
                  </td>
                  <td class="align-middle">
                    <ul class="mb-0">
                      <template x-for="cam in item.device_log?.cam_payloads">
                        <li>
                          <a target="_blank" :href="`/storage/${cam.file}`">
                            <span x-text="cam.file_name"></span>-id: <span x-text="cam.id"></span>
                          </a>
                        </li>
                      </template>
                      <template x-if="item.device_log?.cam_payloads?.length === 0">
                        <li>No Image Available</li>
                      </template>
                    </ul>
                  </td>
                  <td class="align-middle">
                    <ul class="mb-0">
                      <template x-for="cam in item.device_log?.cam_payloads">
                        <li>
                          <a target="_blank" :href="`https://www.google.com/maps/search/?api=1&query=${cam.latlong}`">
                            <span x-text="cam.latlong"></span>-id: <span x-text="cam.id"></span>
                          </a>
                        </li>
                      </template>
                      <template x-if="item.device_log?.cam_payloads?.length === 0">
                        <li>No Coordinate Available</li>
                      </template>
                    </ul>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div
    class="modal fade"
    tabindex="-1"
    x-bind:id="'card-widget-note-modal'"
    x-bind:aria-labelledby="'card-widget-note-modal'"
    aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span x-text="'NOTE FOR LOG ID: ' + selectedDeviceStatus?.device_log?.id + ' - DEVICE ID: ' + selectedDeviceStatus?.device_id"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <textarea
            class="form-control mb-2"
            rows="5"
            style="resize: none;"
            x-model="selectedDeviceStatus.notes"></textarea>
          <div class="d-flex gap-2 align-items-center">
            <span>State: </span>
            <span class="state" :class="selectedDeviceStatus.is_normal_state ? 'btn btn-success' : 'btn btn-danger'">
              <span x-text="selectedDeviceStatus.is_normal_state ? 'Normal' : 'Not Normal'"></span>
            </span>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" @click="stream">
            <i class="ph-video-camera me-2"></i>
            STREAM
          </button>
          <template x-if="selectedDeviceStatus?.device?.publish_action?.length > 0">
            <template x-for="action in selectedDeviceStatus?.device?.publish_action">
              <button type="button" class="btn btn-success" @click="publishAction(action)">
                <span x-text="action.label?.toUpperCase()"></span>
              </button>
            </template>
          </template>
          <button type="button" class="btn btn-link ms-auto" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" @click="submitNote">Submit Note</button>
        </div>
        <div class="modal-streaming">
          <div
            style="aspect-ratio: 16/9"
            :style="{ display: isStreamingLoading ? 'flex' : 'none' }"
            class="w-100 flex-column gap-2 justify-content-center align-items-center">
            <div class="spinner-border spinner-border-lg" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <span>Please wait...</span>
          </div>
          <iframe
            style="aspect-ratio: 16/9"
            :style="{ display: isStreaming ? 'block' : 'none' }"
            class="w-100 card-widget-note-modal-iframe">
          </iframe>
        </div>
      </div>
    </div>
  </div>
  <div class="row gx-3">
    <template x-for="item in data?.status_type_widgets">
      <div class="col-lg-3 col-12">
        <div
          class="card text-white shadow-lg"
          :style="{
            backgroundColor: item.status_type.device_status.length === 0 
              ? item.status_type.color 
              : item.status_type.trigger_color
          }">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h3 class="mb-0 display-4">
                <span x-text="item.status_type?.device_status?.length"></span>
              </h3>
              <div class="d-flex justify-content-between align-items-start gap-2">
                <button
                  type="button"
                  class="btn btn-white p-1"
                  @click="selectedStatusType = item.status_type.id"
                  data-bs-target="#card-widget-modal"
                  data-bs-toggle="modal">
                  <i class="ph-table"></i>
                </button>
                <a
                  class="btn btn-white p-1"
                  :href="`/admin/status_types/${item.status_type.id}/history`"
                  target="_blank">
                  <i class="ph-clock"></i>
                </a>
              </div>
            </div>
            <h6 x-text="item.status_type?.name?.toUpperCase()"></h6>
          </div>
        </div>
      </div>
    </template>
  </div>
</div>
@push('js')
<script src="//unpkg.com/alpinejs" defer></script>
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('dashboard', () => ({
      init() {
        const alpineThis = this;
        this.triggerFetch();
        $('.datepicker-basic').daterangepicker({
          timePicker: true,
          showDropdowns: true,
          startDate: moment(this.startDate),
          endDate: moment(this.endDate),
          locale: {
            format: 'YYYY-MM-DD HH:mm:ss'
          }
        }).on('apply.daterangepicker', function(ev, picker) {
          alpineThis.date = picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD');
          alpineThis.triggerFetch();
        });
        $('#branches').select2({
          width: '100%'
        });
        $('#buildings').select2({
          width: '100%'
        });
        $('#rooms').select2({
          width: '100%'
        });
        $('#device_id').on('input', function() {
          if ($(this).val().length >= 3 || $(this).val().length == 0) {
            alpineThis.device_id = $(this).val();
            alpineThis.triggerFetch()
          }
        })
        $('#branches').on('change', function() {
          alpineThis.branches = $(this).val();
          alpineThis.triggerFetch()
        })
        $('#buildings').on('change', function() {
          alpineThis.buildings = $(this).val();
          alpineThis.triggerFetch()
        })
        $('#rooms').on('change', function() {
          alpineThis.rooms = $(this).val();
          alpineThis.triggerFetch()
        })
      },
      data: null,
      date: "{{ $oldDate }}",
      startDate: '{{ $startDate }}',
      endDate: '{{ $endDate }}',
      selectedStatusType: null,
      branches: [],
      buildings: [],
      rooms: [],
      selectedDeviceStatus: null,
      isStreaming: false,
      isStreamingLoading: false,
      async triggerFetch() {
        let url = '{{ route("dashboard.ajax") }}';
        let branches = this.branches;
        let buildings = this.buildings;
        let rooms = this.rooms;
        let date = this.date;
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
        await this.getFetch(url);
      },
      async getFetch(url = "/api/dashboard") {
        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
        });
        const res = await response.json();
        this.data = res;
        console.log("this.data", this.data)
      },
      socketListener() {
        window.Echo.channel('laravel_database_newDataChannel')
          .listen('.newDataEvent', async (e) => {
            console.log("newDataEvent", e)
            if (e?.message?.type === "stream_listener") {
              var topic = e?.message?.topic;
              var topicapp = topic.split('/')[0];
              var topicbranch = topic.split('/')[1];
              var topicbuilding = topic.split('/')[2];
              var iframe = $(
                `.card-widget-note-modal-` +
                topicapp +
                `-` +
                topicbranch +
                `-` +
                topicbuilding +
                ` ` +
                `.card-widget-note-modal-iframe`
              );
              var iframe_loading = $(
                `.card-widget-note-modal-` +
                topicapp +
                `-` +
                topicbranch +
                `-` +
                topicbuilding +
                ` ` +
                `.card-widget-note-modal-iframe-loading`
              );
              iframe.attr('src', 'https://' + e?.message?.plain_payload);
              iframe.show();
              iframe_loading.hide();
            } else if (e?.message?.type === "gethourbyroom") {
              // await printRegisteredLocation();
            } else {
              this.triggerFetch();
              if (e?.message?.data?.at(0)?.notes === "Normal State") {
                // do nothing
              } else if (e?.message?.data?.length > 0) {
                audio.play();
              }
            }
          })
          .listen('.camDataEvent', (e) => {
            console.log("camDataEvent", e)
            this.triggerFetch();
            // audio.play();
          });
      },
      async submitNote() {
        console.log("submitNote", this.selectedDeviceStatus)
      },
      async publishAction(action) {
        console.log("publishAction", action)
      },
      async stream() {
        this.isStreamingLoading = true;
        await new Promise(resolve => setTimeout(resolve, 1000));
        this.isStreaming = true;
        this.isStreamingLoading = false;
      }
    }))
  })
</script>
@endpush
@endsection