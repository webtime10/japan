/**
 * Post form: reload auto-tags when post name matches a city direction.
 */
(function ($) {
	'use strict';

	var cfg = window.fccAutoTags || {};
	var timer = null;

	function setDirection(id) {
		$('#fcc-post-direction').val(id || 0);
	}

	function reloadAutoTags(directionId) {
		var $box = $('#fcc-auto-tags');
		var $hidden = $('#fcc-page-tags-json');
		if (!$box.length) {
			return;
		}

		$box.attr('data-direction', directionId || 0);
		setDirection(directionId);

		if (!directionId) {
			$box.html('<p class="description fcc-auto-tags-empty">Укажите название поста как имя города (направления) — теги подтянутся со страниц.</p>');
			if ($hidden.length) {
				$hidden.val('[]');
			}
			return;
		}

		$box.html('<p class="description">…</p>');

		$.getJSON(cfg.ajaxUrl || '', {
			action: 'fcc_get_direction_auto_tags',
			nonce: cfg.nonce || '',
			direction_id: directionId
		}).done(function (res) {
			if (!res || !res.success || !res.data) {
				$box.html('<p class="description">Ошибка загрузки тегов</p>');
				return;
			}
			$box.html(res.data.html || '');
			if ($hidden.length) {
				$hidden.val(JSON.stringify(res.data.tags || []));
			}
		}).fail(function () {
			$box.html('<p class="description">Ошибка загрузки тегов</p>');
		});
	}

	function resolveDirectionByName(name, done) {
		name = $.trim(name || '');
		if (!name) {
			done(0);
			return;
		}

		$.getJSON(cfg.ajaxUrl || '', {
			action: 'fcc_resolve_direction_by_name',
			nonce: cfg.nonce || '',
			name: name
		}).done(function (res) {
			var id = (res && res.success && res.data) ? (parseInt(res.data.direction_id, 10) || 0) : 0;
			done(id);
		}).fail(function () {
			done(0);
		});
	}

	$(document).ready(function () {
		$('#fcc-post-name').on('input change blur', function () {
			var name = $(this).val();
			clearTimeout(timer);
			timer = setTimeout(function () {
				resolveDirectionByName(name, reloadAutoTags);
			}, 350);
		});
	});
})(jQuery);
