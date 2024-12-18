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
                    multiple="multiple"
                >
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
                    multiple="multiple"
                >
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
                    multiple="multiple"
                >
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
                    name="date"
                >
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
	*
	* The code is too much already, please DO NOT save data into variable. 
	* Make it consistent by just counting the html element.
	*
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
	@include('admin.dashboard_v2_helper')
	@include('admin.dashboard_v2_html_appender')
	@push('js')
		<script>
			document.addEventListener('DOMContentLoaded', async () => {
				await triggerFetch();
			})
		</script>
		<script>
			const startDate = '{{ $startDate }}';
			const endDate = '{{ $endDate }}';
			$('.datepicker-basic').daterangepicker({
				timePicker: true,
				showDropdowns: true,
				startDate: moment(startDate),
				endDate: moment(endDate),
				locale: {
					format: 'YYYY-MM-DD HH:mm:ss'
				}
			}).on('apply.daterangepicker', function(ev, picker) {
				triggerFetch();
			});
		</script>
		<script>
			$('#branches').select2({
				width: '100%',
			});
			$('#buildings').select2({
				width: '100%',
			});
			$('#rooms').select2({
				width: '100%',
			});
			$('#device_id').on('input', function() {
				if ($(this).val().length >= 3 || $(this).val().length == 0) {
					triggerFetch()
				}
			})
			$('#branches, #buildings, #rooms').change(function() {
				triggerFetch()
			});
		</script>
	@endpush
@endsection