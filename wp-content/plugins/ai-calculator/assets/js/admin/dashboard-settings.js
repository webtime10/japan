jQuery(document).ready(function ($) {
	'use strict';

	var $form = $('#ai-calculator-remote-site-form');
	if (!$form.length || typeof aiCalculatorAdmin === 'undefined') {
		return;
	}

	$form.on('submit', function (e) {
		e.preventDefault();

		var $btn = $form.find('[type="submit"]');
		$btn.prop('disabled', true);

		$.ajax({
			url: aiCalculatorAdmin.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ai_calculator_save_remote_site',
				nonce: aiCalculatorAdmin.nonce,
				url: $('#ai-calculator-remote-url').val(),
				api_key: $('#ai-calculator-remote-api-key').val()
			},
			success: function (response) {
				if (!response || !response.success) {
					window.alert(
						(response && response.data && response.data.message) || 'Ошибка'
					);
					return;
				}

				var activeUrl = response.data.activeUrl || '';
				if (activeUrl) {
					$('#ai-calculator-remote-url').val(activeUrl);
				}

				window.alert(response.data.message || 'Сохранено');
			},
			error: function () {
				window.alert('Ошибка сохранения');
			},
			complete: function () {
				$btn.prop('disabled', false);
			}
		});
	});
});
