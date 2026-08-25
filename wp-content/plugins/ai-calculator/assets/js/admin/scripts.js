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
	initFamilyComfortCategoryParent($, '#cat-manufacturer', '#cat-parent', '#cat-parent-group');
	initManufacturerLinkedSelect($, '#prod-manufacturer', '#prod-category', {
		empty: '#prod-category-empty',
		keepZeroOption: true,
		resetOnChange: true
	});
	initFamilyComfortProductPanel($, '#prod-manufacturer', '#prod-category', '#ai-calculator-family-comfort-panel');

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

	$(document).on('click', '.ai-calculator-media-select', function (e) {
		e.preventDefault();

		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}

		var $field = $(this).closest('.ai-calculator-media-field');
		var $input = $field.find('.ai-calculator-media-input');
		var $preview = $field.find('.ai-calculator-media-preview');
		var $clear = $field.find('.ai-calculator-media-clear');
		var title = typeof aiCalculatorAdmin !== 'undefined' && aiCalculatorAdmin.mediaTitle
			? aiCalculatorAdmin.mediaTitle
			: 'Select image';
		var buttonText = typeof aiCalculatorAdmin !== 'undefined' && aiCalculatorAdmin.mediaButton
			? aiCalculatorAdmin.mediaButton
			: 'Use image';

		var frame = wp.media({
			title: title,
			button: { text: buttonText },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var url = attachment.url || '';

			$input.val(url);
			$clear.prop('disabled', !url);

			if (url) {
				$('<img>', { src: url, alt: '' }).appendTo($preview.empty());
			} else {
				$preview.empty();
			}
		});

		frame.open();
	});

	$(document).on('click', '.ai-calculator-media-clear', function (e) {
		e.preventDefault();

		var $field = $(this).closest('.ai-calculator-media-field');
		$field.find('.ai-calculator-media-input').val('');
		$field.find('.ai-calculator-media-preview').empty();
		$(this).prop('disabled', true);
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
		if (opts.resetOnChange && opts.keepZeroOption && !$manufacturer.data('ai-skip-category-reset')) {
			$target.val('0');
		}
		filterOptions();
		$target.trigger('change');
	});
	filterOptions();
}

/**
 * Toggle Family Comfort card panel and general extra fields.
 *
 * @param {jQuery} $
 * @param {string} manufacturerSelector
 * @param {string} categorySelector
 * @param {string} panelSelector
 */
function initFamilyComfortProductPanel($, manufacturerSelector, categorySelector, panelSelector) {
	var $manufacturer = $(manufacturerSelector);
	var $category = $(categorySelector);
	var $panel = $(panelSelector);

	if (!$manufacturer.length || !$panel.length) {
		return;
	}

	function isFamilyComfortCategorySelected() {
		if (!$category.length) {
			return false;
		}

		var $catOpt = $category.find('option:selected');
		var catId = parseInt($category.val(), 10) || 0;

		return catId > 0 && $catOpt.attr('data-family-comfort') === '1';
	}

	function isFamilyComfortManufacturerSelected() {
		var $selected = $manufacturer.find('option:selected');
		var mfrId = parseInt($manufacturer.val(), 10) || 0;
		var familyComfortId = parseInt($panel.attr('data-manufacturer-id'), 10) || 0;

		return $selected.attr('data-family-comfort') === '1'
			|| (familyComfortId > 0 && mfrId === familyComfortId);
	}

	function syncManufacturerFromCategory() {
		if (!$category.length) {
			return;
		}

		var $catOpt = $category.find('option:selected');
		var catId = parseInt($category.val(), 10) || 0;
		var catMfr = parseInt($catOpt.attr('data-manufacturer-id'), 10) || 0;

		if (catId <= 0 || catMfr <= 0) {
			return;
		}

		var currentMfr = parseInt($manufacturer.val(), 10) || 0;
		if (currentMfr === catMfr) {
			return;
		}

		$manufacturer.data('ai-skip-category-reset', 1);
		$manufacturer.val(String(catMfr)).trigger('change');
		$category.val(String(catId));
		$manufacturer.removeData('ai-skip-category-reset');
	}

	function toggleFamilyComfortPanel() {
		var isFamilyComfort = isFamilyComfortManufacturerSelected() || isFamilyComfortCategorySelected();

		$panel.find('.ai-calculator-family-comfort-panel__hint').toggle(!isFamilyComfort);
		$panel.find('.ai-calculator-family-comfort-panel__active-hint').toggle(isFamilyComfort);
		$panel.find('.ai-calculator-family-comfort-card').toggle(isFamilyComfort);
		$('.prod-general-extra').toggle(!isFamilyComfort);
		$('.prod-russian-name').toggle(true);

		// Скрытые поля Family Comfort дублируют name/description/block* и затирают General при сохранении.
		$panel.find(':input').prop('disabled', !isFamilyComfort);
		$('.prod-general-extra').find(':input').prop('disabled', isFamilyComfort);
		$('.prod-russian-name').find(':input').prop('disabled', false);
	}

	$manufacturer.on('change', toggleFamilyComfortPanel);

	if ($category.length) {
		$category.on('change', function () {
			if (isFamilyComfortCategorySelected()) {
				syncManufacturerFromCategory();
			}
			toggleFamilyComfortPanel();
		});
	}

	toggleFamilyComfortPanel();
}

