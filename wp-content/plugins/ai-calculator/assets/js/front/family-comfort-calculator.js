(function () {
	'use strict';

	function updateFamilyComfort(root) {
		var ageSelect = root.querySelector('#ai-family-comfort-age');
		var interestSelect = root.querySelector('#ai-family-comfort-interest');
		var panels = root.querySelectorAll('[data-family-age-result]');

		if (!ageSelect || !interestSelect || !panels.length) {
			return;
		}

		var selectedAge = ageSelect.value;
		var selectedInterest = interestSelect.value;
		var hasExactMatch = false;

		panels.forEach(function (panel) {
			if (
				panel.getAttribute('data-family-age-result') === selectedAge &&
				panel.getAttribute('data-family-interest-result') === selectedInterest
			) {
				hasExactMatch = true;
			}
		});

		panels.forEach(function (panel) {
			var ageMatches = panel.getAttribute('data-family-age-result') === selectedAge;
			var interestMatches = panel.getAttribute('data-family-interest-result') === selectedInterest;

			panel.classList.toggle('is-active', ageMatches && (interestMatches || (!hasExactMatch && panel.getAttribute('data-family-interest-result') === 'animals')));
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.ai-family-comfort').forEach(function (root) {
			var ageSelect = root.querySelector('#ai-family-comfort-age');
			var interestSelect = root.querySelector('#ai-family-comfort-interest');
			var button = root.querySelector('.ai-family-comfort__button');

			updateFamilyComfort(root);

			if (ageSelect) {
				ageSelect.addEventListener('change', function () {
					updateFamilyComfort(root);
				});
			}

			if (interestSelect) {
				interestSelect.addEventListener('change', function () {
					updateFamilyComfort(root);
				});
			}

			if (button) {
				button.addEventListener('click', function () {
					updateFamilyComfort(root);
				});
			}
		});
	});
}());
