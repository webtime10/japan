  (function(){
    const openButtons = Array.from(document.querySelectorAll('.button-lupa-up'));
    const overlays = Array.from(document.querySelectorAll('.search-overlay'));
    if (!openButtons.length || !overlays.length) return;

    function getOverlay(button) {
      const root = button.closest('header') || document;
      return root.querySelector('.search-overlay') || overlays[0];
    }

    function openOverlay(overlay) {
      if (!overlay) return;
      overlay.classList.add('is-open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.classList.add('search-overlay-open');
      const input = overlay.querySelector('.search-field');
      if (input) {
        setTimeout(() => input.focus(), 20);
      }
    }

    function closeOverlay(overlay) {
      if (!overlay) return;
      overlay.classList.remove('is-open');
      overlay.setAttribute('aria-hidden', 'true');
      if (!document.querySelector('.search-overlay.is-open')) {
        document.body.classList.remove('search-overlay-open');
      }
    }

    openButtons.forEach((button) => {
      if (button.dataset.searchBound === '1') return;
      button.dataset.searchBound = '1';
      button.addEventListener('click', () => {
        openOverlay(getOverlay(button));
      });
    });

    overlays.forEach((overlay) => {
      const closeBtn = overlay.querySelector('#search-close-btn, .close-btn');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => closeOverlay(overlay));
      }
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          closeOverlay(overlay);
        }
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      overlays.forEach((overlay) => {
        if (overlay.classList.contains('is-open')) {
          closeOverlay(overlay);
        }
      });
    });
  })();

  (function(){
    const wrap = document.querySelector('.cats-wrap_r');
    if(!wrap) return;

    let isDown = false, moved = false, startX = 0, startScroll = 0;
    const TH = 5; // порог пикселей для драг-срабатывания
    // Не перехватывать жест: кликабельные блоки (.click и т.п.) — скролл-перетаскивание не начинаем
    function isInteractiveTarget(el) {
      return !!(el && el.closest && el.closest('.click'));
    }

    wrap.addEventListener('pointerdown', (e) => {
      if (!e.target.closest('.cat-item_r')) return; // кликнули вне списка
      if (isInteractiveTarget(e.target)) return;
      isDown = true;
      moved  = false;
      startX = e.pageX;
      startScroll = wrap.scrollLeft;
      wrap.classList.add('dragging');
      wrap.setPointerCapture?.(e.pointerId);
    });

    wrap.addEventListener('pointermove', (e) => {
      if (!isDown) return;
      const dx = e.pageX - startX;
      if (Math.abs(dx) > TH) moved = true;
      wrap.scrollLeft = startScroll - dx;
    });

    function end(e){
      if (!isDown) return;
      isDown = false;
      wrap.classList.remove('dragging');
      try { wrap.releasePointerCapture?.(e.pointerId); } catch(_) {}
    }
    wrap.addEventListener('pointerup', end);
    wrap.addEventListener('pointercancel', end);
    wrap.addEventListener('pointerleave', end);

    // если двигали — отменяем клик (в том числе по ссылке); клик по .click не блокируем
    wrap.addEventListener('click', (e) => {
      if (isInteractiveTarget(e.target)) return;
      if (moved) {
        e.preventDefault();
        e.stopPropagation();
      }
    });

    // отключаем нативный drag
    wrap.addEventListener('dragstart', (e) => e.preventDefault());
  })();

  (function(){
    const wraps = Array.from(document.querySelectorAll('.parking-cards'));
    if (!wraps.length) return;

    const media = window.matchMedia('(max-width: 768px)');
    const TH = 5;
    const stateMap = new WeakMap();

    function getStep(wrap) {
      const cards = wrap.querySelectorAll('.parking-card');
      if (cards.length > 1) {
        const step = cards[1].offsetLeft - cards[0].offsetLeft;
        if (step > 0) return step;
      }
      return wrap.clientWidth;
    }

    function snapToNearest(wrap) {
      const step = getStep(wrap);
      if (!step) return;
      const index = Math.round(wrap.scrollLeft / step);
      wrap.scrollTo({ left: index * step, behavior: 'smooth' });
    }

    function setMobileLayout(wrap) {
      wrap.style.display = 'flex';
      wrap.style.flexWrap = 'nowrap';
      wrap.style.overflowX = 'auto';
      wrap.style.gap = '16px';
      wrap.style.scrollSnapType = 'x mandatory';
      wrap.style.webkitOverflowScrolling = 'touch';
      wrap.style.cursor = 'grab';
      wrap.style.paddingBottom = '6px';
      wrap.querySelectorAll('.parking-card').forEach((card) => {
        card.style.flex = '0 0 82%';
        card.style.scrollSnapAlign = 'start';
      });
    }

    function clearLayout(wrap) {
      wrap.removeAttribute('style');
      wrap.querySelectorAll('.parking-card').forEach((card) => card.removeAttribute('style'));
    }

    function isInteractiveTarget(el) {
      return !!(el && el.closest && el.closest('a, button, input, textarea, select'));
    }

    function onPointerDown(e) {
      const wrap = e.currentTarget;
      if (!media.matches) return;
      if (!e.target.closest('.parking-card')) return;
      if (isInteractiveTarget(e.target)) return;

      stateMap.set(wrap, {
        isDown: true,
        moved: false,
        startX: e.pageX,
        startScroll: wrap.scrollLeft,
      });

      wrap.style.cursor = 'grabbing';
      wrap.setPointerCapture?.(e.pointerId);
    }

    function onPointerMove(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown || !media.matches) return;

      const dx = e.pageX - state.startX;
      if (Math.abs(dx) > TH) state.moved = true;
      wrap.scrollLeft = state.startScroll - dx;
    }

    function onPointerEnd(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown) return;

      state.isDown = false;
      wrap.style.cursor = 'grab';
      try { wrap.releasePointerCapture?.(e.pointerId); } catch (_) {}
      snapToNearest(wrap);
    }

    function onClick(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !media.matches) return;
      if (isInteractiveTarget(e.target)) return;
      if (state.moved) {
        e.preventDefault();
        e.stopPropagation();
      }
    }

    function bindWrap(wrap) {
      if (wrap.dataset.parkingSwipeBound === '1') return;
      wrap.dataset.parkingSwipeBound = '1';
      wrap.addEventListener('pointerdown', onPointerDown);
      wrap.addEventListener('pointermove', onPointerMove);
      wrap.addEventListener('pointerup', onPointerEnd);
      wrap.addEventListener('pointercancel', onPointerEnd);
      wrap.addEventListener('pointerleave', onPointerEnd);
      wrap.addEventListener('click', onClick);
      wrap.addEventListener('dragstart', (ev) => ev.preventDefault());
    }

    function applyMode() {
      wraps.forEach((wrap) => {
        bindWrap(wrap);
        if (media.matches) {
          setMobileLayout(wrap);
        } else {
          clearLayout(wrap);
        }
      });
    }

    applyMode();
    media.addEventListener('change', applyMode);
    window.addEventListener('resize', () => {
      if (media.matches) {
        wraps.forEach((wrap) => snapToNearest(wrap));
      }
    });
  })();

  (function(){
    const wraps = Array.from(document.querySelectorAll('.google-otel-cards'));
    if (!wraps.length) return;

    const media = window.matchMedia('(max-width: 768px)');
    const TH = 5;
    const stateMap = new WeakMap();

    function getStep(wrap) {
      const cards = wrap.querySelectorAll('.google-otel-card');
      if (cards.length > 1) {
        const step = cards[1].offsetLeft - cards[0].offsetLeft;
        if (step > 0) return step;
      }
      return wrap.clientWidth;
    }

    function snapToNearest(wrap) {
      const step = getStep(wrap);
      if (!step) return;
      const index = Math.round(wrap.scrollLeft / step);
      wrap.scrollTo({ left: index * step, behavior: 'smooth' });
    }

    function setMobileLayout(wrap) {
      wrap.style.display = 'flex';
      wrap.style.flexWrap = 'nowrap';
      wrap.style.overflowX = 'auto';
      wrap.style.gap = '16px';
      wrap.style.justifyContent = 'flex-start';
      wrap.style.scrollSnapType = 'x mandatory';
      wrap.style.webkitOverflowScrolling = 'touch';
      wrap.style.cursor = 'grab';
      wrap.style.paddingBottom = '6px';
      wrap.querySelectorAll('.google-otel-card').forEach((card) => {
        card.style.flex = '0 0 100%';
        card.style.width = '100%';
        card.style.scrollSnapAlign = 'start';
      });
    }

    function clearLayout(wrap) {
      wrap.removeAttribute('style');
      wrap.querySelectorAll('.google-otel-card').forEach((card) => card.removeAttribute('style'));
    }

    function isInteractiveTarget(el) {
      return !!(el && el.closest && el.closest('a, button, input, textarea, select'));
    }

    function onPointerDown(e) {
      const wrap = e.currentTarget;
      if (!media.matches) return;
      if (!e.target.closest('.google-otel-card')) return;
      if (isInteractiveTarget(e.target)) return;

      stateMap.set(wrap, {
        isDown: true,
        moved: false,
        startX: e.pageX,
        startScroll: wrap.scrollLeft,
      });

      wrap.style.cursor = 'grabbing';
      wrap.setPointerCapture?.(e.pointerId);
    }

    function onPointerMove(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown || !media.matches) return;

      const dx = e.pageX - state.startX;
      if (Math.abs(dx) > TH) state.moved = true;
      wrap.scrollLeft = state.startScroll - dx;
    }

    function onPointerEnd(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown) return;

      state.isDown = false;
      wrap.style.cursor = 'grab';
      try { wrap.releasePointerCapture?.(e.pointerId); } catch (_) {}
      snapToNearest(wrap);
    }

    function onClick(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !media.matches) return;
      if (isInteractiveTarget(e.target)) return;
      if (state.moved) {
        e.preventDefault();
        e.stopPropagation();
      }
    }

    function bindWrap(wrap) {
      if (wrap.dataset.googleHotelSwipeBound === '1') return;
      wrap.dataset.googleHotelSwipeBound = '1';
      wrap.addEventListener('pointerdown', onPointerDown);
      wrap.addEventListener('pointermove', onPointerMove);
      wrap.addEventListener('pointerup', onPointerEnd);
      wrap.addEventListener('pointercancel', onPointerEnd);
      wrap.addEventListener('pointerleave', onPointerEnd);
      wrap.addEventListener('click', onClick);
      wrap.addEventListener('dragstart', (ev) => ev.preventDefault());
    }

    function applyMode() {
      wraps.forEach((wrap) => {
        bindWrap(wrap);
        if (media.matches) {
          setMobileLayout(wrap);
        } else {
          clearLayout(wrap);
        }
      });
    }

    applyMode();
    media.addEventListener('change', applyMode);
    window.addEventListener('resize', () => {
      if (media.matches) {
        wraps.forEach((wrap) => snapToNearest(wrap));
      }
    });
  })();

  (function(){
    const wraps = Array.from(document.querySelectorAll('.oleksandr-wrap'));
    if (!wraps.length) return;

    const media = window.matchMedia('(max-width: 752px)');
    const TH = 5;
    const stateMap = new WeakMap();

    function isRtlPage() {
      const dir = document.documentElement.getAttribute('dir') || document.body.getAttribute('dir') || '';
      return dir.toLowerCase() === 'rtl';
    }

    function getCards(wrap) {
      return wrap.querySelectorAll('.oleksandr-card');
    }

    function getScrollIndex(wrap) {
      const cards = getCards(wrap);
      if (!cards.length) return 0;

      const wrapRect = wrap.getBoundingClientRect();
      let best = 0;
      let bestDist = Infinity;

      cards.forEach((card, i) => {
        const cardRect = card.getBoundingClientRect();
        const dist = Math.abs(cardRect.left - wrapRect.left);
        if (dist < bestDist) {
          bestDist = dist;
          best = i;
        }
      });

      return best;
    }

    function getCardScrollLeft(wrap, card) {
      return wrap.scrollLeft + (card.getBoundingClientRect().left - wrap.getBoundingClientRect().left);
    }

    function scrollToIndex(wrap, index) {
      const cards = getCards(wrap);
      if (!cards.length) return;

      const maxIndex = cards.length - 1;
      const safeIndex = Math.max(0, Math.min(maxIndex, index));
      const card = cards[safeIndex];
      if (!card) return;

      // Только горизонтальный скролл контейнера (scrollIntoView поднимает всю страницу вверх)
      wrap.scrollTo({
        left: getCardScrollLeft(wrap, card),
        behavior: 'smooth',
      });
    }

    function snapToNearest(wrap) {
      scrollToIndex(wrap, getScrollIndex(wrap));
    }

    function scrollByStep(wrap, dir) {
      const maxIndex = Math.max(0, getCards(wrap).length - 1);
      const index = getScrollIndex(wrap);
      scrollToIndex(wrap, Math.max(0, Math.min(maxIndex, index + dir)));
    }

    function getControls(wrap) {
      const root = wrap.closest('.reviews-oleksandr-into');
      if (!root) return {};
      return {
        prev: root.querySelector('.oleksandr-nav-btn--prev'),
        next: root.querySelector('.oleksandr-nav-btn--next'),
      };
    }

    function moveDopText(wrap) {
      const root = wrap.closest('.reviews-oleksandr-into');
      if (!root) return;

      const source = root.querySelector('.oleksandr-dop-text');
      const target = root.querySelector('.oleksandr-dop-text2');
      if (!source || !target) return;

      if (media.matches) {
        if (source.innerHTML.trim() !== '') {
          target.innerHTML = source.innerHTML;
          source.innerHTML = '';
        }
      } else if (target.innerHTML.trim() !== '') {
        source.innerHTML = target.innerHTML;
        target.innerHTML = '';
      }
    }

    function updateArrowState(wrap) {
      const controls = getControls(wrap);
      if (!controls.prev || !controls.next) return;
      if (!media.matches) {
        controls.prev.disabled = false;
        controls.next.disabled = false;
        return;
      }

      const maxIndex = Math.max(0, getCards(wrap).length - 1);
      const index = getScrollIndex(wrap);
      const rtl = isRtlPage();

      // В RTL левая кнопка (--prev) листает вперёд, правая (--next) — назад
      if (rtl) {
        controls.prev.disabled = index >= maxIndex;
        controls.next.disabled = index <= 0;
      } else {
        controls.prev.disabled = index <= 0;
        controls.next.disabled = index >= maxIndex;
      }
    }

    function setMobileLayout(wrap) {
      wrap.style.display = 'flex';
      wrap.style.flexWrap = 'nowrap';
      wrap.style.overflowX = 'auto';
      wrap.style.gap = '16px';
      wrap.style.justifyContent = 'flex-start';
      wrap.style.scrollSnapType = 'x mandatory';
      wrap.style.webkitOverflowScrolling = 'touch';
      wrap.style.cursor = 'grab';
      wrap.style.paddingBottom = '6px';
      wrap.querySelectorAll('.oleksandr-card').forEach((card) => {
        card.style.flex = '0 0 100%';
        card.style.width = '100%';
        card.style.scrollSnapAlign = 'start';
      });
    }

    function clearLayout(wrap) {
      wrap.removeAttribute('style');
      wrap.querySelectorAll('.oleksandr-card').forEach((card) => card.removeAttribute('style'));
    }

    function isInteractiveTarget(el) {
      return !!(el && el.closest && el.closest('a, button, input, textarea, select'));
    }

    function onPointerDown(e) {
      const wrap = e.currentTarget;
      if (!media.matches) return;
      if (!e.target.closest('.oleksandr-card')) return;
      if (isInteractiveTarget(e.target)) return;

      stateMap.set(wrap, {
        isDown: true,
        moved: false,
        startX: e.pageX,
        startScroll: wrap.scrollLeft,
      });

      wrap.style.cursor = 'grabbing';
      wrap.setPointerCapture?.(e.pointerId);
    }

    function onPointerMove(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown || !media.matches) return;

      const dx = e.pageX - state.startX;
      if (Math.abs(dx) > TH) state.moved = true;
      wrap.scrollLeft = state.startScroll - dx;
    }

    function onPointerEnd(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !state.isDown) return;

      state.isDown = false;
      wrap.style.cursor = 'grab';
      try { wrap.releasePointerCapture?.(e.pointerId); } catch (_) {}
      snapToNearest(wrap);
    }

    function onClick(e) {
      const wrap = e.currentTarget;
      const state = stateMap.get(wrap);
      if (!state || !media.matches) return;
      if (isInteractiveTarget(e.target)) return;
      if (state.moved) {
        e.preventDefault();
        e.stopPropagation();
      }
    }

    function bindWrap(wrap) {
      if (wrap.dataset.oleksandrSwipeBound === '1') return;
      wrap.dataset.oleksandrSwipeBound = '1';
      wrap.addEventListener('pointerdown', onPointerDown);
      wrap.addEventListener('pointermove', onPointerMove);
      wrap.addEventListener('pointerup', onPointerEnd);
      wrap.addEventListener('pointercancel', onPointerEnd);
      wrap.addEventListener('pointerleave', onPointerEnd);
      wrap.addEventListener('click', onClick);
      wrap.addEventListener('dragstart', (ev) => ev.preventDefault());
      wrap.addEventListener('scroll', () => updateArrowState(wrap), { passive: true });

      const controls = getControls(wrap);
      if (controls.prev && controls.next && wrap.dataset.oleksandrNavBound !== '1') {
        wrap.dataset.oleksandrNavBound = '1';
        controls.prev.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (!media.matches) return;
          scrollByStep(wrap, isRtlPage() ? 1 : -1);
        });
        controls.next.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (!media.matches) return;
          scrollByStep(wrap, isRtlPage() ? -1 : 1);
        });
      }
    }

    function applyMode() {
      wraps.forEach((wrap) => {
        bindWrap(wrap);
        moveDopText(wrap);
        if (media.matches) {
          setMobileLayout(wrap);
        } else {
          clearLayout(wrap);
        }
        updateArrowState(wrap);
      });
    }

    applyMode();
    media.addEventListener('change', applyMode);
    window.addEventListener('resize', () => {
      if (media.matches) {
        wraps.forEach((wrap) => snapToNearest(wrap));
      }
    });
  })();

  (function(){
    const wrap = document.querySelector('.cats-wrap');
    if(!wrap) return;

    let isDown = false, moved = false, startX = 0, startScroll = 0;
    const TH = 5; // порог пикселей для драг-срабатывания
    function isInteractiveTarget(el) {
      return !!(el && el.closest && el.closest('.click'));
    }

    wrap.addEventListener('pointerdown', (e) => {
      if (!e.target.closest('.cat-item')) return; // кликнули вне списка
      if (isInteractiveTarget(e.target)) return;
      isDown = true;
      moved  = false;
      startX = e.pageX;
      startScroll = wrap.scrollLeft;
      wrap.classList.add('dragging');
      wrap.setPointerCapture?.(e.pointerId);
    });

    wrap.addEventListener('pointermove', (e) => {
      if (!isDown) return;
      const dx = e.pageX - startX;
      if (Math.abs(dx) > TH) moved = true;
      wrap.scrollLeft = startScroll - dx;
    });

    function end(e){
      if (!isDown) return;
      isDown = false;
      wrap.classList.remove('dragging');
      try { wrap.releasePointerCapture?.(e.pointerId); } catch(_) {}
    }
    wrap.addEventListener('pointerup', end);
    wrap.addEventListener('pointercancel', end);
    wrap.addEventListener('pointerleave', end);

    // если двигали — отменяем клик; клик по .click не блокируем
    wrap.addEventListener('click', (e) => {
      if (isInteractiveTarget(e.target)) return;
      if (moved) {
        e.preventDefault();
        e.stopPropagation();
      }
    });

    // отключаем нативный drag
    wrap.addEventListener('dragstart', (e) => e.preventDefault());
  })();

