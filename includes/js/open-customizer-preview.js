(function($, exports) {
	$(document).ready(function() {
		console.log($(document).find('#open_current_status_widget-2 .widget-title'));
		wp.customize('widget_open_current_status_widget[2]', function(value) {
			value.bind(function(to, from) {
				console.log(to);
				console.log(from);
				var decodedValue = atob(to.encoded_serialized_instance);

				$('#open_current_status_widget-2 .widget-title').text(to.title);

				console.log(_base64ToArrayBuffer(to.encoded_serialized_instance));
			});
		});


	});
})(jQuery, window);