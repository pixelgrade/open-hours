(function ($) {
	$(document).ready(function(){
		var options = {
			data: [
				{ value: "{time}" },
				{ value: "{today}" },
				{ value: "{current-day}" },
				{ value: "{today-time}" },
				{ value: "{today-start-time}" },
				{ value: "{today-end-time}" },
				{ value: "{next-day}" },
				{ value: "{next-time}" },
				{ value: "{next-start-time}" },
				{ value: "{next-end-time}" },
			],

			getValue: "value",
		};
		$('#widget-21_open_current_status_widget-5').bind('expand', function(){
			console.log('expanded');
		}).bind('collapse', function() {
			console.log('collapsed');
		});

		$('#widget-21_open_current_status_widget-5').on('wp-collapse-menu', function(){
			console.log('collapse menu');
		});

		$('#widget-21_open_current_status_widget-5').on('click', function(){
			console.log('click');
		});

		$('div[id*="_open_current_status_"]').on('click', function() {
			let testing = $('#' + $(this).attr('id') + ' .widefat');

			testing.each(function(key, value){
				let id = value.getAttribute('id');
				$('#' + id).easyAutocomplete( options );
			})

		});

		// $( '#widget-open_current_status_widget-2-open_note' ).easyAutocomplete( options );
	});
})(jQuery);