(function ($, api) {
	api.bind( 'preview-ready', function() {
		api('open_hours_overview_setting', function(setting) {
			setting.bind(function(value){
				var something = prettyHoursParser(value);
				console.log(something);
			});
		});
		console.log('review ready');
		api.preview.bind( 'open_hours_overview_setting', function( message ) {
			console.info('dsadasdas', message);

			var hours = fourSq.util.HoursParser.parse(message);
			var jsonHours = JSON.stringify(hours);
			// var something = prettyHoursParser(jsonHours);

			// var widgetValues = [];
			// message = message.replace(/s:[0-9]*:"([a-zA-Z0-9_\s]*)"/gi, function(match, group){
			// 	console.log(group);
			// 	widgetValues.push(group);
			// });
			//
			// var newArray = [];
			// for (var i=0; i < widgetValues.length; i++) {
			// 	if (isOdd(i) == 0) {
			// 		newArray[widgetValues[i]] = widgetValues[i+1];
			// 	}
			// }
			//
			// console.log(newArray['title']);

			// Handle the message received from the customizer
			// var expression = message.replace(/a:[0-9]+:/, '');
			// expression = expression.replace(/s:[0-9]+:/, '');
			//
			// var occurences = expression.match(/;s:[0-9]+:/g || []).length;
			// for (var i = 0; i < occurences; i++) {
			// 	if (isOdd(i) == 0) {
			// 		expression = expression.replace(/;s:[0-9]+:/, ':');
			// 	} else {
			// 		expression = expression.replace(/;s:[0-9]+:/, ',');
			// 	}
			// }
			// expression = expression.replace(/;}$/, '}');
			//
			//
			// try {
			// 	var widgetValues = JSON.parse(expression);
			// } catch(e) {
			// 	console.log(e.message);
			// }

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

		var jsonArray = JSON.parse(json);
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

})(jQuery, wp.customize);