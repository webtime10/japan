jQuery(function ($) {
	if (typeof mapPlumAdmin !== 'undefined' && mapPlumAdmin.saveEscape) {
		window.location.replace(mapPlumAdmin.saveEscape);
		return;
	}

	$('.map-plum-lang-tabs a').on('click', function (e) {
		e.preventDefault();
		var target = $(this).attr('href');
		var $wrap = $(this).closest('.panel-body');
		$(this).closest('.map-plum-lang-tabs').find('li').removeClass('active');
		$(this).parent().addClass('active');
		$wrap.find('.tab-pane').removeClass('active');
		$(target).addClass('active');
	});

	initProductImageUpload($);
	initMarkerImageUpload($);
	initMarkerPicker($);
	initShortcodeCopy($);
});

function initProductImageUpload($) {
	var $selectBtn = $('#map-plum-product-image-select');
	if (!$selectBtn.length) {
		return;
	}

	var $url = $('#map-plum-product-image-url');
	var $id = $('#map-plum-product-image-id');
	var $idView = $('#map-plum-product-image-id-view');
	var $file = $('#map-plum-product-image-file');
	var $preview = $('#map-plum-product-image-preview');
	var frame;

	function syncImageIdView() {
		if ($idView.length) {
			$idView.text($id.val() || '0');
		}
	}

	function setPreview(src) {
		if (src) {
			if ($preview.is('img')) {
				$preview.attr('src', src);
			} else {
				$preview.replaceWith(
					$('<img>', {
						src: src,
						alt: '',
						class: 'map-plum-image-preview',
						id: 'map-plum-product-image-preview'
					})
				);
				$preview = $('#map-plum-product-image-preview');
			}
		} else if ($preview.is('img')) {
			$preview.replaceWith(
				$('<div>', {
					class: 'map-plum-image-preview map-plum-image-preview-empty',
					id: 'map-plum-product-image-preview',
					text: 'Нет изображения'
				})
			);
			$preview = $('#map-plum-product-image-preview');
		} else {
			$preview.text('Нет изображения');
		}
	}

	$selectBtn.on('click', function (e) {
		e.preventDefault();
		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: 'Выберите изображение',
			button: { text: 'Использовать' },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$id.val(attachment.id);
			$url.val(attachment.url);
			$file.val('');
			syncImageIdView();
			setPreview(attachment.url);
		});

		frame.open();
	});

	$('#map-plum-product-image-remove').on('click', function (e) {
		e.preventDefault();
		$id.val('');
		$url.val('');
		$file.val('');
		syncImageIdView();
		setPreview('');
	});

	$file.on('change', function () {
		var file = this.files && this.files[0];
		if (!file) {
			return;
		}
		$id.val('');
		$url.val('');
		syncImageIdView();
		var reader = new FileReader();
		reader.onload = function (ev) {
			setPreview(ev.target.result);
		};
		reader.readAsDataURL(file);
	});

	syncImageIdView();
}

function initMarkerImageUpload($) {
	var $selectBtn = $('#map-plum-marker-image-select');
	if (!$selectBtn.length) {
		return;
	}

	var $url = $('#map-plum-marker-image-url');
	var $id = $('#map-plum-marker-image-id');
	var $idView = $('#map-plum-marker-image-id-view');
	var $file = $('#map-plum-marker-image-file');
	var $preview = $('#map-plum-marker-image-preview');
	var frame;

	function syncImageIdView() {
		if ($idView.length) {
			$idView.text($id.val() || '0');
		}
	}

	function setPreview(src) {
		if (src) {
			if ($preview.is('img')) {
				$preview.attr('src', src);
			} else {
				$preview.replaceWith(
					$('<img>', {
						src: src,
						alt: '',
						class: 'map-plum-image-preview',
						id: 'map-plum-marker-image-preview'
					})
				);
				$preview = $('#map-plum-marker-image-preview');
			}
		} else if ($preview.is('img')) {
			$preview.replaceWith(
				$('<div>', {
					class: 'map-plum-image-preview map-plum-image-preview-empty',
					id: 'map-plum-marker-image-preview',
					text: 'Нет изображения'
				})
			);
			$preview = $('#map-plum-marker-image-preview');
		} else {
			$preview.text('Нет изображения');
		}
	}

	$selectBtn.on('click', function (e) {
		e.preventDefault();
		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: 'Выберите изображение',
			button: { text: 'Использовать' },
			multiple: false,
			library: { type: 'image' }
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$id.val(attachment.id);
			$url.val(attachment.url);
			$file.val('');
			syncImageIdView();
			setPreview(attachment.url);
		});

		frame.open();
	});

	$('#map-plum-marker-image-remove').on('click', function (e) {
		e.preventDefault();
		$id.val('');
		$url.val('');
		$file.val('');
		syncImageIdView();
		setPreview('');
	});

	$file.on('change', function () {
		var file = this.files && this.files[0];
		if (!file) {
			return;
		}
		$id.val('');
		$url.val('');
		syncImageIdView();
		var reader = new FileReader();
		reader.onload = function (ev) {
			setPreview(ev.target.result);
		};
		reader.readAsDataURL(file);
	});

	syncImageIdView();
}

