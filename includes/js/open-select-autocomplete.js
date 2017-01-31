(function ($) {
	$(document).ready(function(){
		var timeTags = [
				{ value: "{time}" },
				{ value: "{today}" },
				{ value: "{current-day}" },
				{ value: "{today-time}" },
				{ value: "{today-start-time}" },
				{ value: "{today-end-time}" },
				{ value: "{next-day}" },
				{ value: "{next-time}" },
				{ value: "{next-start-time}" },
				{ value: "{next-end-time}" }
		];

//		$('#widget-21_open_current_status_widget-5').bind('expand', function(){
//			console.log('expanded');
//		}).bind('collapse', function() {
//			console.log('collapsed');
//		});
//
//		$('#widget-21_open_current_status_widget-5').on('wp-collapse-menu', function(){
//			console.log('collapse menu');
//		});
//
//		$('#widget-21_open_current_status_widget-5').on('click', function(){
//			console.log('click');
//		});
//
//		$('div[id*="_open_current_status_"]').on('click', function() {
//			let testing = $('#' + $(this).attr('id') + ' .widefat');
//
//			testing.each(function(key, value){
//				let id = value.getAttribute('id');
//				$('#' + id).easyAutocomplete( options );
//			})
//
//		});

		function split( val ) {
			return val.split( / \s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		$( '.js-time-autocomplete' ).each( function (  ) {
			// don't navigate away from the field on tab when selecting an item
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