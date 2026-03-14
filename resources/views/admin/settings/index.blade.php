@extends("admin.layout.main")

@push("header")
	<div class="page-header page-header-light">
		<div class="page-header-content d-lg-flex">
			<div class="d-flex">
				<h4 class="page-title mb-0">
					Setting - <span class="fw-normal">Detail</span>
				</h4>
			</div>
		</div>

		<div class="page-header-content d-lg-flex">
			<div class="d-flex">
				<div class="breadcrumb py-2">
					<a class="breadcrumb-item" href="/admin/dashboard"><i class="ph-house"></i></a>
					<a class="breadcrumb-item" href="{{ route("admin.settings.index") }}">Setting</a>
					<span class="breadcrumb-item active">{{ $data->app_name }}</span>
				</div>
				<a class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto"
					data-bs-toggle="collapse" href="#breadcrumb_elements">
					<i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
				</a>
			</div>
		</div>
	</div>
@endpush

@section("content")
	<div class="card shadow-none">
		<div class="card-header">
			<h5 class="mb-0">Edit Setting</h5>
		</div>

		<div class="card-body border-top">
			<div class="row g-lg-5 g-2">
				<div class="col-lg-8 col-12">
					<form id="settings-form" action="{{ route("admin.settings.update", $data->id) }}" method="POST"
						enctype="multipart/form-data">
						@csrf
						@method("PUT")

						{{-- App Name --}}
						<div class="row mb-3">
							<label for="app_name" class="col-lg-4 col-form-label">App Name</label>
							<div class="col-lg-8">
								<input id="app_name" class="form-control" value="{{ $data->app_name }}"
									name="app_name" placeholder="Type App Name" required type="text">
							</div>
						</div>

						{{-- MQTT Main Topic --}}
						<div class="row mb-3">
							<label for="mqtt_main_topic" class="col-lg-4 col-form-label">MQTT Main Topic</label>
							<div class="col-lg-8">
								<input id="mqtt_main_topic" class="form-control" value="{{ $data->mqtt_main_topic }}"
									name="mqtt_main_topic" placeholder="Type MQTT Main Topic" required type="text">
							</div>
						</div>

						{{-- Access Device Feature toggle --}}
						<div class="row mb-3">
							<label class="col-lg-4 col-form-label">Access Device Feature</label>
							<div class="col-lg-8 d-flex align-items-center">
								<label class="form-check form-check-inline mb-0">
									<input id="is_access_device" name="is_access_device" type="checkbox"
										class="form-check-input"
										{{ $data->is_access_device ? "checked" : "" }}>
									<span class="form-check-label">On / Off</span>
								</label>
								<small class="text-muted ms-3" style="font-size:0.78rem;">
									Enables the <strong>Access Control</strong> category card on the dashboard.
									Set status types to category <code>access_control</code> to appear here.
								</small>
							</div>
						</div>

						{{-- Location Widget Feature --}}
						<div class="row mb-3">
							<label class="col-lg-4 col-form-label">Location Widget Feature</label>
							<div class="col-lg-8 d-flex align-items-center">
								<label class="form-check form-check-inline mb-0">
									<input id="location_widget" name="location_widget" type="checkbox"
										class="form-check-input"
										{{ $data->location_widget ? "checked" : "" }}>
									<span class="form-check-label">On / Off</span>
								</label>
							</div>
						</div>

						{{-- Status Card Active (dashboard widgets) --}}
						<div class="row mb-3">
							<label for="status_types" class="col-lg-4 col-form-label">Status Card Active</label>
							<div class="col-lg-8">
								<select id="status_types" multiple="multiple" class="form-control select2"
									data-placeholder="Select Status Card Active" name="status_types[]">
									<option></option>
									@foreach ($status_types as $status_type)
										@php
											$selected = in_array($status_type->id, $status_type_widgets->pluck("status_type_id")->toArray()) ? "selected" : "";
										@endphp
										<option {{ $selected }} value="{{ $status_type->id }}">{{ $status_type->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						{{-- Email Active --}}
						<div class="row mb-3">
							<label for="email_users" class="col-lg-4 col-form-label">Email Active</label>
							<div class="col-lg-8">
								<select id="email_users" multiple="multiple" class="form-control select2"
									data-placeholder="Select Users Email Active" name="email_users[]">
									<option></option>
									@foreach ($users as $user)
										@php
											$selected = in_array($user->id, $data->email_users ?? []) ? "selected" : "";
										@endphp
										<option {{ $selected }} value="{{ $user->id }}">{{ $user->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						{{-- Logo --}}
						<div class="row mb-3">
							<label for="logo" class="col-lg-4 col-form-label">Logo</label>
							<div class="col-lg-8">
								<div class="row">
									<div class="col-12 col-md-4 mb-3 mb-md-0">
										<img src="{{ Storage::url($data->logo) }}" alt="logo"
											class="rounded img-fluid img-thumbnail"
											style="object-fit: cover; object-position: center;">
									</div>
									<div class="col-12 col-md-8">
										<input id="logo" type="file" class="form-control h-auto" name="logo">
									</div>
								</div>
							</div>
						</div>

						<div class="text-end">
							<button class="btn btn-primary" type="submit">
								Submit form <i class="ph-paper-plane-tilt ms-2"></i>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%',
            placeholder: function () {
                return $(this).data('placeholder');
            }
        });

        // Prevent double-submit from SweetAlert or accidental double clicks
        $('#settings-form').on('submit', function (e) {
            // Disable submit button immediately after first click
            $(this).find('[type="submit"]').prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span>Saving...'
            );
        });
    });
</script>
@endpush