// Клик по .click → modal_vt: src iframe из data-video-src, модалка short или default
(function () {
	function getHelloVtModal(block, modalType) {
		if (modalType === 'short') {
			return block.querySelector('.video-reviews-hello-modal_vt.chort');
		}
		return block.querySelector('.video-reviews-hello-modal_vt:not(.chort)');
	}

	function getOpenHelloVtModal(block) {
		var modals = block.querySelectorAll('.video-reviews-hello-modal_vt');
		for (var i = 0; i < modals.length; i++) {
			if (modals[i].style.display === 'block') {
				return modals[i];
			}
		}
		return null;
	}

	function openHelloVt(block, videoSrc, modalType) {
		var overlay = block.querySelector('.video-reviews-hello-overlay_vt');
		var modal = getHelloVtModal(block, modalType);
		if (!overlay || !modal) {
			return;
		}

		// Закрываем другую модалку в этом блоке, если была открыта.
		var openModal = getOpenHelloVtModal(block);
		if (openModal && openModal !== modal) {
			closeHelloVt(block, openModal);
		}

		var iframe = modal.querySelector('.video-reviews-modal-iframe_vt');
		if (iframe) {
			var u = (videoSrc || '').trim();
			if (u) {
				iframe.src = u;
			} else {
				iframe.removeAttribute('src');
			}
		}

		modal.style.display = 'block';
		overlay.style.display = 'block';
		document.body.classList.add('modal-open');
		setTimeout(function () {
			modal.style.opacity = '1';
			overlay.style.opacity = '1';
		}, 10);
	}

	function closeHelloVt(block, modalToClose) {
		var overlay = block.querySelector('.video-reviews-hello-overlay_vt');
		var modal = modalToClose || getOpenHelloVtModal(block);
		if (!overlay || !modal || modal.style.display !== 'block') {
			return;
		}
		var iframe = modal.querySelector('.video-reviews-modal-iframe_vt');
		if (iframe) {
			iframe.removeAttribute('src');
		}
		modal.style.opacity = '0';
		overlay.style.opacity = '0';
		setTimeout(function () {
			modal.style.display = 'none';
			var anyOpen = false;
			block.querySelectorAll('.video-reviews-hello-modal_vt').forEach(function (m) {
				if (m.style.display === 'block') {
					anyOpen = true;
				}
			});
			if (!anyOpen) {
				overlay.style.display = 'none';
				document.body.classList.remove('modal-open');
			}
		}, 300);
	}

	function isHelloVtOpen(modal) {
		return modal && modal.style.display === 'block';
	}

	document.addEventListener('click', function (e) {
		var t = e.target && e.target.closest && e.target.closest('.click');
		if (!t && e.target.closest) {
			t = e.target.closest('.image-rew');
		}
		if (!t) {
			return;
		}
		if (t.classList && t.classList.contains('image-rew--no-video')) {
			return;
		}
		var block = t.closest('.video-reviews-block');
		if (!block) {
			return;
		}
		if (!block.querySelector('.video-reviews-hello-modal_vt')) {
			return;
		}
		var src = (t.getAttribute('data-video-src') || '').trim();
		var modalType = (t.getAttribute('data-video-modal') || 'default').trim();
		if (modalType !== 'short') {
			modalType = 'default';
		}
		e.preventDefault();
		openHelloVt(block, src, modalType);
	});

	// capture: раньше app.js Modal, иначе крестик закроет не ту модалку (первый .modal_vt на странице)
	document.addEventListener(
		'click',
		function (e) {
			if (e.target.closest('.video-reviews-hello-modal_vt .modal-close_vt')) {
				var block = e.target.closest('.video-reviews-block');
				var modal = e.target.closest('.video-reviews-hello-modal_vt');
				if (block && modal) {
					e.preventDefault();
					e.stopImmediatePropagation();
					closeHelloVt(block, modal);
				}
				return;
			}
			if (e.target.classList && e.target.classList.contains('video-reviews-hello-overlay_vt')) {
				var blockOv = e.target.closest('.video-reviews-block');
				if (blockOv) {
					e.stopImmediatePropagation();
					closeHelloVt(blockOv);
				}
			}
		},
		true
	);

	document.addEventListener('keydown', function (e) {
		if (e.key !== 'Escape') {
			return;
		}
		document.querySelectorAll('.video-reviews-hello-modal_vt').forEach(function (modal) {
			if (isHelloVtOpen(modal)) {
				var block = modal.closest('.video-reviews-block');
				if (block) {
					closeHelloVt(block, modal);
				}
			}
		});
	});
})();

