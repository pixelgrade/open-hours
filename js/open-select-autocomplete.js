(function ($) {
	$(document).ready(function(){
		var timeTags = [
				{ value: "{time}" },
				{ value: "{today}" },
				{ value: "{today-timeframe}" },
				{ value: "{today-opening-time}" },
				{ value: "{today-closing-time}" },
				{ value: "{next-opening-day}" },
				{ value: "{next-opening-timeframe}" },
				{ value: "{next-opening-time}" },
				{ value: "{next-closing-time}" }
		];

		function split( val ) {
			return val.split( / \s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}
//		console.log($('div[id*="_open_current_status_"]'));


		$( '.js-time-autocomplete' ).each( function (  ) {
			// don't navigate away from the field on tab when selecting an item
//			console.log($(this));
			$( this ).on( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
				     $( this ).autocomplete( "instance" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					response( $.ui.autocomplete.filter(
						timeTags, extractLast( request.term ) ) );
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( " " );
					return false;
				}
			});
		});
	});
})(jQuery);