function initMarkerPicker($) {
	var $picker = $('.map-plum-marker-picker');
	if (!$picker.length) {
		return;
	}

	var $select = $('#map-plum-marker-select');
	var $list = $('#map-plum-marker-selected');
	var $empty = $('.map-plum-marker-empty');

	function getSelectedIds() {
		var ids = [];
		$list.find('input[name="product_marker[]"]').each(function () {
			ids.push(String($(this).val()));
		});
		return ids;
	}

	function updateEmptyState() {
		if ($list.find('.map-plum-marker-chip').length) {
			$empty.addClass('hidden');
		} else {
			$empty.removeClass('hidden');
		}
	}

	function refreshSelectOptions() {
		var selected = getSelectedIds();
		$select.find('option').each(function () {
			var val = String($(this).val());
			if (!val) {
				return;
			}
			$(this).prop('disabled', selected.indexOf(val) !== -1);
		});
		$select.val('');
	}

	function addMarkerFromSelect() {
		var $opt = $select.find('option:selected');
		var id = String($opt.val());
		if (!id) {
			return;
		}
		if (getSelectedIds().indexOf(id) !== -1) {
			return;
		}

		var name = $opt.data('name') || ('Маркер #' + id);
		var region = $opt.data('region') || '';
		var label = region ? (name + ' (' + region + ')') : name;

		var $chip = $('<li class="map-plum-marker-chip"></li>').attr('data-id', id);
		var $label = $('<span class="map-plum-marker-chip-label"></span>');
		$label.append($('<strong></strong>').text(label));
		$chip.append($label);
		$chip.append($('<button type="button" class="map-plum-marker-remove" title="Убрать">&times;</button>'));
		$chip.append($('<input type="hidden" name="product_marker[]">').val(id));
		$list.append($chip);

		updateEmptyState();
		refreshSelectOptions();
	}

	$('#map-plum-marker-add').on('click', addMarkerFromSelect);

	$select.on('change', function () {
		if ($select.val()) {
			addMarkerFromSelect();
		}
	});

	$list.on('click', '.map-plum-marker-remove', function () {
		$(this).closest('.map-plum-marker-chip').remove();
		updateEmptyState();
		refreshSelectOptions();
	});

	updateEmptyState();
	refreshSelectOptions();
}

function initShortcodeCopy($) {
	var $feedback = $('#map-plum-copy-feedback');

	function showFeedback(text) {
		if (!$feedback.length) {
			return;
		}
		$feedback.text(text);
		window.clearTimeout(showFeedback._timer);
		showFeedback._timer = window.setTimeout(function () {
			$feedback.text('');
		}, 2000);
	}

	function copyText(text) {
		if (!text) {
			return;
		}
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(function () {
				showFeedback('Скопировано: ' + text);
			}).catch(function () {
				fallbackCopy(text);
			});
			return;
		}
		fallbackCopy(text);
	}

	function fallbackCopy(text) {
		var $ta = $('<textarea>').val(text).css({ position: 'fixed', left: '-9999px' }).appendTo('body');
		$ta[0].select();
		try {
			document.execCommand('copy');
			showFeedback('Скопировано: ' + text);
		} catch (err) {
			showFeedback('Не удалось скопировать');
		}
		$ta.remove();
	}

	$(document).on('click', '.map-plum-copy-shortcode', function (e) {
		e.preventDefault();
		copyText($(this).attr('data-copy') || '');
	});
}
