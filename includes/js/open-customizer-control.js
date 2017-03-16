(function ($, api) {
	api.bind('ready', function () {
		if ($('#open_hours_overview-dummy').val().length == 0) {
			var schedule = "Monday 10am - 3pm\nTuesday to Friday 9 - 17\nSat noon - 2am";
			$('#open_hours_overview-dummy').append(schedule);
		}

		$('#open_hours_overview-dummy').on('keyup', function(e) {
			e.preventDefault();
			var currentValue = $(this).val();
			currentValue = removeClosedDays(currentValue);

			var hours = fourSq.util.HoursParser.parse(currentValue);
			var jsonHours = JSON.stringify(hours);

			// Remove the days in which the business is closed. The parser doesn't need those days anyways.
			function removeClosedDays( schedule ) {
				var hoursString ='';
				var lines = schedule.split('\n');
				for (var i=0; i< lines.length; i++) {
					if (lines[i].includes('closed') || lines[i].includes('Closed') || !lines[i].match(/\d+/g)) {
						// don't add it to the list
					} else {
						hoursString += lines[i] + '\n';
					}
				}

				return hoursString;
			}
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
		$wpOverlay.prepend( $('<div class="opening-hours-timestamps-explained"></div>') );

		// Show the Open Hours Scheme
		$( document ).on( 'click', '.js-show-hours-scheme', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			$wpOverlay.addClass( 'show-open-hours-scheme' );
		} )

		// Hide the Open Hours Scheme
		$( document ).on( 'click', '.opening-hours-timestamps-explained', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			$wpOverlay.removeClass( 'show-open-hours-scheme' );
		} );

	});
})(jQuery, wp.customize);