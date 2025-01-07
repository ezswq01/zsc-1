@if ($setting->location_widget)
	<div id="card-widget-location-device" class="col-lg-4 col-12" style="display:none;">
		<div class="card text-white shadow-lg">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start">
					<h3 class="mb-0 display-3"></h3>
					<div class="d-flex justify-content-between align-items-start gap-2">
						<button 
							type="button" 
							class="btn btn-white p-1"
							data-bs-toggle="modal"
							data-bs-target=""
						>
							<i class="ph-table"></i>
						</button>
					</div>
				</div>
				<h6></h6>
			</div>
		</div>
	</div>
	<div id="card-widget-location-device-modal-" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"></h5>
					<button 
						type="button" 
						class="btn-close" 
						data-bs-dismiss="modal"
					></button>
				</div>
				<div class="modal-body overflow-auto text-nowrap">
					<table class="table table-center">
						<thead>
							<tr>
								<th class="">No.</th>
								<th>Location ID</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button 
						type="button" 
						class="btn btn-link" 
						data-bs-dismiss="modal"
					>
						Close
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		const card_widget_html = $('#card-widget-location-device');
		const card_widget_modal_html = $('#card-widget-location-device-modal-');

		const getRegisteredLocation = async () => {
			const res = await $.ajax({
				url: '/admin/devices/get-registered-locations',
				type: 'post',
				data: {
					_token: '{{ csrf_token() }}'
				},
				success: async function(response) {
					return response;
				},
				error: function(error) {
					console.log(error);
					alert('An error occured while retrieving location data!');
				}
			})
			return res;
		}
		const inactive_locations = async (html, data) => {
			var cloned_card_widget_html = html.clone();
			var cloned_card_widget_modal_html = card_widget_modal_html.clone();

			// change card id
			cloned_card_widget_html.attr(
				'id', `card-widget-inactive-location`
			);

			// change modal id
			cloned_card_widget_modal_html.attr(
				'id', `card-widget-location-device-modal-inactive-device`
			);

			// display block
			cloned_card_widget_html.css(
				'display', 'block'
			).attr(
				'id', `card-widget-inactive-location`
			);

			// background color
			cloned_card_widget_html.find('.card').css(
				'background-color', 'rgb(0, 100, 0)'
			).attr(
				'id', `card-widget-card-inactive-location`
			);

			// widget name
			cloned_card_widget_html.find('h6').text(
				'In-Active Location'
			).attr(
				'id', `card-widget-name-inactive-location`
			);

			// modal table append
			const table = cloned_card_widget_modal_html.find('table');
			const tbody = table.find('tbody');
			tbody.empty();
			if (data?.data?.inactiveLocations) {
				Object.keys(data?.data?.inactiveLocations).forEach((key, index) => {
					const tr  = $('<tr></tr>');
					const td1 = $('<td></td>').text(index + 1);
					const td2 = $('<td></td>').text(key);
					tr.append(td1, td2);
					tbody.append(tr);
				});
			}

			// modal title
			cloned_card_widget_modal_html.find('.modal-title').text(
				'In-Active Location'
			).attr(
				'id', `card-widget-location-device-modal-title-inactive-location`
			);

			// modal button data-bs-target
			cloned_card_widget_html.find('button').attr(
				'data-bs-target', `#card-widget-location-device-modal-inactive-device`
			);

			// title count
			cloned_card_widget_html.find('h3').text(
				data?.data?.inactiveLocations 
					? Object.keys(data?.data?.inactiveLocations).length 
					: "Error!"
			).attr(
				'id', `card-widget-title-inactive-location`
			);

			$('#card-widgets').prepend(cloned_card_widget_html);
			$('#card-widget-inactive-location').prepend(cloned_card_widget_modal_html);
		}
		const active_locations = async (html, data) => {
			var cloned_card_widget_html = html.clone();
			var cloned_card_widget_modal_html = card_widget_modal_html.clone();

			// change card id
			cloned_card_widget_html.attr(
				'id', `card-widget-active-location`
			);

			// change modal id
			cloned_card_widget_modal_html.attr(
				'id', `card-widget-location-device-modal-active-device`
			);

			// display block
			cloned_card_widget_html.css(
				'display', 'block'
			).attr(
				'id', `card-widget-active-location`
			);

			// background color
			cloned_card_widget_html.find('.card').css(
				'background-color', 'rgb(0, 100, 0)'
			).attr(
				'id', `card-widget-card-active-location`
			);

			// widget name
			cloned_card_widget_html.find('h6').text(
				'Active Location'
			).attr(
				'id', `card-widget-name-active-location`
			);

			// modal table append
			const table = cloned_card_widget_modal_html.find('table');
			const tbody = table.find('tbody');
			tbody.empty();
			if (data?.data?.activeLocations) {
				Object.keys(data?.data?.activeLocations).forEach((key, index) => {
					const tr  = $('<tr></tr>');
					const td1 = $('<td></td>').text(index + 1);
					const td2 = $('<td></td>').text(key);
					tr.append(td1, td2);
					tbody.append(tr);
				});
			}

			// modal title
			cloned_card_widget_modal_html.find('.modal-title').text(
				'Active Location'
			).attr(
				'id', `card-widget-location-device-modal-title-active-location`
			);

			// modal button data-bs-target
			cloned_card_widget_html.find('button').attr(
				'data-bs-target', `#card-widget-location-device-modal-active-device`
			);

			// title count
			cloned_card_widget_html.find('h3').text(
				data?.data?.activeLocations 
					? Object.keys(data?.data?.activeLocations).length 
					: "Error!"
			).attr(
				'id', `card-widget-title-active-location`
			);

			$('#card-widgets').prepend(cloned_card_widget_html);
			$('#card-widget-active-location').prepend(cloned_card_widget_modal_html);
		}
		const registered_locations = async (html, data) => {

			var cloned_card_widget_html = html.clone();
			var cloned_card_widget_modal_html = card_widget_modal_html.clone();

			// change card id
			cloned_card_widget_html.attr(
				'id', `card-widget-registered-location`
			);

			// change modal id
			cloned_card_widget_modal_html.attr(
				'id', `card-widget-location-device-modal-registered-device`
			);

			// display block
			cloned_card_widget_html.css(
				'display', 'block'
			).attr(
				'id', `card-widget-registered-location`
			);

			// background color
			cloned_card_widget_html.find('.card').css(
				'background-color', 'rgb(0, 100, 0)'
			).attr(
				'id', `card-widget-card-registered-location`
			);

			// widget name
			cloned_card_widget_html.find('h6').text(
				'Registered Location'
			).attr(
				'id', `card-widget-name-registered-location`
			);

			// modal table append
			const table = cloned_card_widget_modal_html.find('table');
			const tbody = table.find('tbody');
			tbody.empty();
			if (data?.data?.registeredLocations) {
				Object.keys(data?.data?.registeredLocations).forEach((key, index) => {
					const tr  = $('<tr></tr>');
					const td1 = $('<td></td>').text(index + 1);
					const td2 = $('<td></td>').text(key);
					tr.append(td1, td2);
					tbody.append(tr);
				});
			}

			// modal button data-bs-target
			cloned_card_widget_html.find('button').attr(
				'data-bs-target', `#card-widget-location-device-modal-registered-device`
			);

			// modal title
			cloned_card_widget_modal_html.find('.modal-title').text(
				'Registered Location'
			).attr(
				'id', `card-widget-location-device-modal-title-registered-location`
			);

			// title count
			cloned_card_widget_html.find('h3').text(
				data?.data?.registeredLocations 
					? Object.keys(data?.data?.registeredLocations).length 
					: "Error!"
			).attr(
				'id', `card-widget-title-registered-location`
			);

			$('#card-widgets').prepend(cloned_card_widget_html);
			$('#card-widget-registered-location').prepend(cloned_card_widget_modal_html);
		}

		document.addEventListener('DOMContentLoaded', async () => {
			// location_widgets
			var res = await getRegisteredLocation();
			inactive_locations(card_widget_html, res);
			active_locations(card_widget_html, res);
			registered_locations(card_widget_html, res);
		})
	</script>
@endif