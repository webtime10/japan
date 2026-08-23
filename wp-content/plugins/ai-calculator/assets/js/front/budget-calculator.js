/**
 * Budget calculator — radio + datetimepicker (локаль из Polylang).
 */
jQuery(function ($) {
	'use strict';

	function getRoot() {
		var $app = $('#ai-calculator-budget-app');
		return $app.length ? $app : $('[data-ai-bg-step]');
	}

	function bindOptionToggle($root) {
		$root.find('.ai-bg__option-input').off('change.aiBg').on('change.aiBg', function () {
			$root.find('[data-ai-bg-option]').each(function () {
				var $option = $(this);
				var active = $option.find('.ai-bg__option-input').prop('checked');
				$option.toggleClass('ai-bg__option--active', active);
				$option.find('.ai-bg__date-input, .ai-bg__date-trigger, .ai-bg__input, button.ai-bg__field').prop('disabled', !active);
				$option.find('.ai-bg__field--date').toggleClass('is-disabled', !active);
			});
		}).trigger('change');
	}

	function initDatePickers() {
		var $root = getRoot();
		if (!$root.length || !$.fn.datetimepicker) {
			return;
		}

		var cfg = window.aiCalculatorBudget || {};
		var locale = cfg.datePickerLocale || 'en';
		var rtl = !!cfg.datePickerRtl;
		var weekStart = typeof cfg.datePickerWeekStart === 'number' ? cfg.datePickerWeekStart : 1;
		var today = new Date();
		var todayMin = new Date(today.getFullYear(), today.getMonth(), today.getDate());

		var $from = $root.find('[data-date-role="from"]');
		var $to = $root.find('[data-date-role="to"]');

		if (!$from.length || !$to.length) {
			return;
		}

		bindOptionToggle($root);

		if ($.datetimepicker.setLocale) {
			$.datetimepicker.setLocale(locale);
		}

		var pickerOptions = {
			timepicker: false,
			format: 'd.m.Y',
			formatDate: 'd.m.Y',
			closeOnDateSelect: true,
			closeOnInputClick: false,
			closeOnWithoutClick: true,
			dayOfWeekStart: weekStart,
			minDate: todayMin,
			rtl: rtl,
			onSelectDate: function (ct, $input) {
				if (!$input || !$input.length) {
					return;
				}
				var el = $input[0];
				el.dispatchEvent(new Event('input', { bubbles: true }));
				el.dispatchEvent(new Event('change', { bubbles: true }));
			},
		};

		if ($from.data('xdsoft_datetimepicker')) {
			$from.datetimepicker('destroy');
		}
		if ($to.data('xdsoft_datetimepicker')) {
			$to.datetimepicker('destroy');
		}

		$from.datetimepicker(pickerOptions);
		$to.datetimepicker(pickerOptions);

		$from.off('change.aiBg').on('change.aiBg', function () {
			$to.datetimepicker({ minDate: $.trim($(this).val()) || todayMin });
		});
	}

	function hidePicker($input) {
		var $picker = $input.data('xdsoft_datetimepicker');
		if ($picker && $picker.is(':visible')) {
			$input.datetimepicker('hide');
		}
	}

	function openPicker($input) {
		if (!$input.length || $input.is(':disabled')) {
			return;
		}

		var $root = getRoot();
		var $from = $root.find('[data-date-role="from"]');
		var $to = $root.find('[data-date-role="to"]');
		var $other = $input.is($from) ? $to : $from;

		hidePicker($other);

		if ($input.is($to)) {
			$input.datetimepicker({ minDate: $.trim($from.val()) || new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()) });
		}

		var $picker = $input.data('xdsoft_datetimepicker');
		if ($picker && $picker.is(':visible')) {
			return;
		}

		$input.datetimepicker('show');
	}

	var $root = getRoot();
	if (!$root.length) {
		return;
	}

	$root.on('mousedown', '.ai-bg__date-trigger, .ai-bg__date-input', function (e) {
		e.stopPropagation();
	});

	$root.on('click', '.ai-bg__date-trigger', function (e) {
		e.preventDefault();
		e.stopPropagation();
		openPicker($(this).closest('.ai-bg__field--date').find('.ai-bg__date-input'));
	});

	$root.on('click', '.ai-bg__date-input', function (e) {
		e.preventDefault();
		e.stopPropagation();
		openPicker($(this));
	});

	window.aiCalculatorBudgetInitPickers = initDatePickers;
	initDatePickers();
});
