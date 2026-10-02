/**
 * Post form: search city pages (template «Город») and fill image/url.
 */
(function ($) {
	'use strict';

	var cfg = window.fccCityPage || {};
	var ajaxUrl = cfg.ajaxUrl || '';
	var nonce = cfg.nonce || '';
	var minChars = parseInt(cfg.minChars, 10) || 3;
	var timer = null;
	var lastQuery = '';
	var $wrap;
	var $input;
	var $list;
	var $hidden;
	var $status;

	function setStatus(text) {
		if ($status && $status.length) {
			$status.text(text || '');
		}
	}

	function hideList() {
		$list.empty().prop('hidden', true);
	}

	function showItems(items) {
		$list.empty();

		if (!items || !items.length) {
			setStatus(cfg.i18n && cfg.i18n.empty ? cfg.i18n.empty : 'Ничего не найдено');
			hideList();
			return;
		}

		setStatus('');
		items.forEach(function (item) {
			var $btn = $('<button type="button" class="fcc-city-page-item"></button>');
			$btn.attr('data-id', item.id || 0);
			$btn.attr('data-title', item.title || '');
			$btn.attr('data-url', item.url || '');
			$btn.attr('data-image', item.image || '');
			$btn.append($('<span class="fcc-city-page-item__title"></span>').text(item.title || ''));
			if (item.url) {
				$btn.append($('<span class="fcc-city-page-item__url"></span>').text(item.url));
			}
			$list.append($('<li></li>').append($btn));
		});
		$list.prop('hidden', false);
	}

	function search(term) {
		term = $.trim(term || '');
		if (term.length < minChars) {
			hideList();
			setStatus('');
			return;
		}

		if (term === lastQuery) {
			return;
		}
		lastQuery = term;
		setStatus(cfg.i18n && cfg.i18n.searching ? cfg.i18n.searching : 'Поиск…');

		$.ajax({
			url: ajaxUrl,
			method: 'GET',
			dataType: 'json',
			data: {
				action: 'fcc_search_city_pages',
				nonce: nonce,
				q: term
			}
		})
			.done(function (res) {
				if (!res || !res.success || !res.data) {
					showItems([]);
					return;
				}
				showItems(res.data.items || []);
			})
			.fail(function () {
				setStatus(cfg.i18n && cfg.i18n.error ? cfg.i18n.error : 'Ошибка поиска');
				hideList();
			});
	}

	function scheduleSearch() {
		clearTimeout(timer);
		timer = setTimeout(function () {
			search($input.val());
		}, 300);
	}

	function fillFromPage($btn) {
		var title = $btn.attr('data-title') || '';
		var url = $btn.attr('data-url') || '';
		var image = $btn.attr('data-image') || '';
		var id = $btn.attr('data-id') || '';

		$hidden.val(id);
		$input.val(title);
		hideList();
		setStatus(title ? ((cfg.i18n && cfg.i18n.selected ? cfg.i18n.selected : 'Выбрано:') + ' ' + title) : '');

		var $name = $('#fcc-post-name');
		if ($name.length && (!$name.val() || $name.data('fcc-from-page'))) {
			$name.val(title).data('fcc-from-page', 1);
		}

		$('#fcc-post-url').val(url);
		$('#fcc-page-image').val(image);

		var $preview = $('#fcc-city-page-preview');
		var $thumb = $('#fcc-city-page-preview-thumb');
		var $urlLink = $('#fcc-city-page-preview-url');

		if (image) {
			$thumb.html('<img src="' + String(image).replace(/"/g, '&quot;') + '" alt="">');
		} else {
			$thumb.empty();
		}

		if (url) {
			$urlLink.attr('href', url).text(url);
		} else {
			$urlLink.attr('href', '#').text('');
		}

		if (image || url) {
			$preview.prop('hidden', false);
		} else {
			$preview.prop('hidden', true);
		}
	}

	function clearSelection() {
		$hidden.val('');
		setStatus('');
	}

	$(function () {
		$wrap = $('.fcc-city-page-picker');
		if (!$wrap.length) {
			return;
		}

		$input = $('#fcc-city-page-search');
		$list = $('#fcc-city-page-results');
		$hidden = $('#fcc-city-page-id');
		$status = $('#fcc-city-page-status');

		$input.on('input', function () {
			lastQuery = '';
			clearSelection();
			scheduleSearch();
		});

		$input.on('keydown', function (e) {
			if (e.key === 'Escape') {
				hideList();
			}
			if (e.key === 'Enter') {
				e.preventDefault();
				var $first = $list.find('.fcc-city-page-item').first();
				if ($first.length) {
					fillFromPage($first);
				}
			}
		});

		$list.on('click', '.fcc-city-page-item', function (e) {
			e.preventDefault();
			fillFromPage($(this));
		});

		$(document).on('click', function (e) {
			if (!$wrap.is(e.target) && $wrap.has(e.target).length === 0) {
				hideList();
			}
		});
	});
})(jQuery);

/**
 * Post form: search default-template pages → достопримечательности (circles).
 */
(function ($) {
	'use strict';

	var cfg = window.fccAttractionPage || {};
	var ajaxUrl = cfg.ajaxUrl || '';
	var nonce = cfg.nonce || '';
	var minChars = parseInt(cfg.minChars, 10) || 3;
	var timer = null;
	var lastQuery = '';
	var selectedUrl = '';
	var selectedTitle = '';
	var $wrap;
	var $input;
	var $list;
	var $status;
	var $selected;
	var $selectedPage;
	var $selectedLink;
	var $tagName;
	var $addBtn;
	var $cancelBtn;

	function setStatus(text) {
		if ($status && $status.length) {
			$status.text(text || '');
		}
	}

	function hideList() {
		$list.empty().prop('hidden', true);
	}

	function hideSelected() {
		selectedUrl = '';
		selectedTitle = '';
		$selected.prop('hidden', true);
		$selectedPage.text('');
		$selectedLink.attr('href', '#').text('');
		$tagName.val('');
	}

	function showSelected(title, url) {
		selectedTitle = title || '';
		selectedUrl = url || '';
		$selectedPage.text(selectedTitle);
		if (selectedUrl) {
			$selectedLink.attr('href', selectedUrl).text(selectedUrl);
		} else {
			$selectedLink.attr('href', '#').text('');
		}
		$tagName.val('');
		$selected.prop('hidden', false);
		$tagName.trigger('focus');
	}

	function showItems(items) {
		$list.empty();

		if (!items || !items.length) {
			setStatus(cfg.i18n && cfg.i18n.empty ? cfg.i18n.empty : 'Ничего не найдено');
			hideList();
			return;
		}

		setStatus('');
		items.forEach(function (item) {
			var $btn = $('<button type="button" class="fcc-city-page-item"></button>');
			$btn.attr('data-title', item.title || '');
			$btn.attr('data-url', item.url || '');
			$btn.append($('<span class="fcc-city-page-item__title"></span>').text(item.title || ''));
			if (item.url) {
				$btn.append($('<span class="fcc-city-page-item__url"></span>').text(item.url));
			}
			$list.append($('<li></li>').append($btn));
		});
		$list.prop('hidden', false);
	}

	function search(term) {
		term = $.trim(term || '');
		if (term.length < minChars) {
			hideList();
			setStatus('');
			return;
		}

		if (term === lastQuery) {
			return;
		}
		lastQuery = term;
		setStatus(cfg.i18n && cfg.i18n.searching ? cfg.i18n.searching : 'Поиск…');

		$.ajax({
			url: ajaxUrl,
			method: 'GET',
			dataType: 'json',
			data: {
				action: 'fcc_search_attraction_pages',
				nonce: nonce,
				q: term
			}
		})
			.done(function (res) {
				if (!res || !res.success || !res.data) {
					showItems([]);
					return;
				}
				showItems(res.data.items || []);
			})
			.fail(function () {
				setStatus(cfg.i18n && cfg.i18n.error ? cfg.i18n.error : 'Ошибка поиска');
				hideList();
			});
	}

	function scheduleSearch() {
		clearTimeout(timer);
		timer = setTimeout(function () {
			search($input.val());
		}, 300);
	}

	function pickAttraction($btn) {
		var title = $btn.attr('data-title') || '';
		var url = $btn.attr('data-url') || '';

		hideList();
		$input.val(title);
		lastQuery = '';
		setStatus(cfg.i18n && cfg.i18n.selected ? cfg.i18n.selected : 'Выбрано:');
		showSelected(title, url);
	}

	function addAttraction() {
		var label = $.trim($tagName.val() || '');
		var api = window.fccTagsApi;

		if (!selectedUrl) {
			setStatus(cfg.i18n && cfg.i18n.needPage ? cfg.i18n.needPage : 'Сначала выберите страницу');
			return;
		}

		if (!label) {
			setStatus(cfg.i18n && cfg.i18n.needName ? cfg.i18n.needName : 'Введите название достопримечательности');
			$tagName.trigger('focus');
			return;
		}

		if (!api || typeof api.add !== 'function') {
			return;
		}

		var ok = api.add({ label: label, url: selectedUrl });
		if (ok) {
			setStatus((cfg.i18n && cfg.i18n.added ? cfg.i18n.added : 'Добавлено:') + ' ' + label);
			$input.val('');
			lastQuery = '';
			hideSelected();
		} else {
			setStatus(cfg.i18n && cfg.i18n.limitOrDup ? cfg.i18n.limitOrDup : 'Уже добавлено или достигнут лимит');
		}
	}

	$(function () {
		$wrap = $('.fcc-attraction-picker');
		if (!$wrap.length) {
			return;
		}

		$input = $('#fcc-attraction-search');
		$list = $('#fcc-attraction-results');
		$status = $('#fcc-attraction-status');
		$selected = $('#fcc-attraction-selected');
		$selectedPage = $('#fcc-attraction-selected-page');
		$selectedLink = $('#fcc-attraction-selected-link');
		$tagName = $('#fcc-attraction-tag-name');
		$addBtn = $('#fcc-attraction-add');
		$cancelBtn = $('#fcc-attraction-cancel');

		$input.on('input', function () {
			lastQuery = '';
			scheduleSearch();
		});

		$input.on('keydown', function (e) {
			if (e.key === 'Escape') {
				hideList();
			}
			if (e.key === 'Enter') {
				e.preventDefault();
				var $first = $list.find('.fcc-city-page-item').first();
				if ($first.length) {
					pickAttraction($first);
				}
			}
		});

		$list.on('click', '.fcc-city-page-item', function (e) {
			e.preventDefault();
			pickAttraction($(this));
		});

		$addBtn.on('click', function (e) {
			e.preventDefault();
			addAttraction();
		});

		$cancelBtn.on('click', function (e) {
			e.preventDefault();
			hideSelected();
			$input.val('');
			setStatus('');
		});

		$tagName.on('keydown', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				addAttraction();
			}
		});

		$(document).on('click', function (e) {
			if (!$wrap.is(e.target) && $wrap.has(e.target).length === 0) {
				hideList();
			}
		});
	});
})(jQuery);
