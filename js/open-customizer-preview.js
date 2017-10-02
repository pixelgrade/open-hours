(function ($, api) {
	api.bind('preview-ready', function () {
		// The IDs of all our widgets
		let open_widgets = open_hours.widget_ids;

		for (var i = 0; i < open_widgets.length; i++) {
//			console.log(open_widgets[i]);
			api(create_open_customizer_id(open_widgets[i]), function (value) {
				value.bind(function (message) {
					// deserialize the decoded value
					message = atob(message.encoded_serialized_instance);

					// Multiple regexes to parse the serialized_instance
					var expression = message.replace(/a:[0-9]+:/, '');
					expression = expression.replace(/s:[0-9]+:/, '');

					var occurences = expression.match(/;s:[0-9]+:/g || []).length;
					for (var i = 0; i < occurences; i++) {
						if (isOdd(i) == 0) {
							expression = expression.replace(/;s:[0-9]+:/, ':');
						} else {
							expression = expression.replace(/;s:[0-9]+:/, ',');
						}
					}
					expression = expression.replace(/;}$/, '}');

					try {
						var widgetValues = JSON.parse(expression);
					} catch (e) {
//						console.log(e.message);
					}


					// Change the closed label for our Schedule
					if (widgetValues !== 'undefined' && widgetValues.closed_label !== 'undefined') {
						$('#' + widgetValues['widget_id'] + ' .open-hours-closed').html(widgetValues.closed_label);
					}

					if (widgetValues !== 'undefined' && widgetValues['widget_id'] && widgetValues['widget_id'].includes('open_overview_widget')) {
						$.get({
							url: open_hours.wp_rest.root + 'open_hours/v1/get_schedule_content',
							beforeSend: function (xhr) {
								xhr.setRequestHeader('X-WP-Nonce', open_hours.wp_rest.nonce);
							},
							data: {
								'open_nonce': open_hours.wp_rest.open_nonce,
								values: widgetValues
							},
							success: function (e) {
								$('#' + widgetValues['widget_id'] + ' .widget-title + table').replaceWith(e);
							}
						});
					}

					// Replacements
					if (widgetValues !== 'undefined' && widgetValues['widget_id'] !== 'undefined' && widgetValues['widget_id'].includes('open_current_status_widget')) {
						//check if replacement tag is contained in open_label
						widgetValues['open_note'] = replace_tags(widgetValues['open_note']);
						widgetValues['closed_note'] = replace_tags(widgetValues['closed_note']);
					}

//					console.log(widgetValues);
					// Change the widget's content
					var section_id = '#' + widgetValues['widget_id'];
					var open_note_id = '#' + widgetValues['widget_id'] + '-openNote';
					var closed_note_id = '#' + widgetValues['widget_id'] + '-closeNote';

					$(section_id).find('h2').text(widgetValues['title']);
					$(open_note_id).text(widgetValues['open_note']);
					$(closed_note_id).text(widgetValues['closed_note']);
				});

			});
		}

		function replace_tags(value) {
			let string = '';

			if (/\{(.*?)\}/.test(value) == true) {
				string = value;
				let regex = /\{(.*?)\}/g,
					occurences = string.match(regex).length,
					match;

				for (let i = 0; i < occurences; i++) {
					match = /\{(.*?)\}/.exec(string);
					$.get({
						url: 'http://whatever.dev/wp-json/open_hours/v1/get_time',
						beforeSend: function (xhr) {
							xhr.setRequestHeader('X-WP-Nonce', open_hours.wp_rest.nonce);
						},
						data: {
							'open_nonce': open_hours.wp_rest.open_nonce,
							value: match[1]
						},
						dataType: 'jsonp',
						async: false,
						success: function (json) {
//							console.log(json);

						},
						error: function (data) {;
							if (data.status == 200) {
								string = string.replace(/\{(.*?)\}/, data.responseText);
							}
						}
					});
				}
			} else {
				return value;
			}

			return string;
		}

		function isOdd(num) {
			return num % 2;
		}

		function create_open_customizer_id(widgetId) {
			var number = widgetId.match(/\d+/)[0];

			if (widgetId.indexOf('current_status') > -1) {
				var customizer_id = 'widget_open_current_status_widget[' + number + ']';
			} else {
				if (widgetId.indexOf('overview') > -1) {
					var customizer_id = 'widget_open_overview_widget[' + number + ']';
				}
			}


			return customizer_id;
		}
	});
})(jQuery, wp.customize);