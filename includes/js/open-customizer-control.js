(function ($, api) {
	api.bind('ready', function () {
		console.log(open_hours_control);
		$('#open_hours_overview-dummy').on('keyup', function(e) {
			e.preventDefault();
			var currentValue = $(this).val();
			var hours = fourSq.util.HoursParser.parse(currentValue);
			var jsonHours = JSON.stringify(hours);

			// Handle Preview
			$.get({
				url: open_hours_control.wp_rest.root + 'open_hours/v1/get_schedule_content',
				beforeSend: function (xhr) {
					xhr.setRequestHeader('X-WP-Nonce', open_hours_control.wp_rest.nonce);
				},
				data: {
					'open_nonce': open_hours_control.wp_rest.open_nonce,
					overview_option: jsonHours
				},
				success: function (e) {
					console.log(e);
					$('.preview_open_widget').html(e);
				}
			});

			// Handle parser
			var textarea = $("[data-customize-setting-link=open_hours_overview_setting]");
			textarea.val(jsonHours);
			textarea.trigger('change');

			return true;
		});

		var $wpOverlay = $( '.wp-full-overlay' );

		// Offset preview when Open Hours section is open
		$( '[id*="open_hours_overview_section"] > *:first-child' ).on( 'click', function() {
			$wpOverlay.toggleClass( 'is--open-hours-section-expanded' );
		} );

		// Add a div with the Open Hours Scheme
		$wpOverlay.prepend( $('<div class="open-scheme"></div>') );

		// Show the Open Hours Scheme
		$( document ).on( 'click', '.js-show-hours-scheme', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			$wpOverlay.addClass( 'show-open-hours-scheme' );
		} )

		// Hide the Open Hours Scheme
		$( document ).on( 'click', '.open-scheme', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			$wpOverlay.removeClass( 'show-open-hours-scheme' );
		} )

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

		$( '#widget-open_current_status_widget-4-open_note' ).easyAutocomplete( options );
	});
})(jQuery, wp.customize);