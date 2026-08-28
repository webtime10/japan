jQuery(function($) {
    function isCarouselRtl(carusel) {
        var $items = $(carusel).find('.now-carousel-items').first();
        if ($items.length) {
            if ($items.attr('dir') === 'rtl' || $items.css('direction') === 'rtl') {
                return true;
            }
        }
        return document.documentElement.getAttribute('dir') === 'rtl';
    }

    // Обработка клика на стрелку вправо
    $(document).on('click', ".now-carousel-button-right", function () {
        var carusel = $(this).parents('.now-carousel');
        if (isCarouselRtl(carusel)) {
            left_carusel(carusel);
        } else {
            right_carusel(carusel);
        }
        return false;
    });

    // Обработка клика на стрелку влево
    $(document).on('click', ".now-carousel-button-left", function () {
        var carusel = $(this).parents('.now-carousel');
        if (isCarouselRtl(carusel)) {
            right_carusel(carusel);
        } else {
            left_carusel(carusel);
        }
        return false;
    });

    function left_carusel(carusel) {
        var block_width = $(carusel).find('.now-carousel-block').outerWidth();
        $(carusel).find(".now-carousel-items .now-carousel-block").eq(-1).clone().prependTo($(carusel).find(".now-carousel-items"));
        $(carusel).find(".now-carousel-items").css({ "left": "-" + block_width + "px" });
        $(carusel).find(".now-carousel-items .now-carousel-block").eq(-1).remove();
        $(carusel).find(".now-carousel-items").animate({ left: "0px" }, 200, function () {
            setActiveCenter(carusel, false);
        });
    }

    function right_carusel(carusel) {
        var block_width = $(carusel).find('.now-carousel-block').outerWidth();
        $(carusel).find(".now-carousel-items").animate({ left: "-" + block_width + "px" }, 200, function () {
            $(carusel).find(".now-carousel-items .now-carousel-block").eq(0).clone().appendTo($(carusel).find(".now-carousel-items"));
            $(carusel).find(".now-carousel-items .now-carousel-block").eq(0).remove();
            $(carusel).find(".now-carousel-items").css({ "left": "0px" });
            setActiveCenter(carusel, false);
        });
    }

    function setActiveCenter(carusel, preserveIfAlreadyActive) {
        var $carusel = $(carusel);
        var $wrapper = $carusel.find(".now-carousel-wrapper").first();
        if (!$wrapper.length) return;

        // If server already marked some slide as active, preserve it during initial init.
        if (preserveIfAlreadyActive) {
            if ($carusel.find(".now-carousel-items .now-carousel-block.is-active").length) {
                return;
            }
        }

        var wrapperRect = $wrapper[0].getBoundingClientRect();
        var wrapperLeft = wrapperRect.left;
        var wrapperRight = wrapperRect.right;
        var viewCenter = wrapperLeft + wrapperRect.width / 2;

        var visibleBlocks = [];
        $carusel.find(".now-carousel-items .now-carousel-block").each(function () {
            var el = this;
            var rect = el.getBoundingClientRect();
            var left = rect.left;
            var right = rect.right;

            // блок считается видимым, если он хотя бы частично попадает в область wrapper
            if (right > wrapperLeft && left < wrapperRight) {
                var center = left + rect.width / 2;
                visibleBlocks.push({ el: $(el), center: center });
            }
        });

        var $blocks = $carusel.find(".now-carousel-items .now-carousel-block");
        $blocks.removeClass("is-active");

        if (visibleBlocks.length === 0) {
            // Fallback: mark the middle block active (works with flex + justify-content center).
            if ($blocks.length) {
                var midIndex = Math.floor($blocks.length / 2);
                $blocks.eq(midIndex).addClass("is-active");
            }
            return;
        }

        // находим блок, чей центр ближе всего к центру области просмотра
        var active = visibleBlocks.reduce(function (best, curr) {
            if (!best) return curr;
            return (Math.abs(curr.center - viewCenter) < Math.abs(best.center - viewCenter)) ? curr : best;
        }, null);

        // если по геометрии не получилось выбрать - ставим "центр" по индексу
        if (active && active.el) {
            active.el.addClass("is-active");
        } else if ($blocks.length) {
            var midIndex = Math.floor($blocks.length / 2);
            $blocks.eq(midIndex).addClass("is-active");
        }
    }

    // Инициализация: выбираем именно seasons (slider-pogoda), иначе другая карусель может перехватить active.
    function initCarouselActive() {
        // First try visible seasons block (ACF). If none found, fallback to first.
        var $carousel = $('.slider-pogoda:visible .now-carousel').first();
        if (!$carousel.length) {
            $carousel = $('.slider-pogoda .now-carousel').first();
        }
        if (!$carousel.length) return;

        // Если сервер уже отметил активный слайд (is-active), сохраняем его.
        // Иначе ставим актив на "середину" и потом уточняем геометрией.
        var $blocks = $carousel.find(".now-carousel-items .now-carousel-block");
        var hasActive = $blocks.filter(".is-active").length > 0;

        if (!hasActive && $blocks.length) {
            $blocks.removeClass("is-active");
            $blocks.eq(Math.floor($blocks.length / 2)).addClass("is-active");
        }

        setActiveCenter($carousel, true);
    }
    initCarouselActive();
    // Доп. повторы: размеры/offsets могут корректно появляться не сразу
    setTimeout(initCarouselActive, 250);
    setTimeout(initCarouselActive, 800);
    window.addEventListener('load', initCarouselActive);
    // auto_right('.now-carousel:first'); // включить, если нужна автопрокрутка

    // Автоматическая прокрутка
    function auto_right(carusel) {
        setInterval(function () {
            if (!$(carusel).is('.now-hover'))
                right_carusel(carusel);
        }, 1000);
    }

    // Навели курсор на карусель
    $(document).on('mouseenter', '.now-carousel', function () { $(this).addClass('now-hover'); });
    // Убрали курсор с карусели
    $(document).on('mouseleave', '.now-carousel', function () { $(this).removeClass('now-hover'); });
});