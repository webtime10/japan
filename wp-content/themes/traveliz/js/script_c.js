/**
 * Language Switcher Dropdown
 * Opens/closes language list when clicking on current language
 */

(function() {
    'use strict';

    /* Скрипт нельзя инициализировать дважды: иначе два обработчика на клик открывают и тут же закрывают список. */
    if (typeof window !== 'undefined' && window.__travelizLangSwitcherInit) {
        return;
    }
    if (typeof window !== 'undefined') {
        window.__travelizLangSwitcherInit = true;
    }

    function closeSwitcher(switcher) {
        var dropdown = switcher.querySelector('.lang-list');
        var trigger = switcher.querySelector('.current-language-display');
        var arrow = trigger ? trigger.querySelector('.lang-arrow') : null;
        if (dropdown) {
            dropdown.classList.remove('show');
        }
        if (arrow) {
            arrow.style.transform = 'rotate(0deg)';
        }
    }

    function openSwitcher(switcher, allSwitchers) {
        var dropdown = switcher.querySelector('.lang-list');
        var trigger = switcher.querySelector('.current-language-display');
        var arrow = trigger ? trigger.querySelector('.lang-arrow') : null;
        Array.prototype.forEach.call(allSwitchers, function(other) {
            if (other !== switcher) {
                closeSwitcher(other);
            }
        });
        if (dropdown) {
            dropdown.classList.add('show');
        }
        if (arrow) {
            arrow.style.transform = 'rotate(180deg)';
        }
    }

    function initLanguageSwitcher() {
        var switchers = document.querySelectorAll('.custom-lang-switcher');
        if (!switchers.length) {
            return;
        }

        Array.prototype.forEach.call(switchers, function(switcher) {
            var trigger = switcher.querySelector('.current-language-display');
            var dropdown = switcher.querySelector('.lang-list');
            var arrow = trigger ? trigger.querySelector('.lang-arrow') : null;

            if (!trigger || !dropdown) {
                return;
            }

            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var isOpen = dropdown.classList.contains('show');

                if (isOpen) {
                    closeSwitcher(switcher);
                } else {
                    openSwitcher(switcher, switchers);
                }
            });

            /* Десктоп: блок в шапке с классом .desc-il — открытие по hover, мобильный блок без него — только клик */
            if (switcher.classList.contains('desc-il')) {
                var hoverCloseTimer = null;
                switcher.addEventListener('mouseenter', function() {
                    if (hoverCloseTimer) {
                        clearTimeout(hoverCloseTimer);
                        hoverCloseTimer = null;
                    }
                    openSwitcher(switcher, switchers);
                });
                switcher.addEventListener('mouseleave', function() {
                    hoverCloseTimer = setTimeout(function() {
                        closeSwitcher(switcher);
                        hoverCloseTimer = null;
                    }, 180);
                });
            }

            var langItems = dropdown.querySelectorAll('.lang-item a');
            Array.prototype.forEach.call(langItems, function(item) {
                item.addEventListener('click', function() {
                    closeSwitcher(switcher);
                });
            });
        });

        document.addEventListener('click', function(e) {
            Array.prototype.forEach.call(switchers, function(switcher) {
                var trigger = switcher.querySelector('.current-language-display');
                var dropdown = switcher.querySelector('.lang-list');
                if (!trigger || !dropdown) {
                    return;
                }
                if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                    closeSwitcher(switcher);
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLanguageSwitcher);
    } else {
        initLanguageSwitcher();
    }

})();

// Мобильное меню
(function() {
    'use strict';
    
    function initMobileMenu() {
        var hamburgerBtn = document.querySelector('.hamburger-btn');
        var closeBtn = document.getElementById('mobile-menu-close');
        var nav = document.getElementById('mobile-nav');
        
        if (!hamburgerBtn || !nav) {
            return;
        }
        
        // Открытие меню по клику на бургер
        hamburgerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            nav.classList.add('menu-open');
            document.body.style.overflow = 'hidden'; // Блокируем скролл
        });
        
        // Закрытие меню по клику на крестик
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                nav.classList.remove('menu-open');
                document.body.style.overflow = ''; // Разблокируем скролл
            });
        }
        
        // Закрытие меню при клике вне меню
        nav.addEventListener('click', function(e) {
            if (e.target === nav) {
                nav.classList.remove('menu-open');
                document.body.style.overflow = '';
            }
        });
        
        // Закрытие меню при клике на ссылку меню
        var menuLinks = nav.querySelectorAll('#main-menu a');
        Array.prototype.forEach.call(menuLinks, function(link) {
            link.addEventListener('click', function() {
                nav.classList.remove('menu-open');
                document.body.style.overflow = '';
            });
        });
    }
    
    // Инициализация при загрузке DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }
})();


