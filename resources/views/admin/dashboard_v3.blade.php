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
          <template x-if="selectedDeviceStatus">
            <textarea
              class="form-control mb-2"
              rows="5"
              style="resize: none;"
              x-model="selectedDeviceStatus.notes"></textarea>
          </template>
          <div class="d-flex gap-2 align-items-center">
            <span>State: </span>
            <span class="state" :class="selectedDeviceStatus?.is_normal_state ? 'btn btn-success' : 'btn btn-danger'">
              <span x-text="selectedDeviceStatus?.is_normal_state ? 'Normal' : 'Not Normal'"></span>
            </span>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" @click="stream(selectedDeviceStatus?.device_id)">
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
            :style="{ display: isStreaming ? 'block' : 'none', backgroundColor: 'black' }"
            class="w-100 card-widget-note-modal-iframe"
            :src="iFrameUrl">
          </iframe>
        </div>
      </div>
    </div>
  </div>
  <div class="row gx-3">
    @if($setting->is_access_device)
    <div class="col-lg-3 col-12">
      <div
        class="card text-white shadow-lg"
        :style="{
          backgroundColor: data.absent_received_logs.length > 0 
            ? 'rgb(200, 0, 0)' 
            : 'rgb(0, 100, 0)'
        }">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="mb-0 display-4" x-text="data.absent_received_logs.length"></h3>
            <div class="d-flex justify-content-between align-items-start gap-2"></div>
          </div>
          <h6>ABSENT DEVICE</h6>
        </div>
      </div>
    </div>
    @endif
    <div class="col-lg-3 col-12">
      <div class="card text-white shadow-lg" style="background-color: rgb(0, 100, 0);">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="mb-0 display-4">
              <span x-text="data?.data?.registeredLocations ? Object.keys(data?.data?.registeredLocations).length : 'Error!'"></span>
            </h3>
            <div class="d-flex justify-content-between align-items-start gap-2">
              <button
                type="button"
                class="btn btn-white p-1"
                data-bs-toggle="modal"
                data-bs-target="">
                <i class="ph-table"></i>
              </button>
            </div>
          </div>
          <h6>REGISTERED LOCATION</h6>
        </div>
      </div>
      <div class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">REGISTERED LOCATION</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body overflow-auto text-nowrap">
              <table class="table table-center">
                <thead>
                  <tr>
                    <th class="">No.</th>
                    <th>Location ID</th>
                    <th>Location Confirmation</th>
                    <th>Active Hour</th>
                    <th>Inactive Hour</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="(value, key, index) in registeredLocations" :key="key">
                    <tr>
                      <td class="align-middle">
                        <span x-text="index + 1"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="key"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="registeredLocations[key][0]['last_ping_at'] || 'No ping data'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="registeredLocations[key][0]['active_hour'] || 'No active hour'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="registeredLocations[key][0]['inactive_hour'] || 'No inactive hour'"></span>
                      </td>
                      <td class="align-middle">
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`getHour('${registeredLocations[key][0]['id']}')`">
                          Get Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setActiveHour('${registeredLocations[key][0]['id']}')`">
                          Set Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setInctiveHour('${registeredLocations[key][0]['id']}')`">
                          Set Inactive Hours
                        </button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-link"
                data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card text-white shadow-lg" style="background-color: rgb(0, 100, 0);">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="mb-0 display-4"></h3>
            <div class="d-flex justify-content-between align-items-start gap-2">
              <button
                type="button"
                class="btn btn-white p-1"
                data-bs-toggle="modal"
                data-bs-target="">
                <i class="ph-table"></i>
              </button>
            </div>
          </div>
          <h6>ACTIVE LOCATION</h6>
        </div>
      </div>
      <div class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">ACTIVE LOCATION</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body overflow-auto text-nowrap">
              <table class="table table-center">
                <thead>
                  <tr>
                    <th class="">No.</th>
                    <th>Location ID</th>
                    <th>Location Confirmation</th>
                    <th>Active Hour</th>
                    <th>Inactive Hour</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="(value, key, index) in activeLocations" :key="key">
                    <tr>
                      <td class="align-middle">
                        <span x-text="index + 1"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="key"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="activeLocations[key][0]['last_ping_at'] || 'No ping data'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="activeLocations[key][0]['active_hour'] || 'No active hour'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="activeLocations[key][0]['inactive_hour'] || 'No inactive hour'"></span>
                      </td>
                      <td class="align-middle">
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`getHour('${registeredLocations[key][0]['id']}')`">
                          Get Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setActiveHour('${activeLocations[key][0]['id']}')`">
                          Set Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setInctiveHour('${activeLocations[key][0]['id']}')`">
                          Set Inactive Hours
                        </button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-link"
                data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-12">
      <div class="card text-white shadow-lg" style="background-color: rgb(0, 100, 0);">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="mb-0 display-4"></h3>
            <div class="d-flex justify-content-between align-items-start gap-2">
              <button
                type="button"
                class="btn btn-white p-1"
                data-bs-toggle="modal"
                data-bs-target="">
                <i class="ph-table"></i>
              </button>
            </div>
          </div>
          <h6>INACTIVE LOCATION</h6>
        </div>
      </div>
      <div class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">INACTIVE LOCATION</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body overflow-auto text-nowrap">
              <table class="table table-center">
                <thead>
                  <tr>
                    <th class="">No.</th>
                    <th>Location ID</th>
                    <th>Location Confirmation</th>
                    <th>Active Hour</th>
                    <th>Inactive Hour</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="(value, key, index) in inactiveLocations" :key="key">
                    <tr>
                      <td class="align-middle">
                        <span x-text="index + 1"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="key"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="inactiveLocations[key][0]['last_ping_at'] || 'No ping data'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="inactiveLocations[key][0]['active_hour'] || 'No active hour'"></span>
                      </td>
                      <td class="align-middle">
                        <span x-text="inactiveLocations[key][0]['inactive_hour'] || 'No inactive hour'"></span>
                      </td>
                      <td class="align-middle">
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`getHour('${inactiveLocations[key][0]['id']}')`">
                          Get Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setActiveHour('${inactiveLocations[key][0]['id']}')`">
                          Set Active Hours
                        </button>
                        <button
                          class="btn btn-sm btn-primary"
                          :onclick="`setInctiveHour('${inactiveLocations[key][0]['id']}')`">
                          Set Inactive Hours
                        </button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-link"
                data-bs-dismiss="modal">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
    var audio = new Audio('/mcc-notification.wav');
    Alpine.data('dashboard', () => ({
      init() {
        const alpineThis = this;
        alpineThis.triggerFetch();
        alpineThis.getRegisteredLocation();
        setInterval(async () => {
          alpineThis.getRegisteredLocation();
        }, 1000 * 60 * 1); // 1 minute
        $('.datepicker-basic').daterangepicker({
          timePicker: true,
          showDropdowns: true,
          startDate: moment(alpineThis.startDate),
          endDate: moment(alpineThis.endDate),
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

      /** Dashboard V3 - Data */
      data: null,
      selectedStatusType: null,
      selectedDeviceStatus: null,

      /** Dashboard V3 - Filters*/
      date: "{{ $oldDate }}",
      startDate: '{{ $startDate }}',
      endDate: '{{ $endDate }}',
      branches: [],
      buildings: [],
      rooms: [],

      /** Dashboard V3 - Streaming*/
      isStreaming: false,
      isStreamingLoading: false,
      iFrameUrl: "",

      /** Dashboard V3 - Locations*/
      registeredLocations: {},
      activeLocations: {},
      inactiveLocations: {},

      /** Dashboard V3 - Fetch*/
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
      },
      async submitNote() {
        const alpineThis = this;
        if (!alpineThis.selectedDeviceStatus) return alert('Something went wrong!');
        if (!alpineThis.selectedDeviceStatus?.notes) return alert('Please enter a note!');
        const data = {
          "_token": '{{ csrf_token() }}',
          "device_status_id": alpineThis.selectedDeviceStatus?.id,
          "notes": alpineThis.selectedDeviceStatus?.notes,
        };
        $.ajax({
          url: '/admin/device_status/notes',
          type: 'POST',
          data: data,
          success: async function(response) {
            await alpineThis.getFetch();
            alert('Note submitted successfully!');
          },
          error: function(error) {
            console.log("submitNote error", error);
          }
        })
      },
      async publishAction(action) {
        const alpineThis = this;
        if (!alpineThis.selectedDeviceStatus) return alert('Something went wrong!');
        if (!alpineThis.selectedDeviceStatus?.notes) return alert('Please enter a note!');
        const data = {
          "_token": '{{ csrf_token() }}',
          "id": action,
          "device_status_id": alpineThis.selectedDeviceStatus?.id,
          "log_id": alpineThis.selectedDeviceStatus?.device_log?.id,
          "notes": alpineThis.selectedDeviceStatus?.notes
        };
        $.ajax({
          url: '/admin/devices/publish',
          type: 'POST',
          data: data,
          success: async function(response) {
            await alpineThis.getFetch();
            alert('Published successfully!');
          },
          error: function(error) {
            console.log("publishAction error", error);
            alert('Failed to publish action!');
          }
        })
      },
      async stream(device_id) {
        const alpineThis = this;
        alpineThis.isStreamingLoading = true;
        alpineThis.iFrameUrl = "";
        alpineThis.isStreaming = false;
        const data = {
          '_token': '{{ csrf_token() }}',
          'device_id': device_id
        };
        $.ajax({
          url: '/admin/devices/publish-streaming',
          type: 'POST',
          data: data,
          success: async function(response) {
            console.log("stream success", response);
            alert('Requested streaming successfully!');
          },
          error: function(error) {
            console.log("stream error", error);
            alert('Failed to request streaming!');
            alpineThis.isStreamingLoading = false;
          }
        })
      },


      /** Dashboard V3 - Locations*/
      async getRegisteredLocation() {
        const alpineThis = this;
        $.ajax({
          url: '/admin/devices/get-registered-locations',
          type: 'post',
          data: {
            _token: '{{ csrf_token() }}'
          },
          success: async function(response) {
            console.log("getRegisteredLocation success", response);
            alpineThis.registeredLocations = response.data.registeredLocations || {};
            alpineThis.activeLocations = response.data.activeLocations || {};
            alpineThis.inactiveLocations = response.data.inactiveLocations || {};
          },
          error: async function(error) {
            console.log("getRegisteredLocation error", error);
            alert('Failed to get registered location!');
          }
        })
      },
      async getHour(deviceId) {
        $.ajax({
          url: '/admin/devices/get-hour',
          type: 'post',
          data: {
            _token: '{{ csrf_token() }}',
            device_id: deviceId
          },
          success: function(response) {
            console.log("getHour success", response);
            alert('Hour fetched successfully!');
          },
          error: function(error) {
            console.log("getHour error", error);
            alert('Failed to get hour!');
          }
        })
      },
      async setActiveHour(deviceId) {
        const hour = prompt('Please enter the hour value:');
        if (!hour) {
          alert('Please enter a valid hour value!');
          return;
        }
        const minute = prompt('Please enter the minute value:');
        if (!minute) {
          alert('Please enter a valid minute value!');
          return;
        }
        const time = hour + ':' + minute;
        $.ajax({
          url: '/admin/devices/set-active-hour',
          type: 'post',
          data: {
            _token: '{{ csrf_token() }}',
            device_id: deviceId,
            time: time,
          },
          success: function(response) {
            console.log("setActiveHour success", response);
            alert('Active hour set successfully!');
          },
          error: function(error) {
            console.log("setActiveHour error", error);
            alert('Failed to set active hour!');
          }
        })
      },
      async setInctiveHour(deviceId) {
        const hour = prompt('Please enter the hour value:');
        if (!hour) {
          alert('Please enter a valid hour value!');
          return;
        }
        const minute = prompt('Please enter the minute value:');
        if (!minute) {
          alert('Please enter a valid minute value!');
          return;
        }
        const time = hour + ':' + minute;
        $.ajax({
          url: '/admin/devices/set-inactive-hour',
          type: 'post',
          data: {
            _token: '{{ csrf_token() }}',
            device_id: deviceId,
            time: time,
          },
          success: function(response) {
            console.log("setInctiveHour success", response);
            alert('Inactive hour set successfully!');
          },
          error: function(error) {
            console.log("setInctiveHour error", error);
            alert('Failed to set inactive hour!');
          }
        })
      },


      socketListener() {
        const alpineThis = this;
        window.Echo.channel('laravel_database_newDataChannel')
          .listen('.newDataEvent', async (e) => {
            console.log("newDataEvent", e)
            if (e?.message?.type === "stream_listener") {
              alpineThis.iFrameUrl = e?.message?.data?.url;
              alpineThis.isStreaming = true;
              alpineThis.isStreamingLoading = false;
            } else if (e?.message?.type === "gethourbyroom") {
              alpineThis.getRegisteredLocation();
            } else {
              alpineThis.triggerFetch();
              if (e?.message?.data?.at(0)?.notes === "Normal State") {
                // do nothing
              } else if (e?.message?.data?.length > 0) {
                audio.play();
              }
            }
          })
          .listen('.camDataEvent', (e) => {
            console.log("camDataEvent", e)
            alpineThis.triggerFetch();
            // audio.play();
          });
      },
    }))
  })
</script>
@endpush
@endsection