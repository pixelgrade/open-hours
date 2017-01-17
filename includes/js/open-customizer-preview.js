(function ($, api) {
	api.bind( 'preview-ready', function() {
		console.log(open_hours);
		api('open_hours_overview_setting', function(setting) {
			setting.bind(function(value){
				$('#some-random-id').empty()
				var something = prettyHoursParser(value);
				$.each(something, function(key, value){
					$('#some-random-id').append('<pre>' + key + ' - ' + value + '</pre>');
				});

			});
		});

		// Update Widget Texts
		let open_widgets = open_hours.widget_ids;

		for (var i = 0; i < open_widgets.length; i++) {
			console.log(open_widgets[i]);
			api(create_open_customizer_id(open_widgets[i]), function (value) {
				value.bind(function(message){
					// deserialize the decoded value
					message = atob(message.encoded_serialized_instance);

					// Handle the message received from the customizer
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
					} catch(e) {
						console.log(e.message);
					}

					console.log(widgetValues);
					console.log(open_widgets[i]);

					//change the widget's content
					var section_id = '#' + widgetValues['widget_id'];
					var open_note_id = '#' + widgetValues['widget_id'] + '-openNote';
					var close_note_id = '#' + widgetValues['widget_id'] + '-closeNote';

					$(section_id).find('h2').text(widgetValues['title']);
					$(open_note_id).text(widgetValues['open_note']);
					$(close_note_id).text(widgetValues['close_note']);
				});

			});
		}

		console.log('review ready');
		api.preview.bind( 'open_hours_overview_setting', function( message ) {
			console.info('dsadasdas', message);

			var hours = fourSq.util.HoursParser.parse(message);
			var jsonHours = JSON.stringify(hours);
			// var something = prettyHoursParser(jsonHours);



			// Parse days/hours
			// var output = $( '#some-random-id' );
			//
			// function parseHours() {
			// 	var hours = fourSq.util.HoursParser.parse(message);
			// 	output.empty();
			// 	console.log(stringifyHours(hours));
			// 	output.append($('<pre>' + stringifyHours(hours) + '</pre>'));
			// };
			//
			// function stringifyHours(hours) {
			// 	return JSON.stringify(hours, undefined, 2).replace(/\n/g, "<br>");
			// }
			//
			// // input.on('change keyup paste', parseHours);
			// parseHours();

			// $( '#open_current_status_widget-6 h2' ).text(widgetValues["title"]);
			//
			// $( '#open_current_status_widget-6-closeNote' ).text(widgetValues["close_note"]);

		} );

	} );

	function isOdd(num) { return num % 2;}

	// An overly complicated function that parses the dates from the json in the User friendly strings
	function prettyHoursParser( json ) {
		var finalArray = {
			'monday': 'closed',
			'tuesday': 'closed',
			'wednesday': 'closed',
			'thursday': 'closed',
			'friday': 'closed',
			'saturday': 'closed',
			'sunday': 'closed',
		};

		try {
			var jsonArray = JSON.parse(json);

			if (jsonArray) {
				var timeframes = jsonArray['timeframes'];
				for (var i = 0; i < timeframes.length; i++) {
					for (var j = 0; j < timeframes[i]['days'].length; j++) {
						var start = parseTime(timeframes[i]['open'][0]['start']);
						var end = parseTime(timeframes[i]['open'][0]['end']);

						//change this to something better
						switch (timeframes[i]['days'][j]) {
							case 1:
								finalArray['monday'] = start + ' - ' + end;
								break;
							case 2:
								finalArray['tuesday'] = start + ' - ' + end;
								break;
							case 3:
								finalArray['wednesday'] = start + ' - ' + end;
								break;
							case 4:
								finalArray['thursday'] = start + ' - ' + end;
								break;
							case 5:
								finalArray['friday'] = start + ' - ' + end;
								break;
							case 6:
								finalArray['saturday'] = start + ' - ' + end;
								break;
							case 7:
								finalArray['sunday'] = start + ' - ' + end;
								break;
							default:
								break;
						}

					}
				}
			}
		} catch(e){
			console.log(e.message);
		}

		return finalArray;
	}

	function parseTime( timeString ) {
		if (timeString.match(/^\+/) ) {
			timeString = timeString.replace(/^\+/, '');
			timeString = timeString.match(/../g).join(':');
		} else {
			if (timeString.match(/^0/)){
				timeString = timeString.match(/../g).join(':');
			} else {
				timeString = timeString.match(/../g).join(':');
			}
		}

		return timeString;
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

})(jQuery, wp.customize);