// Карусель carousel_m
jQuery(document).ready(function($) {
	var currentSlide_m = 0;

	function setCarouselTrackWidth(carusel_m) {
		var $carousel = $(carusel_m);
		var $wrapper = $carousel.find('.carousel-wrapper_m').first();
		var $items = $carousel.find('.carousel-items_m').first();
		var $blocks = $items.children('.carousel-block_m');

		if (!$blocks.length) {
			return;
		}

		// Только swiss-experience: один совет в кадре.
		// Ширина = точная ширина окна (до 487px), чтобы не было щели справа.
		if ($carousel.closest('.swiss-experience').length && $wrapper.length) {
			var slideWidth = Math.floor($wrapper[0].clientWidth || $wrapper.width() || 0);
			if (slideWidth <= 0) {
				slideWidth = 487;
			}
			if (slideWidth > 487) {
				slideWidth = 487;
			}
			$wrapper.css('direction', 'ltr');
			$items.css({
				direction: 'ltr',
				left: '0px',
				right: 'auto'
			});
			$blocks.css({
				width: slideWidth + 'px',
				maxWidth: 'none',
				marginLeft: 0,
				marginRight: 0,
				float: 'left',
				clear: 'none',
				direction: 'rtl',
				boxSizing: 'border-box'
			});
		}

		var totalWidth = 0;
		$blocks.each(function() {
			totalWidth += $(this).outerWidth(true);
		});

		if (totalWidth > 0) {
			$items.css('width', totalWidth + 'px');
		}
	}

	function initAllCarouselTracks() {
		$('.carousel_m').each(function() {
			setCarouselTrackWidth(this);
		});
	}

	function left_carusel_m(carusel_m, skipUpdate_m){
	   var block_width_m = $(carusel_m).find('.carousel-block_m').outerWidth();
	   $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(-1).clone().prependTo($(carusel_m).find(".carousel-items_m")); 
	   $(carusel_m).find(".carousel-items_m").css({"left":"-"+block_width_m+"px"});
	   $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(-1).remove();    
	   $(carusel_m).find(".carousel-items_m").animate({left: "0px"}, 200); 
	   
	   // Обновление активной точки
	   if(!skipUpdate_m){
	      var totalBlocks_m = $(carusel_m).find('.carousel-block_m').length;
	      currentSlide_m = (currentSlide_m - 1 + totalBlocks_m) % totalBlocks_m;
	      updateDots_m(carusel_m);
	   }
	}
	
	function right_carusel_m(carusel_m, skipUpdate_m){
	   var block_width_m = $(carusel_m).find('.carousel-block_m').outerWidth();
	   $(carusel_m).find(".carousel-items_m").animate({left: "-"+ block_width_m +"px"}, 200, function(){
		  $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(0).clone().appendTo($(carusel_m).find(".carousel-items_m")); 
	      $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(0).remove(); 
	      $(carusel_m).find(".carousel-items_m").css({"left":"0px"}); 
	   }); 
	   
	   // Обновление активной точки
	   if(!skipUpdate_m){
	      var totalBlocks_m = $(carusel_m).find('.carousel-block_m').length;
	      currentSlide_m = (currentSlide_m + 1) % totalBlocks_m;
	      updateDots_m(carusel_m);
	   }
	}

	// Функция для перехода к конкретному слайду
	function goToSlide_m(carusel_m, slideIndex_m){
	   var currentIndex_m = currentSlide_m;
	   var targetIndex_m = slideIndex_m;
	   var diff_m = targetIndex_m - currentIndex_m;
	   var totalBlocks_m = $(carusel_m).find('.carousel-block_m').length;
	   
	   if(diff_m === 0) return;
	   
	   // Вычисляем кратчайший путь (учитывая цикличность)
	   if(Math.abs(diff_m) > totalBlocks_m / 2){
	      diff_m = diff_m > 0 ? diff_m - totalBlocks_m : diff_m + totalBlocks_m;
	   }
	   
	   // Обновляем индекс
	   currentSlide_m = targetIndex_m;
	   updateDots_m(carusel_m);
	   
	   // Рекурсивная функция для последовательного выполнения анимаций
	   function animateStep_m(remaining_m){
	      if(remaining_m === 0) return;
	      
	      if(remaining_m > 0){
	         var block_width_m = $(carusel_m).find('.carousel-block_m').outerWidth();
	         $(carusel_m).find(".carousel-items_m").animate({left: "-"+ block_width_m +"px"}, 200, function(){
	            $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(0).clone().appendTo($(carusel_m).find(".carousel-items_m")); 
	            $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(0).remove(); 
	            $(carusel_m).find(".carousel-items_m").css({"left":"0px"});
	            animateStep_m(remaining_m - 1);
	         });
	      } else {
	         var block_width_m = $(carusel_m).find('.carousel-block_m').outerWidth();
	         $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(-1).clone().prependTo($(carusel_m).find(".carousel-items_m")); 
	         $(carusel_m).find(".carousel-items_m").css({"left":"-"+block_width_m+"px"});
	         $(carusel_m).find(".carousel-items_m .carousel-block_m").eq(-1).remove();    
	         $(carusel_m).find(".carousel-items_m").animate({left: "0px"}, 200, function(){
	            animateStep_m(remaining_m + 1);
	         });
	      }
	   }
	   
	   animateStep_m(diff_m);
	}

	// Функция обновления активной точки
	function updateDots_m(carusel_m){
	   $(carusel_m).find('.carousel-dot_m').removeClass('active_m');
	   $(carusel_m).find('.carousel-dot_m').eq(currentSlide_m).addClass('active_m');
	}

	// Инициализация точек
	function initDots_m(carusel_m){
	   var totalBlocks_m = $(carusel_m).find('.carousel-block_m').length;
	   var dotsContainer_m = $(carusel_m).find('.carousel-dots_m');
	   dotsContainer_m.empty();
	   
	   for(var i_m = 0; i_m < totalBlocks_m; i_m++){
	      var dot_m = $('<span class="carousel-dot_m" data-slide="' + i_m + '"></span>');
	      dotsContainer_m.append(dot_m);
	   }
	   
	   // Обработка клика на точку
	   $(carusel_m).find('.carousel-dot_m').on('click', function(){
	      var slideIndex_m = $(this).data('slide');
	      goToSlide_m(carusel_m, slideIndex_m);
	   });
	   
	   // Устанавливаем первую точку как активную
	   updateDots_m(carusel_m);
	}

	// Автоматическая прокрутка
	function auto_right_m(carusel_m){
		setInterval(function(){
			if (!$(carusel_m).is('.hover'))
				right_carusel_m(carusel_m);
		}, 1000)
	}

	//Обработка клика на стрелку вправо
	$(document).on('click', ".carousel-button-right_m",function(){ 
		var carusel_m = $(this).parents('.carousel_m');
		right_carusel_m(carusel_m);
		return false;
	});
	
	//Обработка клика на стрелку влево
	$(document).on('click',".carousel-button-left_m",function(){ 
		var carusel_m = $(this).parents('.carousel_m');
		left_carusel_m(carusel_m);
		return false;
	});

	// Свайп пальцем для мобильного слайдера отзывов (в обе стороны)
	var touchStartX_m = 0;
	var touchStartY_m = 0;
	var touchEndX_m = 0;
	var touchEndY_m = 0;
	var isSwiping_m = false;
	var minSwipeDistance_m = 35;
	var maxVerticalShift_m = 70;

	$(document).on('touchstart', '.carousel-wrapper_m, .carousel-items_m', function(e) {
		if (!e.originalEvent.touches || !e.originalEvent.touches.length) return;
		touchStartX_m = e.originalEvent.touches[0].clientX;
		touchStartY_m = e.originalEvent.touches[0].clientY;
		touchEndX_m = touchStartX_m;
		touchEndY_m = touchStartY_m;
		isSwiping_m = false;
	});

	$(document).on('touchmove', '.carousel-wrapper_m, .carousel-items_m', function(e) {
		if (!e.originalEvent.touches || !e.originalEvent.touches.length) return;
		touchEndX_m = e.originalEvent.touches[0].clientX;
		touchEndY_m = e.originalEvent.touches[0].clientY;
		if (Math.abs(touchStartX_m - touchEndX_m) > 10) {
			isSwiping_m = true;
		}
	});

	$(document).on('touchend', '.carousel-wrapper_m, .carousel-items_m', function() {
		if (!isSwiping_m) return;

		var swipeDistance_m = touchStartX_m - touchEndX_m;
		var verticalDistance_m = Math.abs(touchStartY_m - touchEndY_m);
		if (Math.abs(swipeDistance_m) < minSwipeDistance_m) return;
		if (verticalDistance_m > maxVerticalShift_m) return;

		var carusel_m = $(this).closest('.carousel_m');
		if (!carusel_m.length) return;

		if (swipeDistance_m > 0) {
			right_carusel_m(carusel_m);
		} else {
			left_carusel_m(carusel_m);
		}
	});

	// Навели курсор на карусель
	$(document).on('mouseenter', '.carousel_m', function(){$(this).addClass('hover')});
	//Убрали курсор с карусели
	$(document).on('mouseleave', '.carousel_m', function(){$(this).removeClass('hover')});

	//Раскомментируйте строку ниже, чтобы включить автоматическую прокрутку карусели
	// auto_right_m('.carousel_m:first');
	
	// Инициализация точек для всех каруселей
	$('.carousel_m').each(function(){
		initDots_m($(this));
	});

	initAllCarouselTracks();

	$(window).on('load', initAllCarouselTracks);

	var resizeCarouselTimer;
	$(window).on('resize', function() {
		clearTimeout(resizeCarouselTimer);
		resizeCarouselTimer = setTimeout(initAllCarouselTracks, 150);
	});
});