jQuery(document).ready(function($) {
   

$('.mobile-menu').click(function() {
$('.smenu-container').slideToggle(500);
$(this).toggleClass('active');
$('.nav-wrap').css('border-radius','0');
$('.wrap-btn-menu-wotasap').toggleClass('active');

});



    // 1. СНАЧАЛА ОБЪЯВЛЯЕМ ФУНКЦИИ
    function left_carusel(carusel) {
        var block_width = $(carusel).find('.carousel-block').outerWidth();
        $(carusel).find(".carousel-items .carousel-block").eq(-1).clone().prependTo($(carusel).find(".carousel-items")); 
        $(carusel).find(".carousel-items").css({"left":"-"+block_width+"px"});
        $(carusel).find(".carousel-items .carousel-block").eq(-1).remove();    
        $(carusel).find(".carousel-items").animate({left: "0px"}, 200); 
    }

    function right_carusel(carusel) {
        var block_width = $(carusel).find('.carousel-block').outerWidth();
        $(carusel).find(".carousel-items").animate({left: "-"+ block_width +"px"}, 200, function(){
            $(carusel).find(".carousel-items .carousel-block").eq(0).clone().appendTo($(carusel).find(".carousel-items")); 
            $(carusel).find(".carousel-items .carousel-block").eq(0).remove(); 
            $(carusel).find(".carousel-items").css({"left":"0px"}); 
        }); 
    }
/*
    function auto_right(carusel_selector) {
        setInterval(function() {
            var carusel = $(carusel_selector);
            if (!carusel.hasClass('hover')) {
                right_carusel(carusel);
            }
        }, 3000);
    }
*/
    // 2. ЗАТЕМ ВЕШАЕМ СОБЫТИЯ И ЗАПУСКАЕМ
    // Простой и надёжный обработчик кликов по стрелкам
    $(document).on('click', ".carousel-button-right, .carousel-button-right a", function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Запоминаем текущую позицию прокрутки
        var scrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

        // Кнопки находятся вне блока .carousel, поэтому ищем карусель через общий контейнер
        var $carousel = $(this).closest('.region, .container-5, .into-region').find('.carousel').first();

        right_carusel($carousel);

        // Мгновенно возвращаем пользователя в ту же точку
        setTimeout(function() {
            window.scrollTo(0, scrollY);
        }, 0);

        return false;
    });

    $(document).on('click', ".carousel-button-left, .carousel-button-left a", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var scrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

        // Кнопки находятся вне блока .carousel, поэтому ищем карусель через общий контейнер
        var $carousel = $(this).closest('.region, .container-5, .into-region').find('.carousel').first();
        left_carusel($carousel);

        setTimeout(function() {
            window.scrollTo(0, scrollY);
        }, 0);

        return false;
    });

    // Prevent focus on anchor tags (prevents page scroll on first click)
    $('.carousel-button-left a, .carousel-button-right a').attr('tabindex', '-1');
    
    // 3. SWIPE (СВАЙП) ДЛЯ МОБИЛЬНЫХ УСТРОЙСТВ
    var touchStartX = 0;
    var touchStartY = 0;
    var touchEndX = 0;
    var touchEndY = 0;
    var minSwipeDistance = 50; // Минимальное расстояние для свайпа
    
    $(document).on('touchstart', '.carousel-items, .carousel-wrapper', function(e) {
        var touch = e.originalEvent.touches[0];
        touchStartX = touch.clientX;
        touchStartY = touch.clientY;
    });
    
    $(document).on('touchmove', '.carousel-items, .carousel-wrapper', function(e) {
        // Предотвращаем прокрутку страницы при горизонтальном свайпе
        var touch = e.originalEvent.touches[0];
        var deltaX = Math.abs(touch.clientX - touchStartX);
        var deltaY = Math.abs(touch.clientY - touchStartY);
        
        // Если горизонтальное движение больше вертикального, предотвращаем прокрутку
        if (deltaX > deltaY && deltaX > 10) {
            e.preventDefault();
        }
    });
    
    $(document).on('touchend', '.carousel-items, .carousel-wrapper', function(e) {
        var touch = e.originalEvent.changedTouches[0];
        touchEndX = touch.clientX;
        touchEndY = touch.clientY;
        
        var deltaX = touchEndX - touchStartX;
        var deltaY = touchEndY - touchStartY;
        var absDeltaX = Math.abs(deltaX);
        var absDeltaY = Math.abs(deltaY);
        
        // Проверяем, что это горизонтальный свайп (не вертикальный)
        if (absDeltaX > absDeltaY && absDeltaX > minSwipeDistance) {
            e.preventDefault();
            e.stopPropagation();
            
            var $carousel = $(this).closest('.carousel');
            
            // Свайп влево = следующий слайд (right_carusel)
            if (deltaX < 0) {
                right_carusel($carousel);
            }
            // Свайп вправо = предыдущий слайд (left_carusel)
            else if (deltaX > 0) {
                left_carusel($carousel);
            }
        }
    });
    
    // Остановка при наведении
    $(document).on('mouseenter', '.carousel', function(){ $(this).addClass('hover'); });
    $(document).on('mouseleave', '.carousel', function(){ $(this).removeClass('hover'); });

    // Запуск автопрокрутки (закомментировано)
    // auto_right('.carousel:first');




});

