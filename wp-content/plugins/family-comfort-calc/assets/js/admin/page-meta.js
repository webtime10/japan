/**
 * Family Comfort: метабокс на странице — позиция и теги.
 */
(function ($) {
	'use strict';

	var cfg = window.fccPageMeta || {};
	var maxTags = parseInt(cfg.maxTags, 10) || 10;
	var boxId = cfg.boxId || 'family_comfort_calc_page';

	function positionFamilyComfortBox() {
		var $box = $('#' + boxId);
		if (!$box.length) {
			return;
		}

		var $ai = $('#my-acf-ai-importer-box');
		if ($ai.length) {
			$box.insertAfter($ai);
		} else {
			var $poststuff = $('#poststuff');
			if ($poststuff.length) {
				$poststuff.append($box);
			}
		}

		initPostboxToggle($box);
	}

	function initPostboxToggle($box) {
		if (!$box || !$box.length || $box.data('fcc-toggle-ready')) {
			return;
		}

		var $header = $box.children('.postbox-header');
		if (!$header.length) {
			var $hndle = $box.children('.hndle').first();
			if ($hndle.length) {
				$hndle.wrap('<div class="postbox-header"></div>');
				$header = $box.children('.postbox-header');
			} else {
				$header = $('<div class="postbox-header"></div>');
				$header.append('<h2 class="hndle ui-sortable-handle"><span>Family Comfort</span></h2>');
				$box.prepend($header);
			}
		}

		if (!$header.find('.handlediv').length) {
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

		$toggle.off('click.fcc').on('click.fcc', function (e) {
			e.preventDefault();
			e.stopPropagation();
			setClosed(!$box.hasClass('closed'));
		});

		$header.find('.hndle').off('click.fcc').on('click.fcc', function (e) {
			if ($(e.target).closest('.handlediv').length) {
				return;
			}
			e.preventDefault();
			setClosed(!$box.hasClass('closed'));
		});

		$box.data('fcc-toggle-ready', true);
	}

	function getTagsFromDom() {
		var tags = [];
		$('#fcc-page-tags-list .fcc-page-tag').each(function () {
			var $tag = $(this);
			var label = $.trim($tag.attr('data-label') || '');
			var url = $.trim($tag.attr('data-url') || '');
			if (label) {
				tags.push({ label: label, url: url });
			}
		});
		return tags;
	}

	function syncTagsHidden() {
		var tags = getTagsFromDom();
		$('#fcc-page-tags-json').val(JSON.stringify(tags));
		updateCounter(tags.length);
	}

	function updateCounter(count) {
		$('#fcc-page-tags-counter').text(count + ' / ' + maxTags);
		var $wrap = $('.fcc-page-tags');
		if (count >= maxTags) {
			$wrap.addClass('is-limit');
		} else {
			$wrap.removeClass('is-limit');
		}
	}

	function escapeHtml(text) {
		return $('<div>').text(text).html();
	}

	function buildTagChip(tag) {
		var $chip = $('<span>', {
			'class': 'fcc-page-tag',
			'data-label': tag.label,
			'data-url': tag.url || ''
		});

		$chip.append($('<span>', { 'class': 'fcc-page-tag__text', text: tag.label }));

		if (tag.url) {
			$chip.append($('<span>', {
				'class': 'fcc-page-tag__url',
				text: tag.url,
				title: tag.url
			}));
		}

		$chip.append($('<button>', {
			type: 'button',
			'class': 'fcc-page-tag__remove',
			'aria-label': 'Удалить тег',
			html: '&times;'
		}));

		return $chip;
	}

	function resetAddForm() {
		$('#fcc-page-tag-label').val('');
		$('#fcc-page-tag-url').val('');
		$('#fcc-page-tags-add-form').prop('hidden', true);
	}

	function showAddForm() {
		if (getTagsFromDom().length >= maxTags) {
			window.alert(cfg.limitReached || 'Можно добавить не больше 10 тегов.');
			return;
		}
		$('#fcc-page-tags-add-form').prop('hidden', false);
		$('#fcc-page-tag-label').trigger('focus');
	}

	function addTag() {
		var label = $.trim($('#fcc-page-tag-label').val());
		var url = $.trim($('#fcc-page-tag-url').val());
		var tags = getTagsFromDom();

		if (!label) {
			window.alert(cfg.emptyLabel || 'Введите текст тега.');
			$('#fcc-page-tag-label').trigger('focus');
			return;
		}

		if (tags.length >= maxTags) {
			window.alert(cfg.limitReached || 'Можно добавить не больше 10 тегов.');
			return;
		}

		$('#fcc-page-tags-list').append(buildTagChip({ label: label, url: url }));
		syncTagsHidden();
		resetAddForm();
	}

	function initMediaField() {
		$(document).on('click', '.fcc-page-meta-media-select', function (e) {
			e.preventDefault();

			if (typeof wp === 'undefined' || !wp.media) {
				return;
			}

			var $field = $(this).closest('.fcc-page-meta-media-field');
			var $input = $field.find('.fcc-page-meta-media-input');
			var $preview = $field.find('.fcc-page-meta-media-preview');
			var $clear = $field.find('.fcc-page-meta-media-clear');

			var frame = wp.media({
				title: cfg.mediaTitle || 'Выберите фото',
				button: { text: cfg.mediaButton || 'Использовать' },
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var url = attachment.url || '';
				$input.val(url);
				$preview.html(url ? '<img src="' + url + '" alt="">' : '');
				$clear.prop('disabled', !url);
			});

			frame.open();
		});

		$(document).on('click', '.fcc-page-meta-media-clear', function (e) {
			e.preventDefault();
			var $field = $(this).closest('.fcc-page-meta-media-field');
			$field.find('.fcc-page-meta-media-input').val('');
			$field.find('.fcc-page-meta-media-preview').empty();
			$(this).prop('disabled', true);
		});
	}

	function initCategoryChecks() {
		$('.fcc-page-meta-checkboxes').each(function () {
			var count = $(this).find('.fcc-page-meta-checkbox').length;
			$(this).toggleClass('fcc-page-meta-checkboxes--scroll', count > 10);
		});

		$(document).on('click', '.fcc-page-meta-clear', function (e) {
			e.preventDefault();
			var targetId = $(this).attr('data-target');
			if (!targetId) {
				return;
			}
			$('#' + targetId + ' input[type="checkbox"]').prop('checked', false);
		});
	}

	function initTags() {
		var $wrap = $('.fcc-page-tags');
		if (!$wrap.length) {
			return;
		}

		syncTagsHidden();

		$(document).on('click', '#fcc-page-tag-add', function (e) {
			e.preventDefault();
			showAddForm();
		});

		$(document).on('click', '#fcc-page-tag-ok', function (e) {
			e.preventDefault();
			addTag();
		});

		$(document).on('click', '#fcc-page-tag-cancel', function (e) {
			e.preventDefault();
			resetAddForm();
		});

		$(document).on('click', '.fcc-page-tag__remove', function (e) {
			e.preventDefault();
			$(this).closest('.fcc-page-tag').remove();
			syncTagsHidden();
		});

		$(document).on('keydown', '#fcc-page-tag-label, #fcc-page-tag-url', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				addTag();
			}
			if (e.key === 'Escape') {
				e.preventDefault();
				resetAddForm();
			}
		});
	}

	$(document).ready(function () {
		positionFamilyComfortBox();
		initMediaField();
		initCategoryChecks();
		initTags();
	});

	$(window).on('load', positionFamilyComfortBox);

	if (typeof acf !== 'undefined' && acf.add_action) {
		acf.add_action('ready', positionFamilyComfortBox);
	}
})(jQuery);