/// мега меню

jQuery(document).ready(function($) {
	'use strict';
  
	/* ===== 1) Показ нужной панели и позиционирование с поддержкой картинок ===== */
	const $nav = $('.nav-wrap');
	if (!$nav.length) return;
  
	const $menu = $nav.find('.smenu');
	const $layer = $nav.find('.mega-layer');
	if (!$menu.length || !$layer.length) return;

	const MEGA_MENU_BP = 1162;
	const MEGA_NS = 'travelizMega';

	function isMegaClickMode() {
	  return window.innerWidth <= MEGA_MENU_BP;
	}
  
	// Функция расчета сетки - динамически на основе количества секций
	function applyGridClass($grid) {
	  if (!$grid || !$grid.length) return;
	  
	  const $sections = $grid.find('> section');
	  const $textSections = $sections.not('.mega-image-section');
	  const $imageSection = $grid.find('.mega-image-section');
	  const textCount = $textSections.length;
	  const hasImage = $imageSection.length > 0;
	  
	  // Проверяем порядок элементов в DOM (без проверки RTL/LTR)
	  const firstSection = $sections.first();
	  const isImageFirst = firstSection.hasClass('mega-image-section');
	  
	  let gridTemplate = '';
	  
	  if (hasImage) {
		// Если картинка первая - она слева (3fr), затем текстовые колонки
		// Если картинка последняя - текстовые колонки сначала, картинка справа (3fr)
		if (isImageFirst) {
		  // Картинка первая (слева) - 3fr, затем текстовые колонки
		  if (textCount === 1) {
			gridTemplate = '3fr 1fr';
		  } else if (textCount === 2) {
			gridTemplate = '3fr 1fr 1fr';
		  } else if (textCount === 3) {
			gridTemplate = '3fr 1fr 1fr 1fr';
		  } else if (textCount === 4) {
			gridTemplate = '3fr 1fr 1fr 1fr 1fr';
		  } else {
			const textCols = '1fr '.repeat(textCount).trim();
			gridTemplate = '3fr ' + textCols;
		  }
		} else {
		  // Картинка последняя (справа) - текстовые колонки сначала, затем 3fr
		  if (textCount === 1) {
			gridTemplate = '1fr 3fr';
		  } else if (textCount === 2) {
			gridTemplate = '1fr 1fr 3fr';
		  } else if (textCount === 3) {
			gridTemplate = '1fr 1fr 1fr 3fr';
		  } else if (textCount === 4) {
			gridTemplate = '1fr 1fr 1fr 1fr 3fr';
		  } else {
			const textCols = '1fr '.repeat(textCount).trim();
			gridTemplate = textCols + ' 3fr';
		  }
		}
	  } else {
		// Если нет картинки - только текстовые колонки
		if (textCount === 1) {
		  gridTemplate = '1fr';
		} else if (textCount === 2) {
		  gridTemplate = '1fr 1fr';
		} else if (textCount === 3) {
		  gridTemplate = '1fr 1fr 1fr';
		} else {
		  gridTemplate = '1fr '.repeat(textCount).trim();
		}
	  }
	  
	  $grid.css('grid-template-columns', gridTemplate);
	}
  
	// Функция скрытия всех панелей
	function hideAll() {
	  const caretDefault = isMegaClickMode() ? '+' : '▾';
	  $layer.removeClass('mega-layer--open');
	  document.body.style.overflow = '';
	  $.each(panels, function(key, panel) {
		if (panel) {
		  $(panel).css({
			'display': 'none',
			'opacity': '0',
			'visibility': 'hidden'
		  }).attr('aria-hidden', 'true');
		}
	  });
	  // Убираем активный класс со всех пунктов меню
	  $menu.find('> div').removeClass('active');
	  // Возвращаем символ тогглера по текущему режиму (mobile: +, desktop: стрелка)
	  $menu.find('> div[data-panel] .caret2').text(caretDefault);
	}
  
	// Собираем все панели
	const panels = {};
	$layer.find('.mega').each(function() {
	  const $panel = $(this);
	  const panelId = $panel.attr('id');
	  if (!panelId) return;
	  
	  const id = panelId.replace('panel-', '');
	  panels[id] = $panel[0];
	  
	  const $grid = $panel.find('.mega-grid');
	  if ($grid.length) {
		// Применяем сетку к каждой панели
		applyGridClass($grid);
	  }
	});
	
  
	// Функция показа панели под меню-баром (от края до края)
	function positionPanel($div, $panel) {
	  if (!$div || !$div.length || !$panel || !$panel.length) return;
  
	  // Пересчитываем сетку (картинки уже в HTML)
	  const $grid = $panel.find('.mega-grid');
	  if (!$grid.length) return;
	  
	  // Применяем динамическую сетку с учетом количества секций и направления
	  applyGridClass($grid);

	  /* Узкий экран: одна колонка, контент «лежит» в потоке под меню */
	  if (isMegaClickMode()) {
		$grid.css({
		  'grid-template-columns': '1fr',
		  'grid-auto-flow': 'row',
		});
	  }
  
	  const flow = isMegaClickMode();
	  const showCss = {
		display: 'block',
		opacity: '1',
		visibility: 'visible',
		background: '#fff',
		width: '100%',
	  };
	  if (!flow) {
		showCss.left = '0';
		showCss.right = '0';
	  }
	  $panel.css(showCss).attr('aria-hidden', 'false');
	  
	  $panel.find('section').css({
		display: '',
		visibility: 'visible',
		opacity: '1',
	  });

	  if (flow) {
		$layer.addClass('mega-layer--open');
		document.body.style.overflow = 'hidden';
		$panel.find('img').css({
		  display: 'block',
		  visibility: 'visible',
		  opacity: '1',
		  width: 'auto',
		  maxWidth: '100%',
		  height: 'auto',
		});
	  } else {
		$panel.find('img').css({
		  display: 'block',
		  visibility: 'visible',
		  opacity: '1',
		  width: '100%',
		  height: 'auto',
		});
	  }
	}
  
	// Таймеры для задержки закрытия (только режим hover)
	let hideTimers = {};

	function clearHideTimers() {
	  Object.keys(hideTimers).forEach(function (tid) {
		clearTimeout(hideTimers[tid]);
	  });
	  hideTimers = {};
	}

	function bindMegaDocClose() {
	  $(document).off('click.' + MEGA_NS + 'Doc');
	  if (!isMegaClickMode()) {
		return;
	  }
	  $(document).on('click.' + MEGA_NS + 'Doc', function (e) {
		if (!isMegaClickMode()) {
		  return;
		}
		if (!$layer.hasClass('mega-layer--open')) {
		  return;
		}
		/* Клик вне открытой панели (по шапке, логотипу и т.д.) — закрыть */
		if ($(e.target).closest('.mega-layer').length) {
		  return;
		}
		hideAll();
		clearHideTimers();
	  });
	}

	function wireMegaMenuItems() {
	  $menu.find('> div[data-panel]').each(function () {
		const $div = $(this);
		const id = $div.attr('data-panel');
		if (!id) {
		  return;
		}

		const panel = panels[id];
		if (!panel) {
		  return;
		}
		const $panel = $(panel);

		$div.off('.' + MEGA_NS);
		$panel.off('.' + MEGA_NS);

		if (isMegaClickMode()) {
		  /* В мобильном режиме открытие панели — по .caret2 (+/-). */
		  const $caret = $div.find('.caret2');
		  if ($caret.length) {
			$caret.text('+');
		  }
		  const $toggle = $caret.length ? $caret : $div;
		  $toggle.on('click.' + MEGA_NS, function (e) {
			e.preventDefault();
			e.stopPropagation();
			const open =
			  $div.hasClass('active') &&
			  $panel.css('display') === 'block';
			if (open) {
			  hideAll();
			  if ($caret.length) $caret.text('+');
			  return;
			}
			hideAll();
			$div.addClass('active');
			positionPanel($div, $panel);
			if ($caret.length) $caret.text('−');
		  });
		  return;
		}

		// Desktop mode: always show default caret arrow.
		const $desktopCaret = $div.find('.caret2');
		if ($desktopCaret.length) {
		  $desktopCaret.text('▾');
		}

		$div.on('mouseenter.' + MEGA_NS, function () {
		  if (hideTimers[id]) {
			clearTimeout(hideTimers[id]);
			delete hideTimers[id];
		  }
		  hideAll();
		  $div.addClass('active');
		  positionPanel($div, $panel);
		});

		$div.on('mouseleave.' + MEGA_NS, function () {
		  hideTimers[id] = setTimeout(function () {
			$div.removeClass('active');
			$panel
			  .css({
				display: 'none',
				opacity: '0',
				visibility: 'hidden',
			  })
			  .attr('aria-hidden', 'true');
			delete hideTimers[id];
		  }, 200);
		});

		$panel.on('mouseenter.' + MEGA_NS, function () {
		  if (hideTimers[id]) {
			clearTimeout(hideTimers[id]);
			delete hideTimers[id];
		  }
		});

		$panel.on('mouseleave.' + MEGA_NS, function () {
		  hideTimers[id] = setTimeout(function () {
			$div.removeClass('active');
			$panel
			  .css({
				display: 'none',
				opacity: '0',
				visibility: 'hidden',
			  })
			  .attr('aria-hidden', 'true');
			delete hideTimers[id];
		  }, 200);
		});
	  });

	  bindMegaDocClose();
	}

	let lastMegaClickMode = isMegaClickMode();
	wireMegaMenuItems();

	function closeMegaFromArrow(e) {
	  if (!$layer.hasClass('mega-layer--open')) {
		return;
	  }
	  if (e) {
		e.preventDefault();
		e.stopPropagation();
	  }
	  hideAll();
	  clearHideTimers();
	}

	/* Стрелка закрытия: capture + клик по img внутри кнопки */
	document.addEventListener(
	  'click',
	  function travelizMegaArrowClick(e) {
		var el = e.target;
		var btn = el && el.closest ? el.closest('.left-close-menu-svg') : null;
		if (!btn || !$layer[0] || !$layer[0].contains(btn)) {
		  return;
		}
		closeMegaFromArrow(e);
	  },
	  true
	);
	document.addEventListener(
	  'touchend',
	  function travelizMegaArrowTouch(e) {
		var el = e.target;
		var btn = el && el.closest ? el.closest('.left-close-menu-svg') : null;
		if (!btn || !$layer[0] || !$layer[0].contains(btn)) {
		  return;
		}
		if (!$layer.hasClass('mega-layer--open')) {
		  return;
		}
		e.preventDefault();
		closeMegaFromArrow(e);
	  },
	  { capture: true, passive: false }
	);
	
	// Закрытие по ESC
	$(document).on('keydown', function(e) {
	  if (e.key === 'Escape' || e.keyCode === 27) {
		hideAll();
		clearHideTimers();
	  }
	});
  
	// Плавная фиксация меню при прокрутке
	let lastScrollTop = 0;
	const scrollThreshold = 100; // Расстояние прокрутки для активации фиксации
	
	$(window).on('scroll', function() {
	  const scrollTop = $(this).scrollTop();
	  
	  if (scrollTop > scrollThreshold) {
		// Добавляем класс для фиксации меню
		$nav.addClass('scrolled');

		$(".nav-wrap").css('box-shadow','none');
	  } else {
		// Убираем класс при возврате наверх
		$nav.removeClass('scrolled');
	  }
	  
	  lastScrollTop = scrollTop;
	});
	
	// Ресайз: смена режима клик/hover при пересечении 1162px + перепозиционирование панели
	let resizeTimer;
	$(window).on('resize', function() {
	  clearTimeout(resizeTimer);
	  resizeTimer = setTimeout(function() {
		const nowClick = isMegaClickMode();
		if (nowClick !== lastMegaClickMode) {
		  lastMegaClickMode = nowClick;
		  hideAll();
		  clearHideTimers();
		  wireMegaMenuItems();
		}

		let $openPanel = null;
		let $openDiv = null;

		$.each(panels, function(key, panel) {
		  const $p = $(panel);
		  if ($p.css('display') === 'block') {
			$openPanel = $p;
			const panelId = $p.attr('id').replace('panel-', '');
			$openDiv = $menu.find('> div[data-panel="' + panelId + '"]');
		  }
		});

		if ($openPanel && $openPanel.length && $openDiv && $openDiv.length) {
		  positionPanel($openDiv, $openPanel);
		}
	  }, 150);
	});
  });



  // Плавная фиксация меню при прокрутке
  const hero = document.querySelector('.hero-wrapper header');
  const intoHeader = document.querySelector('.into-header-0');
  let isScrolled = false;
  const scrollThreshold = 50; // Расстояние прокрутки для активации фиксации

  window.addEventListener('scroll', function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > scrollThreshold && !isScrolled) {
      // Добавляем класс для фиксации меню с плавной анимацией
      if (hero) hero.classList.add('scrolled');
      if (intoHeader) intoHeader.classList.add('scrolled');
      isScrolled = true;
    } else if (scrollTop <= scrollThreshold && isScrolled) {
      // Убираем класс при возврате наверх
      if (hero) hero.classList.remove('scrolled');
      if (intoHeader) intoHeader.classList.remove('scrolled');
      isScrolled = false;
    }
  });

