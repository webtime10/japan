jQuery(function ($) {
	'use strict';

	var $picker = $('#ai-product-related-picker');
	if (!$picker.length || typeof aiCalculatorAdmin === 'undefined') {
		return;
	}

	var $category = $('#prod-category');
	var $chips = $('#ai-related-chips');
	var $search = $('#ai-related-search');
	var $suggestions = $('#ai-related-suggestions');
	var $hint = $picker.find('.ai-related-hint');
	var searchTimer = null;
	var ajaxRequest = null;

	function productId() {
		return parseInt($picker.data('product-id'), 10) || 0;
	}

	function languageId() {
		return parseInt($picker.data('language-id'), 10) || 0;
	}

	function categoryId() {
		return parseInt($category.val(), 10) || 0;
	}

	function selectedIds() {
		var ids = [];
		$chips.find('input[name="related_product_ids[]"]').each(function () {
			ids.push(parseInt($(this).val(), 10));
		});
		return ids;
	}

	function updateSearchState() {
		var hasCategory = categoryId() > 0;
		$search.prop('disabled', !hasCategory);
		if (!hasCategory) {
			hideSuggestions();
			$search.val('');
			$hint.text('Сначала выберите категорию — рекомендуемые только из неё.');
		} else {
			$hint.text('Только товары из той же категории. Введите 2+ буквы, выберите из подсказок.');
		}
	}

	function hideSuggestions() {
		$suggestions.empty().prop('hidden', true);
	}

	function addChip(id, name) {
		id = parseInt(id, 10);
		if (!id || selectedIds().indexOf(id) !== -1) {
			return;
		}

		var $chip = $('<span class="ai-related-chip"></span>').attr('data-id', id);
		$chip.append($('<span class="ai-related-chip__label"></span>').text(name));
		$chip.append(
			$('<button type="button" class="ai-related-chip__remove" aria-label="Remove">&times;</button>')
		);
		$chip.append(
			$('<input type="hidden" name="related_product_ids[]">').val(id)
		);
		$chips.append($chip);
	}

	function searchProducts(query) {
		if (ajaxRequest) {
			ajaxRequest.abort();
		}

		ajaxRequest = $.ajax({
			url: aiCalculatorAdmin.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ai_calculator_search_related_products',
				nonce: aiCalculatorAdmin.nonce,
				category_id: categoryId(),
				language_id: languageId(),
				product_id: productId(),
				q: query,
				exclude_ids: selectedIds()
			}
		})
			.done(function (response) {
				renderSuggestions(response && response.success ? response.data.items : []);
			})
			.fail(function () {
				hideSuggestions();
			});
	}

	function renderSuggestions(items) {
		$suggestions.empty();

		if (!items || !items.length) {
			$suggestions.prop('hidden', true);
			return;
		}

		items.forEach(function (item) {
			var $li = $('<li class="ai-related-suggestions__item" tabindex="0"></li>')
				.text(item.name)
				.attr('data-id', item.id)
				.attr('data-name', item.name);
			$suggestions.append($li);
		});

		$suggestions.prop('hidden', false);
	}

	$search.on('input', function () {
		var query = $.trim($search.val());

		clearTimeout(searchTimer);
		hideSuggestions();

		if (categoryId() <= 0 || query.length < 2) {
			return;
		}

		searchTimer = setTimeout(function () {
			searchProducts(query);
		}, 250);
	});

	$suggestions.on('click', '.ai-related-suggestions__item', function () {
		var id = parseInt($(this).data('id'), 10);
		var name = $(this).data('name') || $(this).text();
		addChip(id, name);
		$search.val('');
		hideSuggestions();
	});

	$suggestions.on('keydown', '.ai-related-suggestions__item', function (e) {
		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault();
			$(this).trigger('click');
		}
	});

	$chips.on('click', '.ai-related-chip__remove', function () {
		$(this).closest('.ai-related-chip').remove();
	});

	$category.on('change', function () {
		$chips.empty();
		updateSearchState();
		hideSuggestions();
		$search.val('');
	});

	$(document).on('click', function (e) {
		if (!$(e.target).closest('.ai-related-search-wrap').length) {
			hideSuggestions();
		}
	});

	updateSearchState();
});
