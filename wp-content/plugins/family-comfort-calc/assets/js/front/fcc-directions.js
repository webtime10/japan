(function () {
	'use strict';

	var GAP = 20;

	function getPerView() {
		var w = window.innerWidth || document.documentElement.clientWidth;
		if (w <= 768) {
			return 1;
		}
		if (w <= 1200) {
			return 2;
		}
		return 3;
	}

	function FamilyComfortSlider(root) {
		this.root = root;
		this.sliderEl = root.querySelector('#fcc-family-comfort-slider');
		this.viewport = root.querySelector('.ai-family-comfort__slider-viewport');
		this.track = root.querySelector('#fcc-family-comfort-cards');
		this.pool = root.querySelector('#fcc-family-comfort-card-pool');
		this.prevBtn = root.querySelector('[data-fc-prev]');
		this.nextBtn = root.querySelector('[data-fc-next]');
		this.perView = 3;
		this.onResize = this.onResize.bind(this);
		this.onViewportScroll = this.onViewportScroll.bind(this);
		this.onTouchEnd = this.onTouchEnd.bind(this);
		this.resizeTimer = null;
		this.scrollTimer = null;
		this.touchStartX = 0;
		this.touchDeltaX = 0;
		var self = this;

		if (this.viewport) {
			window.addEventListener('resize', this.onResize);
			this.viewport.addEventListener('scroll', this.onViewportScroll, { passive: true });
			this.viewport.addEventListener('click', function (event) {
				if (Math.abs(self.touchDeltaX) <= 10) {
					return;
				}
				var link = event.target.closest('.ai-family-comfort__card-link');
				if (link) {
					event.preventDefault();
				}
			}, true);
			this.viewport.addEventListener('touchstart', function (event) {
				if (event.touches && event.touches[0]) {
					self.touchStartX = event.touches[0].clientX;
					self.touchDeltaX = 0;
				}
			}, { passive: true });
			this.viewport.addEventListener('touchmove', function (event) {
				if (event.touches && event.touches[0]) {
					self.touchDeltaX = event.touches[0].clientX - self.touchStartX;
				}
			}, { passive: true });
			this.viewport.addEventListener('touchend', this.onTouchEnd, { passive: true });
		}
	}

	FamilyComfortSlider.prototype.onResize = function () {
		var self = this;
		clearTimeout(this.resizeTimer);
		this.resizeTimer = setTimeout(function () {
			self.updateLayout();
		}, 120);
	};

	FamilyComfortSlider.prototype.updateLayout = function () {
		if (!this.viewport || !this.sliderEl.classList.contains('is-active')) {
			return;
		}

		this.perView = getPerView();
		var width = this.viewport.clientWidth;
		if (width <= 0) {
			return;
		}

		var cardWidth = (width - GAP * (this.perView - 1)) / this.perView;
		this.viewport.style.setProperty('--fc-per-view', String(this.perView));
		this.viewport.style.setProperty('--fc-card-width', cardWidth + 'px');
		this.updateArrows();
	};

	FamilyComfortSlider.prototype.getStep = function () {
		var card = this.track.querySelector('.ai-family-comfort__card');
		if (!card) {
			return 0;
		}
		return card.getBoundingClientRect().width + GAP;
	};

	FamilyComfortSlider.prototype.getCardCount = function () {
		return this.track ? this.track.querySelectorAll('.ai-family-comfort__card').length : 0;
	};

	FamilyComfortSlider.prototype.getMaxIndex = function () {
		return Math.max(0, this.getCardCount() - this.perView);
	};

	FamilyComfortSlider.prototype.getCurrentIndex = function () {
		var step = this.getStep();
		if (step <= 0) {
			return 0;
		}
		return Math.round(this.viewport.scrollLeft / step);
	};

	FamilyComfortSlider.prototype.goToIndex = function (index, animate) {
		var maxIndex = this.getMaxIndex();
		if (maxIndex <= 0) {
			return;
		}

		while (index < 0) {
			index += maxIndex + 1;
		}
		while (index > maxIndex) {
			index -= maxIndex + 1;
		}

		var left = index * this.getStep();
		if (animate) {
			this.viewport.scrollTo({ left: left, behavior: 'smooth' });
		} else {
			this.viewport.scrollLeft = left;
		}
	};

	FamilyComfortSlider.prototype.scrollByStep = function (direction) {
		if (!this.viewport) {
			return;
		}

		var maxIndex = this.getMaxIndex();
		if (maxIndex <= 0) {
			return;
		}

		this.goToIndex(this.getCurrentIndex() + direction, true);
	};

	FamilyComfortSlider.prototype.onViewportScroll = function () {
		var self = this;
		clearTimeout(this.scrollTimer);
		this.scrollTimer = setTimeout(function () {
			var maxIndex = self.getMaxIndex();
			if (maxIndex <= 0) {
				return;
			}

			var step = self.getStep();
			if (step <= 0) {
				return;
			}

			var index = Math.round(self.viewport.scrollLeft / step);
			var maxScroll = self.viewport.scrollWidth - self.viewport.clientWidth;

			if (index <= 0 && self.viewport.scrollLeft <= 1) {
				return;
			}

			if (index >= maxIndex && self.viewport.scrollLeft >= maxScroll - 1) {
				return;
			}

			self.goToIndex(index, false);
		}, 80);
	};

	FamilyComfortSlider.prototype.onTouchEnd = function () {
		var self = this;
		setTimeout(function () {
			var maxIndex = self.getMaxIndex();
			if (maxIndex <= 0) {
				return;
			}

			var step = self.getStep();
			var maxScroll = self.viewport.scrollWidth - self.viewport.clientWidth;
			var left = self.viewport.scrollLeft;
			var index = Math.round(left / step);

			if (left <= 2 && self.touchDeltaX > 30) {
				self.goToIndex(maxIndex, true);
				return;
			}

			if (left >= maxScroll - 2 && self.touchDeltaX < -30) {
				self.goToIndex(0, true);
				return;
			}

			self.goToIndex(index, false);
		}, 40);
	};

	FamilyComfortSlider.prototype.updateArrows = function () {
		if (!this.prevBtn || !this.nextBtn) {
			return;
		}

		var canScroll = this.getMaxIndex() > 0;
		this.prevBtn.classList.toggle('is-disabled', !canScroll);
		this.nextBtn.classList.toggle('is-disabled', !canScroll);
		this.sliderEl.classList.toggle('is-looping', canScroll);
	};

	FamilyComfortSlider.prototype.returnCardsToPool = function () {
		if (!this.track || !this.pool) {
			return;
		}

		var cards = Array.prototype.slice.call(this.track.querySelectorAll('.ai-family-comfort__card'));
		cards.forEach(function (card) {
			card.classList.remove('is-visible');
			card.hidden = true;
			resetCardTags(card);
			this.pool.appendChild(card);
		}, this);
	};

	function resetCardTags(card) {
		var tagsEl = card.querySelector('.ai-family-comfort__tags--collapsible');
		if (!tagsEl) {
			return;
		}
		tagsEl.classList.remove('is-expanded');
		card.classList.remove('is-tags-expanded');
		var moreBtn = tagsEl.querySelector('[data-fcc-tags-more]');
		if (moreBtn) {
			moreBtn.hidden = false;
		}
	}

	function expandCardTags(moreBtn) {
		var tagsEl = moreBtn.closest('.ai-family-comfort__tags');
		var card = moreBtn.closest('.ai-family-comfort__card');
		if (!tagsEl) {
			return;
		}
		tagsEl.classList.add('is-expanded');
		if (card) {
			card.classList.add('is-tags-expanded');
		}
		moreBtn.hidden = true;
	}

	FamilyComfortSlider.prototype.collapseAllTags = function () {
		if (!this.track) {
			return;
		}
		Array.prototype.forEach.call(this.track.querySelectorAll('.ai-family-comfort__card'), resetCardTags);
	};

	FamilyComfortSlider.prototype.showCards = function (cards) {
		var self = this;

		this.returnCardsToPool();

		if (!cards.length) {
			this.sliderEl.classList.remove('is-active');
			return;
		}

		cards.forEach(function (card) {
			card.hidden = false;
			card.classList.add('is-visible');
			self.track.appendChild(card);
		});

		this.sliderEl.classList.add('is-active');

		requestAnimationFrame(function () {
			if (self.viewport) {
				self.viewport.scrollLeft = 0;
			}
			self.updateLayout();
		});
	};

	FamilyComfortSlider.prototype.bindControls = function () {
		var self = this;

		if (this.prevBtn && !this.prevBtn.dataset.fcBound) {
			this.prevBtn.dataset.fcBound = '1';
			this.prevBtn.addEventListener('click', function () {
				if (!self.prevBtn.classList.contains('is-disabled')) {
					self.collapseAllTags();
					self.scrollByStep(-1);
				}
			});
		}

		if (this.nextBtn && !this.nextBtn.dataset.fcBound) {
			this.nextBtn.dataset.fcBound = '1';
			this.nextBtn.addEventListener('click', function () {
				if (!self.nextBtn.classList.contains('is-disabled')) {
					self.collapseAllTags();
					self.scrollByStep(1);
				}
			});
		}
	};

	function getSlider(root) {
		if (!root._fccSlider) {
			root._fccSlider = new FamilyComfortSlider(root);
			root._fccSlider.bindControls();
		}
		return root._fccSlider;
	}

	function listHasId(raw, id) {
		if (!raw || !id) {
			return false;
		}
		return raw.split(',').indexOf(String(id)) !== -1;
	}

	function collectMatchingCards(root, ageId, interestId) {
		var pool = root.querySelector('#fcc-family-comfort-card-pool');
		if (!pool) {
			return [];
		}

		return Array.prototype.filter.call(pool.querySelectorAll('.ai-family-comfort__card'), function (card) {
			var ageMatch = listHasId(card.getAttribute('data-fcc-age') || '', ageId);
			var interestMatch = listHasId(card.getAttribute('data-fcc-interest') || '', interestId);
			return ageMatch && interestMatch;
		});
	}

	function updateDirections(root, showResults) {
		var ageSelect = root.querySelector('#fcc-family-comfort-age');
		var interestSelect = root.querySelector('#fcc-family-comfort-interest');
		var emptyEl = root.querySelector('#fcc-family-comfort-empty');
		var slider = getSlider(root);

		if (!ageSelect || !interestSelect) {
			return;
		}

		var selectedAge = ageSelect.value;
		var selectedInterest = interestSelect.value;

		if (!showResults || !selectedAge || !selectedInterest) {
			slider.returnCardsToPool();
			slider.sliderEl.classList.remove('is-active');
			if (emptyEl) {
				emptyEl.hidden = true;
			}
			return;
		}

		var matching = collectMatchingCards(root, selectedAge, selectedInterest);

		if (emptyEl) {
			emptyEl.hidden = matching.length > 0;
		}

		slider.showCards(matching);
	}

	function initDirectionsRoot(root) {
		if (root.dataset.fccInit === '1') {
			updateDirections(root, false);
			return;
		}

		root.dataset.fccInit = '1';

		var ageSelect = root.querySelector('#fcc-family-comfort-age');
		var interestSelect = root.querySelector('#fcc-family-comfort-interest');
		var button = root.querySelector('.ai-family-comfort__button');

		getSlider(root);
		updateDirections(root, false);

		if (ageSelect) {
			ageSelect.addEventListener('change', function () {
				updateDirections(root, false);
			});
		}

		if (interestSelect) {
			interestSelect.addEventListener('change', function () {
				updateDirections(root, false);
			});
		}

		if (button) {
			button.addEventListener('click', function () {
				updateDirections(root, true);
			});
		}

		root.addEventListener('click', function (event) {
			var moreBtn = event.target.closest('[data-fcc-tags-more]');
			if (!moreBtn) {
				return;
			}
			event.preventDefault();
			event.stopPropagation();
			expandCardTags(moreBtn);
		});
	}

	function bootDirections() {
		document.querySelectorAll('.fcc-family-directions').forEach(function (root) {
			initDirectionsRoot(root);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootDirections);
	} else {
		bootDirections();
	}
}());