// faq
       const wrapper = document.querySelector('#faq-wrapper');

        wrapper.addEventListener('click', function(event) {
            const currentItem = event.target.closest('.faq-item');
            if (!currentItem) return;

            // Если нужно закрывать остальные (Аккордеон), раскументируй этот блок:
            /*
            const allItems = document.querySelectorAll('.faq-item');
            allItems.forEach(item => {
                if (item !== currentItem) {
                    item.classList.remove('active');
                    item.querySelector('.status-box').innerText = '+';
                }
            });
            */

            const isActive = currentItem.classList.toggle('active');
      
        });
		



// галерея 3 d

// Обработка клика на стрелку вправо
document.addEventListener('click', function(e) {
	if (e.target.closest('.carousel-button-right_d3')) {
		const carousel = e.target.closest('.carousel_d3');
		rightCarousel(carousel);
		e.preventDefault();
	}
});

// Обработка клика на стрелку влево
document.addEventListener('click', function(e) {
	if (e.target.closest('.carousel-button-left_d3')) {
		const carousel = e.target.closest('.carousel_d3');
		leftCarousel(carousel);
		e.preventDefault();
	}
});

// Получить текущий слайд для карусели
function getCurrentSlide(carousel) {
	return parseInt(carousel.getAttribute('data-current-slide') || '0');
}

