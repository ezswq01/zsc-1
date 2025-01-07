<script>
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

		// display block
		cloned_card_widget_html.css(
			'display', 'block'
		).attr(
			'id', `card-widget-inactive-location`
		);

		// a tag history
		cloned_card_widget_html.find('a').remove();

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

		// title count
		cloned_card_widget_html.find('h3').text(
			data?.data?.inactiveLocations 
				? Object.keys(data?.data?.inactiveLocations).length 
				: "Error!"
		).attr(
			'id', `card-widget-title-inactive-location`
		);

		$('#card-widgets').prepend(cloned_card_widget_html);
	}
	const active_locations = async (html, data) => {
		var cloned_card_widget_html = html.clone();

		// display block
		cloned_card_widget_html.css(
			'display', 'block'
		).attr(
			'id', `card-widget-active-location`
		);

		// a tag history
		cloned_card_widget_html.find('a').remove();

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

		// title count
		cloned_card_widget_html.find('h3').text(
			data?.data?.activeLocations 
				? Object.keys(data?.data?.activeLocations).length 
				: "Error!"
		).attr(
			'id', `card-widget-title-active-location`
		);

		$('#card-widgets').prepend(cloned_card_widget_html);
	}
	const registered_locations = async (html, data) => {
		var cloned_card_widget_html = html.clone();

		// display block
		cloned_card_widget_html.css(
			'display', 'block'
		).attr(
			'id', `card-widget-registered-location`
		);

		// a tag history
		cloned_card_widget_html.find('a').remove();

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

		// title count
		cloned_card_widget_html.find('h3').text(
			data?.data?.registeredLocations 
				? Object.keys(data?.data?.registeredLocations).length 
				: "Error!"
		).attr(
			'id', `card-widget-title-registered-location`
		);

		$('#card-widgets').prepend(cloned_card_widget_html);
	}
	document.addEventListener('DOMContentLoaded', async () => {
		// location_widgets
		@if ($setting->location_widget)
			const card_widget_html = $('#card-widget-example');
			var res = await getRegisteredLocation();
			inactive_locations(card_widget_html, res);
			active_locations(card_widget_html, res);
			registered_locations(card_widget_html, res);
		@endif
	})
</script>