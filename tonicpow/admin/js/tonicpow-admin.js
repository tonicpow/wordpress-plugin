(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	if ($.fn.select2) {
		$('select[name^="tonicpow_goals"]').select2();
	}

})( jQuery );

function tonicpow_test_trigger(goal) {
	jQuery('#trigger_' + goal).attr('disabled', true);
	jQuery(document).ready(function($) {
		var hook_name = $('select[name="tonicpow_goals[' + goal + '][hook_name]"]').val();
		var delay_in_minutes = $('input[name="tonicpow_goals[' + goal + '][delay_in_minutes]"]').val();
		var custom_dimensions = $('input[name="tonicpow_goals[' + goal + '][custom_dimensions]"]').val();
		jQuery.post(ajaxurl, {
			action: "tonicpow_test_goal",
			goal,
			hook_name,
			delay_in_minutes,
			custom_dimensions,
		}, function(data) {
			console.log(data);
			if (data.error) {
				alert('ERROR: ' + data.error);
			} else {
				alert(data.message || 'OK');
			}
			jQuery('#trigger_' + goal).attr('disabled', false);
		})
		.fail(function() {
			alert('ERROR: the request failed to complete');
			jQuery('#trigger_' + goal).attr('disabled', false);
		});
	});
}
