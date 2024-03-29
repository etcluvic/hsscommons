/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	if ($("#field-publish_up").length && $("#field-publish_down").length) {
		$('#field-publish_up, #field-publish_down').datetimepicker({
			controlType: 'slider',
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss',
			timezone: $('#field-publish_up').attr('data-timezone')
		});
	}

	$('.below')
		// Toggle text and classes when clicking reply
		.on('click', 'a.reply', function (e) {
			e.preventDefault();

			var frm = $('#' + $(this).attr('rel'));

			if (frm.hasClass('hide')) {
				frm.removeClass('hide');

				$(this)
					.addClass('active')
					.text($(this).attr('data-txt-active'));
			} else {
				frm.addClass('hide');
				$(this)
					.removeClass('active')
					.text($(this).attr('data-txt-inactive'));
			}
		})
		// Add confirm dialog to delete links
		.on('click', 'a.delete', function (e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	
	// Copy URL button
	$('#copy-url-btn').on('click', function() {
		// Get the current URL
		var currentUrl = window.location.href;
	
		// Use the Clipboard API to copy the URL
		navigator.clipboard.writeText(currentUrl)
			.then(function() {
				// Success: URL copied to clipboard
				alert('URL copied to clipboard!');
			})
			.catch(function(err) {
				// Error handling
				console.error('Error copying text: ', err);
			});
	});
});