// Установить текущий слайд для карусели
function setCurrentSlide(carousel, slide) {
	carousel.setAttribute('data-current-slide', slide.toString());
}

// Обновление позиций карточек в 3D пространстве
function update3DPositions(carousel) {
	const carouselItems = carousel.querySelector('.carousel-items_d3');
	const blocks = Array.from(carouselItems.querySelectorAll('.carousel-block_d3'));
	const totalBlocks = blocks.length;
	const currentSlide = getCurrentSlide(carousel);
	
	blocks.forEach(function(block, index) {
		// Удаляем все классы позиционирования
		block.classList.remove('prev', 'active', 'next', 'hidden');
		
		// Вычисляем относительную позицию от текущего слайда
		let relativePos = index - currentSlide;
		
		// Обрабатываем циклический переход
		if (relativePos > totalBlocks / 2) {
			relativePos = relativePos - totalBlocks;
		} else if (relativePos < -totalBlocks / 2) {
			relativePos = relativePos + totalBlocks;
		}
		
		// Присваиваем классы в зависимости от позиции
		if (relativePos === -1) {
			block.classList.add('prev');
		} else if (relativePos === 0) {
			block.classList.add('active');
		} else if (relativePos === 1) {
			block.classList.add('next');
		} else {
			block.classList.add('hidden');
		}
	});
}

