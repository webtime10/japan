/**
 * Family Comfort Calc admin scripts.
 */
(function ($) {
	'use strict';

	$(function () {
		if (window.fccAdmin && window.fccAdmin.saveEscape) {
			$(document).on('keydown', function (e) {
				if (e.key === 'Escape') {
					window.location.href = window.fccAdmin.saveEscape;
				}
			});
		}
	});
})(jQuery);
