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

		$('.js-time-autocomplete').each( function() {
			$( this ).autocomplete({
				lookup: timeTags
			});
		});
	});
})(jQuery);