function leftCarousel(carousel, skipUpdate) {
	const totalBlocks = carousel.querySelectorAll('.carousel-block_d3').length;
	const currentSlide = getCurrentSlide(carousel);
	const newSlide = (currentSlide - 1 + totalBlocks) % totalBlocks;
	
	setCurrentSlide(carousel, newSlide);
	update3DPositions(carousel);
	
	// Обновление активной точки
	if (!skipUpdate) {
		updateDots(carousel);
	}
}

function rightCarousel(carousel, skipUpdate) {
	const totalBlocks = carousel.querySelectorAll('.carousel-block_d3').length;
	const currentSlide = getCurrentSlide(carousel);
	const newSlide = (currentSlide + 1) % totalBlocks;
	
	setCurrentSlide(carousel, newSlide);
	update3DPositions(carousel);
	
	// Обновление активной точки
	if (!skipUpdate) {
		updateDots(carousel);
	}
}

// Функция для перехода к конкретному слайду
function goToSlide(carousel, slideIndex) {
	const currentIndex = getCurrentSlide(carousel);
	const targetIndex = slideIndex;
	let diff = targetIndex - currentIndex;
	const totalBlocks = carousel.querySelectorAll('.carousel-block_d3').length;
	
	if (diff === 0) return;
	
	// Вычисляем кратчайший путь (учитывая цикличность)
	if (Math.abs(diff) > totalBlocks / 2) {
		diff = diff > 0 ? diff - totalBlocks : diff + totalBlocks;
	}
	
	// Обновляем индекс
	setCurrentSlide(carousel, targetIndex);
	update3DPositions(carousel);
	updateDots(carousel);
}