/**
 * Family Comfort category form: only root can be parent.
 *
 * @param {jQuery} $
 * @param {string} manufacturerSelector
 * @param {string} parentSelector
 * @param {string} groupSelector
 */
function initFamilyComfortCategoryParent($, manufacturerSelector, parentSelector, groupSelector) {
	var $manufacturer = $(manufacturerSelector);
	var $parent = $(parentSelector);
	var $group = $(groupSelector);

	if (!$manufacturer.length || !$parent.length || !$group.length) {
		return;
	}

	var familyComfortId = parseInt($group.attr('data-family-comfort-manufacturer-id'), 10) || 0;
	var rootCategoryId = parseInt($group.attr('data-family-comfort-root-category-id'), 10) || 0;
	var isRootCategory = $group.attr('data-is-family-comfort-root') === '1';

	function isFamilyComfortManufacturerSelected() {
		var mfrId = parseInt($manufacturer.val(), 10) || 0;
		var $selected = $manufacturer.find('option:selected');

		return $selected.attr('data-family-comfort') === '1'
			|| (familyComfortId > 0 && mfrId === familyComfortId);
	}

	function syncFamilyComfortParent() {
		var isFamilyComfort = isFamilyComfortManufacturerSelected();
		var $noneOption = $parent.find('option[value="0"]');
		var $rootOption = rootCategoryId > 0 ? $parent.find('option[value="' + rootCategoryId + '"]') : $();

		$('#cat-parent-family-comfort-root').toggle(isFamilyComfort && isRootCategory);
		$('#cat-parent-family-comfort-child').toggle(isFamilyComfort && !isRootCategory);

		if (!isFamilyComfort) {
			$parent.prop('disabled', false);
			$noneOption.prop('hidden', false);
			return;
		}

		if (isRootCategory) {
			$parent.val('0');
			$parent.prop('disabled', true);
			return;
		}

		$parent.prop('disabled', false);
		$noneOption.prop('hidden', true);

		$parent.find('option').each(function () {
			var $opt = $(this);
			var val = parseInt($opt.val(), 10) || 0;

			if (val === 0) {
				$opt.prop('hidden', true);
				return;
			}

			$opt.prop('hidden', rootCategoryId > 0 && val !== rootCategoryId);
		});

		if ($rootOption.length) {
			$parent.val(String(rootCategoryId));
		}
	}

	$manufacturer.on('change', syncFamilyComfortParent);
	syncFamilyComfortParent();
}
