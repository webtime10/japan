jQuery(function ($) {
	if (typeof aiCalculatorAdmin !== 'undefined' && aiCalculatorAdmin.saveEscape) {
		window.location.replace(aiCalculatorAdmin.saveEscape);
		return;
	}

	$('.ai-calculator-lang-tabs a').on('click', function (e) {
		e.preventDefault();
		var target = $(this).attr('href');
		var $wrap = $(this).closest('.panel-body');
		$(this).closest('.ai-calculator-lang-tabs').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$wrap.find('.tab-pane').removeClass('active');
		$(target).addClass('active');
	});

	initManufacturerLinkedSelect($, '#cat-manufacturer', '#cat-parent', {
		empty: '#cat-parent-empty',
		keepZeroOption: true
	});
	initManufacturerLinkedSelect($, '#prod-manufacturer', '#prod-category', {
		empty: '#prod-category-empty',
		keepZeroOption: true,
		resetOnChange: true
	});

	$(document).on('click', '.ai-calculator-sort-save', function (e) {
		e.preventDefault();

		var $button = $(this);
		var productId = parseInt($button.data('product-id'), 10) || 0;
		var $row = $button.closest('tr');
		var $input = $row.find('.ai-calculator-sort-input');
		var $status = $row.find('.ai-calculator-sort-status');
		var sortOrder = parseInt($input.val(), 10);

		if (isNaN(sortOrder)) {
			sortOrder = 0;
		}

		if (!productId || typeof aiCalculatorAdmin === 'undefined') {
			$status.removeClass('text-muted text-success').addClass('text-danger').text('AJAX не доступен');
			return;
		}

		$button.prop('disabled', true);
		$button.removeClass('btn-danger').addClass('btn-success');
		$status.removeClass('text-success text-danger').addClass('text-muted').text('Сохранение...');

		$.post(aiCalculatorAdmin.ajaxUrl, {
			action: 'ai_calculator_update_product_sort_order',
			nonce: aiCalculatorAdmin.nonce,
			product_id: productId,
			sort_order: sortOrder
		}, null, 'json').done(function (response) {
			if (!response || !response.success) {
				var message = response && response.data && response.data.message ? response.data.message : 'Ошибка сохранения';
				$status.removeClass('text-muted text-success').addClass('text-danger').text(message);
				return;
			}

			$button.removeClass('btn-success').addClass('btn-danger');
			$status.removeClass('text-muted text-danger').addClass('text-success').text('Сохранено');
			window.setTimeout(function () {
				$button.removeClass('btn-danger').addClass('btn-success');
				$status.text('');
			}, 1500);
		}).fail(function () {
			$status.removeClass('text-muted text-success').addClass('text-danger').text('Ошибка сохранения');
		}).always(function () {
			$button.prop('disabled', false);
		});
	});

	$(document).on('keydown', '.ai-calculator-inline-input, .ai-calculator-sort-input', function (e) {
		if (e.key === 'Enter') {
			e.preventDefault();
		}
	});

	$(document).on('click', '.ai-calculator-inline-save', function (e) {
		e.preventDefault();

		var $button = $(this);
		var productId = parseInt($button.data('product-id'), 10) || 0;
		var languageId = parseInt($button.data('language-id'), 10) || 0;
		var field = String($button.data('field') || '');
		var $cell = $button.closest('td');
		var $input = $cell.find('.ai-calculator-inline-input[data-field="' + field + '"]');
		var $status = $cell.find('.ai-calculator-inline-status');

		if (!productId || !languageId || !field || typeof aiCalculatorAdmin === 'undefined') {
			$status.removeClass('text-muted text-success').addClass('text-danger').text('AJAX не доступен');
			return;
		}

		$button.prop('disabled', true);
		$button.removeClass('btn-danger').addClass('btn-success');
		$status.removeClass('text-success text-danger').addClass('text-muted').text('Сохранение...');

		$.post(aiCalculatorAdmin.ajaxUrl, {
			action: 'ai_calculator_update_product_text_field',
			nonce: aiCalculatorAdmin.nonce,
			product_id: productId,
			language_id: languageId,
			field: field,
			value: $input.val()
		}, null, 'json').done(function (response) {
			if (!response || !response.success) {
				var message = response && response.data && response.data.message ? response.data.message : 'Ошибка сохранения';
				$status.removeClass('text-muted text-success').addClass('text-danger').text(message);
				return;
			}

			$button.removeClass('btn-success').addClass('btn-danger');
			$status.removeClass('text-muted text-danger').addClass('text-success').text('Сохранено');
			window.setTimeout(function () {
				$button.removeClass('btn-danger').addClass('btn-success');
				$status.text('');
			}, 1500);
		}).fail(function () {
			$status.removeClass('text-muted text-success').addClass('text-danger').text('Ошибка сохранения');
		}).always(function () {
			$button.prop('disabled', false);
		});
	});
});

/**
 * Show/hide linked select options by manufacturer_id.
 *
 * @param {jQuery} $
 * @param {string} manufacturerSelector
 * @param {string} targetSelector
 * @param {object} opts
 */
function initManufacturerLinkedSelect($, manufacturerSelector, targetSelector, opts) {
	var $manufacturer = $(manufacturerSelector);
	var $target = $(targetSelector);

	if (!$manufacturer.length || !$target.length) {
		return;
	}

	opts = opts || {};

	function filterOptions() {
		var mfrId = parseInt($manufacturer.val(), 10) || 0;
		var visible = 0;
		var selectedVisible = false;

		if (opts.hint) {
			$(opts.hint).toggle(!mfrId);
		}

		$target.find('option').each(function () {
			var $opt = $(this);
			var optMfr = parseInt($opt.attr('data-manufacturer-id'), 10) || 0;
			var isZero = opts.keepZeroOption && parseInt($opt.val(), 10) === 0;

			var show = isZero || (mfrId > 0 && optMfr === mfrId);

			$opt.prop('hidden', !show);
			if (!show) {
				$opt.prop('selected', false);
			} else if (!isZero) {
				visible++;
			}
			if (show && $opt.prop('selected')) {
				selectedVisible = true;
			}
		});

		if (!selectedVisible && opts.keepZeroOption) {
			$target.val('0');
		}

		if (opts.empty) {
			$(opts.empty).toggle(mfrId > 0 && visible === 0);
		}

		$target.prop('disabled', !mfrId);
	}

	$manufacturer.on('change', function () {
		if (opts.resetOnChange && opts.keepZeroOption) {
			$target.val('0');
		}
		filterOptions();
		$target.trigger('change');
	});
	filterOptions();
}
