/**
 * Family Comfort: метабокс на WP-странице (город radio / возраст / интересы / тег).
 */
(function ($) {
	'use strict';

	var cfg = window.fccWpPageMeta || {};
	var boxId = cfg.boxId || 'family_comfort_calc_page';

	function positionBox() {
		var $box = $('#' + boxId);
		if (!$box.length) {
			return;
		}

		var $ai = $('#my-acf-ai-importer-box');
		if ($ai.length) {
			$box.insertAfter($ai);
		}

		initToggle($box);
	}

	function initToggle($box) {
		if (!$box.length || $box.data('fcc-wp-toggle-ready')) {
			return;
		}

		var $header = $box.children('.postbox-header');
		if (!$header.length) {
			var $hndle = $box.children('.hndle').first();
			if ($hndle.length) {
				$hndle.wrap('<div class="postbox-header"></div>');
				$header = $box.children('.postbox-header');
			}
		}

		if ($header.length && !$header.find('.handlediv').length) {
			$header.append(
				'<div class="handle-actions hide-if-no-js">' +
					'<button type="button" class="handlediv" aria-expanded="true">' +
						'<span class="screen-reader-text">' + (cfg.toggleLabel || 'Toggle') + '</span>' +
						'<span class="toggle-indicator" aria-hidden="true"></span>' +
					'</button>' +
				'</div>'
			);
		}

		var $toggle = $header.find('.handlediv');
		var isClosed = !!cfg.isClosed;

		if (isClosed) {
			$box.addClass('closed');
			$toggle.attr('aria-expanded', 'false');
		} else {
			$box.removeClass('closed');
			$toggle.attr('aria-expanded', 'true');
		}

		function setClosed(closed) {
			$box.toggleClass('closed', closed);
			$toggle.attr('aria-expanded', closed ? 'false' : 'true');
			cfg.isClosed = closed;
			if (typeof postboxes !== 'undefined' && typeof postboxes.save_state === 'function' && typeof pagenow !== 'undefined') {
				postboxes.save_state(pagenow);
			}
		}

		$toggle.off('click.fccWp').on('click.fccWp', function (e) {
			e.preventDefault();
			e.stopPropagation();
			setClosed(!$box.hasClass('closed'));
		});

		$header.find('.hndle').off('click.fccWp').on('click.fccWp', function (e) {
			if ($(e.target).closest('.handlediv').length) {
				return;
			}
			e.preventDefault();
			setClosed(!$box.hasClass('closed'));
		});

		$box.data('fcc-wp-toggle-ready', true);
	}

	function initClear() {
		$('.fcc-wp-page-meta .fcc-page-meta-checkboxes, .fcc-wp-page-meta .fcc-page-meta-radios').each(function () {
			var count = $(this).find('.fcc-page-meta-checkbox, .fcc-page-meta-radio').length;
			$(this).toggleClass('fcc-page-meta-checkboxes--scroll', count > 10);
		});

		$(document).on('click', '.fcc-wp-page-meta-clear', function (e) {
			e.preventDefault();
			var targetId = $(this).attr('data-target');
			var type = $(this).attr('data-type') || 'checkbox';
			if (!targetId) {
				return;
			}
			if (type === 'radio') {
				$('#' + targetId + ' input[type="radio"]').prop('checked', false);
			} else {
				$('#' + targetId + ' input[type="checkbox"]').prop('checked', false);
			}
		});
	}

	$(document).ready(function () {
		positionBox();
		initClear();
	});

	$(window).on('load', positionBox);
})(jQuery);
