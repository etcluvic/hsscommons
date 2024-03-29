/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function (jq) {
	var $ = jq;

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

	$('a.abuse').fancybox({
		type: 'ajax',
		width: 500,
		height: 'auto',
		autoSize: false,
		fitToView: false,
		titleShow: false,
		tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
		},
		beforeLoad: function() {
			href = $(this).attr('href');
			$(this).attr('href', href.nohtml());
		},
		afterShow: function() {
			var frm = $('#hubForm-ajax'),
				self = $(this.element[0]);

			if (frm.length) {
				frm.on('submit', function(e) {
					e.preventDefault();
					$.post($(this).attr('action'), $(this).serialize(), function(data) {
						var response = JSON.parse(data);

						if (!response.success) {
							frm.prepend('<p class="error">' + response.message + '</p>');
							return;
						} else {
							$('#sbox-content').html('<p class="passed">' + response.message + '</p>');
							$('#c' + response.id)
								.find('.comment-body')
								.first()
								.html('<p class="warning">' + self.attr('data-txt-flagged') + '</p>');
						}

						setTimeout(function(){
							$.fancybox.close();
						}, 2 * 1000);
					});
				});
			}
		}
	});

	if ($('#hubForm').length > 0) {
		$('input.datetime-field').datetimepicker({
			controlType: 'slider',
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss',
			timezone: $('input.datetime-field').attr('data-timezone')
		});
	}

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
