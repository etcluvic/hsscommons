/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// Share links info pop-up
	var metadata = $('.metadata'),
		shareinfo = $('.shareinfo');

	if (shareinfo.length > 0) {
		$('.share')
			.on('mouseover', function() {
				shareinfo.addClass('active');
			})
			.on('mouseout', function() {
				shareinfo.removeClass('active');
			});
	}

	// Copy URL button
	$('#copy-url-btn').on('click', function() {
		// Get the current URL
		var currentUrl = window.location.href;

		// Get DOI for publication
		var doi = $(this).data('doi');

		if (doi) {
			// Use the Clipboard API to copy the DOI
			navigator.clipboard.writeText(doi)
			.then(function() {
				// Success: DOI copied to clipboard
				alert('DOI copied to clipboard!');
			})
			.catch(function(err) {
				// Error handling
				console.error('Error copying DOI: ', err);
			});
		} else {
			// Use the Clipboard API to copy the URL
			navigator.clipboard.writeText(currentUrl)
			.then(function() {
				// Success: URL copied to clipboard
				alert('URL copied to clipboard!');
			})
			.catch(function(err) {
				// Error handling
				console.error('Error copying URL: ', err);
			});
		}
	});
});
