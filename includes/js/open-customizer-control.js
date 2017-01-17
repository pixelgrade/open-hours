(function ($, api) {
	api.bind('ready', function () {
		console.log('ready dsa dss');

		$('#open_hours_overview-dummy').on('change', function(e){
			e.preventDefault();
			var currentValue = $(this).val();
			var hours = fourSq.util.HoursParser.parse(currentValue);
			var jsonHours = JSON.stringify(hours);

			var textarea = $("[data-customize-setting-link=open_hours_overview_setting]");
			textarea.val(jsonHours);
			textarea.trigger('change');

			return true;
		});


		// api('open_hours_overview_setting', function(value){
		// 	value.bind(function(to){
		// 		// On save submit - save our values to database
		// 		api.previewer.send('something-something', to);
		//
		// 	});
		// });

		<!-- WIDGETS -->
		// Wait for the site title control to be added.
		// let open_widgets = document.querySelectorAll('li[id^="customize-control-widget_open_current_status_widget"]');
		// for (var i = 0; i < open_widgets.length; i++) {
		// 	api(create_open_customizer_id( open_widgets[i].id ), function (value) {
		// 		value.bind(function(to){
		// 			var decodedValue = atob(to.encoded_serialized_instance);
		// 			api.previewer.send('something-something', decodedValue);
		// 		});
		// 		// console.log(open_widgets[i]);
		// 		// api.preview.send()
		// 		// value.send('current-status-content', function (to, from) {
		//
		// 		// 	console.log(decodedValue);
		// 		// });
		// 	});
		// }
		// console.log(open_widgets);
		// api.control('widget_open_current_status_widget[6]', function (value) {
		// 	console.log(value);
		// 	api.previewer.bind('current-status-content', function (message) {
		// 		console.info('Message sent from preview:', message);
		// 	});
		// });
	});

	// function create_open_customizer_id(widgetId) {
	// 	var number = widgetId.match(/\d+/)[0];
	// 	var customizer_id = 'widget_open_current_status_widget[' + number + ']';
	//
	// 	return customizer_id;
	// }
})(jQuery, wp.customize);