// Функция обновления активной точки
function updateDots(carousel) {
	const dots = carousel.querySelectorAll('.carousel-dot_d3');
	const currentSlide = getCurrentSlide(carousel);
	dots.forEach(function(dot) {
		dot.classList.remove('active_d3');
	});
	if (dots[currentSlide]) {
		dots[currentSlide].classList.add('active_d3');
	}
}

// Инициализация точек
function initDots(carousel) {
	const totalBlocks = carousel.querySelectorAll('.carousel-block_d3').length;
	const dotsContainer = carousel.querySelector('.carousel-dots_d3');
	dotsContainer.innerHTML = '';
	
	// Инициализируем текущий слайд, если еще не установлен
	if (!carousel.hasAttribute('data-current-slide')) {
		setCurrentSlide(carousel, 0);
	}
	
	for (let i = 0; i < totalBlocks; i++) {
		const dot = document.createElement('span');
		dot.className = 'carousel-dot_d3';
		dot.setAttribute('data-slide', i);
		dot.addEventListener('click', function() {
			const slideIndex = parseInt(this.getAttribute('data-slide'));
			goToSlide(carousel, slideIndex);
		});
		dotsContainer.appendChild(dot);
	}
	
	// Устанавливаем первую точку как активную
	updateDots(carousel);
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
	// Инициализация точек и 3D позиций для всех каруселей
	const carousels = document.querySelectorAll('.carousel_d3');
	carousels.forEach(function(carousel) {
		initDots(carousel);
		update3DPositions(carousel);
		initTouchEvents(carousel);
	});
});

// Инициализация touch событий для свайпа
function initTouchEvents(carousel) {
	let touchStartX = 0;
	let touchStartY = 0;
	let touchEndX = 0;
	let touchEndY = 0;
	let isDragging = false;
	
	const carouselWrapper = carousel.querySelector('.carousel-wrapper_d3');
	
	carouselWrapper.addEventListener('touchstart', function(e) {
		touchStartX = e.changedTouches[0].screenX;
		touchStartY = e.changedTouches[0].screenY;
		isDragging = true;
	}, { passive: true });
	
	carouselWrapper.addEventListener('touchmove', function(e) {
		if (isDragging) {
			e.preventDefault();
		}
	}, { passive: false });
	
	carouselWrapper.addEventListener('touchend', function(e) {
		if (!isDragging) return;
		
		touchEndX = e.changedTouches[0].screenX;
		touchEndY = e.changedTouches[0].screenY;
		
		const deltaX = touchEndX - touchStartX;
		const deltaY = touchEndY - touchStartY;
		
		// Проверяем, что это горизонтальный свайп (больше горизонтального движения, чем вертикального)
		if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
			if (deltaX > 0) {
				// Свайп вправо - переходим к предыдущему слайду
				leftCarousel(carousel);
			} else {
				// Свайп влево - переходим к следующему слайду
				rightCarousel(carousel);
			}
		}
		
		isDragging = false;
	}, { passive: true });
}





// кнопка наверх 


// browser window scroll (in pixels) after which the "back to top" link is shown
var offset = 300,
	//browser window scroll (in pixels) after which the "back to top" link opacity is reduced
	offset_opacity = 1200,
	//duration of the top scrolling animation (in ms)
	scroll_top_duration = 700,
	//grab the "back to top" link
	back_to_top = document.querySelector('.cd-top');

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
	//hide or show the "back to top" link
	window.addEventListener('scroll', function(){
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		
		if (scrollTop > offset) {
			back_to_top.classList.add('cd-is-visible');
			back_to_top.classList.remove('cd-fade-out');
		} else {
			back_to_top.classList.remove('cd-is-visible', 'cd-fade-out');
		}
		
		if (scrollTop > offset_opacity) { 
			back_to_top.classList.add('cd-fade-out');
		}
	});

	//smooth scroll to top with custom animation
	back_to_top.addEventListener('click', function(event){
		event.preventDefault();
		
		var startPosition = window.pageYOffset || document.documentElement.scrollTop;
		var startTime = null;
		
		// Easing function for smooth animation (easeInOutCubic)
		function easeInOutCubic(t) {
			return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
		}
		
		function animateScroll(currentTime) {
			if (startTime === null) startTime = currentTime;
			var timeElapsed = currentTime - startTime;
			var progress = Math.min(timeElapsed / scroll_top_duration, 1);
			
			// Apply easing
			var ease = easeInOutCubic(progress);
			
			// Calculate current position
			var currentPosition = startPosition * (1 - ease);
			
			window.scrollTo(0, currentPosition);
			
			if (progress < 1) {
				requestAnimationFrame(animateScroll);
			}
		}
		
		requestAnimationFrame(animateScroll);
	});
});

/**
 * Открытие/закрытие формы: .new_order-mr (основной блок) или .flexibol-toggle (flexible).
 * Панель flexible: .s-flexibol-rewopen или section.flexibol-open внутри .flexibol-block.
 */














