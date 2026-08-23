(function($) {
    'use strict';

    function feedbackT(key, fallback) {
        const tr = (typeof feedbackAjax !== 'undefined' && feedbackAjax.translations) ? feedbackAjax.translations : {};
        return tr[key] || fallback;
    }

    // Ждем загрузки DOM
    $(document).ready(function() {
        initCropperFunctionality();
    });

    function initCropperFunctionality() {
        // Переменная для хранения экземпляра Cropper
        let cropper = null;

        // Открытие модального окна для обрезки
        $(document).on('click', '#open-cropper-btn, .open-cropper-modal-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const modal = $('#cropper-modal');
            modal.removeClass('cropper-modal-hidden');
            // Сбрасываем состояние
            $('#cropper-image').attr('src', '');
            $('#crop-upload-btn').removeClass('show').css('display', 'none');
            $('#file-input-in-modal').val('');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Обработка файла в модальном окне
        $(document).on('change', '#file-input-in-modal', function(e) {
            const file = e.target.files[0];
            if (file) {
                loadImageToCropper(file);
            }
        });

        // Загрузка изображения в cropper
        function loadImageToCropper(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('cropper-image');
                if (!img) {
                    console.error('Cropper image element not found');
                    return;
                }
                
                img.src = e.target.result;
                
                // Уничтожаем предыдущий экземпляр cropper если есть
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                
                // Ждем загрузки изображения
                $(img).off('load.cropper').on('load.cropper', function() {
                    setTimeout(function() {
                        initCropper(img);
                    }, 100);
                });
                
                // Если изображение уже загружено
                if (img.complete && img.naturalWidth > 0) {
                    setTimeout(function() {
                        initCropper(img);
                    }, 100);
                }
            };
            reader.onerror = function() {
                alert(feedbackT('error_read_file', 'שגיאה בקריאת הקובץ'));
            };
            reader.readAsDataURL(file);
        }

        // Инициализация Cropper.js
        function initCropper(img) {
            // Проверяем наличие библиотеки Cropper
            if (typeof Cropper === 'undefined') {
                console.error('Cropper.js library is not loaded');
                alert(feedbackT('error_cropper_lib', 'ספריית Cropper.js לא נטענה. אנא רענן את הדף.'));
                return;
            }
            
            // Проверяем, что модальное окно видимо
            if ($('#cropper-modal').hasClass('cropper-modal-hidden')) {
                return;
            }
            
            // Уничтожаем предыдущий экземпляр если есть
            if (cropper) {
                try {
                    cropper.destroy();
                } catch (e) {
                    console.error('Error destroying cropper:', e);
                }
                cropper = null;
            }
            
            // Очищаем контейнер
            const container = $(img).parent();
            container.css('position', 'relative');
            
            // Инициализируем Cropper.js
            try {
                cropper = new Cropper(img, {
                    aspectRatio: NaN, // Свободное соотношение сторон
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    zoomable: true,
                    zoomOnTouch: true,
                    zoomOnWheel: true,
                    wheelZoomRatio: 0.1,
                    minCanvasWidth: 0,
                    minCanvasHeight: 0,
                    minCropBoxWidth: 0,
                    minCropBoxHeight: 0,
                    minContainerWidth: 0,
                    minContainerHeight: 0,
                    ready: function() {
                        $('#crop-upload-btn').addClass('show').css('display', 'inline-block');
                    }
                });
            } catch (error) {
                console.error('Error initializing Cropper:', error);
                alert(feedbackT('error_crop_init', 'שגיאה באתחול חיתוך התמונה') + ': ' + error.message);
            }
        }

        // Обрезка и сохранение изображения
        $(document).on('click', '#crop-upload-btn', function() {
            if (!cropper) {
                alert(feedbackT('select_image_first', 'בחר והעלה תמונה תחילה'));
                return;
            }
            
            try {
                // Получаем обрезанное изображение
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                
                if (canvas) {
                    // Конвертируем в blob для отправки на сервер
                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            const cropError = feedbackT('error_crop', 'שגיאה בחיתוך התמונה');
                            alert(cropError);
                            return;
                        }
                        
                        // Создаем FormData для отправки
                        const formData = new FormData();
                        formData.append('action', 'upload_feedback_image');
                        if (typeof feedbackAjax !== 'undefined') {
                            formData.append('nonce', feedbackAjax.nonce);
                        }
                        formData.append('image', blob, 'cropped-image.jpg');
                        
                        // Отключаем кнопку
                        const $btn = $('#crop-upload-btn');
                        const originalText = $btn.text();
                        const uploadingText = feedbackT('uploading', 'מעלה...');
                        $btn.prop('disabled', true).text(uploadingText);
                        
                        // Отправляем на сервер
                        $.ajax({
                            url: typeof feedbackAjax !== 'undefined' ? feedbackAjax.ajax_url : ajaxurl,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $btn.prop('disabled', false).text(originalText);
                                
                                if (response.success) {
                                    // Обновляем превью аватара
                                    $('.filesupload img.d44').attr('src', response.data.url);
                                    // Сохраняем имя файла в скрытом поле или data-атрибуте
                                    $('.filesupload img.d44').data('filename', response.data.filename);
                                    // Закрываем модальное окно
                                    closeCropperModal();
                                } else {
                                    const uploadErrorPrefix = feedbackT('error_upload_file', 'שגיאה בהעלאת הקובץ');
                                    alert(uploadErrorPrefix + ': ' + (response.data && response.data.message ? response.data.message : feedbackT('error_unknown', 'שגיאה לא ידועה')));
                                }
                            },
                            error: function(xhr, status, error) {
                                $btn.prop('disabled', false).text(originalText);
                                const uploadErrorPrefix = feedbackT('error_upload_file', 'שגיאה בהעלאת הקובץ');
                                alert(uploadErrorPrefix + ': ' + error);
                            }
                        });
                    }, 'image/jpeg', 0.9);
                }
            } catch (error) {
                console.error('Error cropping image:', error);
                alert(feedbackT('error_crop', 'שגיאה בחיתוך התמונה'));
            }
        });

        // Закрытие модального окна
        $(document).on('click', '#cancel-crop-btn', function() {
            closeCropperModal();
        });

        function closeCropperModal() {
            // Уничтожаем экземпляр cropper
            if (cropper) {
                try {
                    cropper.destroy();
                } catch (e) {
                    console.error('Error destroying cropper:', e);
                }
                cropper = null;
            }
            
            $('#cropper-modal').addClass('cropper-modal-hidden');
            $('#cropper-image').attr('src', '');
            $('#crop-upload-btn').removeClass('show').css('display', 'none');
            $('#file-input-in-modal').val('');
        }

        // Закрытие по клику вне модального окна
        $(document).on('click', '#cropper-modal', function(e) {
            if (e.target === this) {
                closeCropperModal();
            }
        });
    }

    // Демо-форма (flex «Отзывы туристов»): ничего не отправляется на сервер — только сообщение «на модерации» (перевод с сервера).
    $(document).ready(function() {
        $('.my-form-feedback-demo').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const $form = $(this);
            const name = $form.find('input[name="name"]').val().trim();
            const email = $form.find('input[name="email"]').val().trim();
            const text = $form.find('textarea[name="text"]').val().trim();
            const captcha = $form.find('input[name="captcha"]').is(':checked');

            if (!captcha) {
                alert(feedbackT('error_captcha', 'אנא אשר שאינך רובוט'));
                $form.find('input[name="captcha"]').focus();
                return false;
            }
            if (!name || name.length < 2) {
                alert(feedbackT('error_name', 'אנא הזן את שמך (לפחות 2 תווים)'));
                $form.find('input[name="name"]').focus();
                return false;
            }
            const namePattern = /^[\p{L}\p{M}\s\-'.]+$/u;
            if (!namePattern.test(name)) {
                alert(feedbackT('error_invalid_name', 'השם מכיל תווים לא חוקיים'));
                $form.find('input[name="name"]').focus();
                return false;
            }
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert(feedbackT('error_email', 'כתובת האימייל אינה תקינה'));
                $form.find('input[name="email"]').focus();
                return false;
            }
            if (!text || text.length < 10) {
                alert(feedbackT('error_text', 'אנא כתוב את המשוב שלך (לפחות 10 תווים)'));
                $form.find('textarea[name="text"]').focus();
                return false;
            }

            const thanks = $form.attr('data-thanks-msg') || $form.data('thanksMsg') || feedbackT('success_prefix', 'תודה!');
            alert(thanks);
            $form[0].reset();
            return false;
        });
    });

    // Обработка формы отправки отзывов
    $(document).ready(function() {

        $('.my-form').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);
            // Flexible-блок: кнопка .newsubmit — только alert, без AJAX и без отправки на сервер
            if ($form.closest('section.flexibol-open').length && $form.find('.newsubmit').length) {
                const msg = $form.attr('data-moderation-msg') || feedbackT('success_prefix', 'תודה! הביקורת שלך בבדיקה.');
                alert(msg);
                return false;
            }

            const name = $form.find('input[name="name"]').val().trim();
            const email = $form.find('input[name="email"]').val().trim();
            const text = $form.find('textarea[name="text"]').val().trim();
            const captcha = $form.find('input[name="captcha"]').is(':checked');

            // Логируем базовые данные формы (без текста отзыва целиком)
            console.log('[feedback] submit click', { name, email, hasText: text.length > 0, captchaChecked: captcha });

            // Получаем рейтинг из выбранной звезды (если используется)
            let starsRating = 0;
            const checkedStar = $form.find('input[name="stars"]:checked');
            if (checkedStar.length > 0) {
                starsRating = parseInt(checkedStar.val()) || 0;
            }

            // Получаем переводы (если есть)
            console.log('[feedback] feedbackAjax object', typeof feedbackAjax !== 'undefined' ? feedbackAjax : null);
            
            // Получаем имя файла изображения из аватара
            const photoUrl = $('.filesupload img.d44').attr('src');
            let photoFilename = '';
            
            if (photoUrl) {
                // Проверяем разные варианты пути
                if (photoUrl.indexOf('/uploads/') !== -1) {
                    photoFilename = photoUrl.split('/uploads/')[1].split('?')[0];
                } else if (photoUrl.indexOf('uploads/') !== -1) {
                    photoFilename = photoUrl.split('uploads/')[1].split('?')[0];
                } else if ($('.filesupload img.d44').data('filename')) {
                    photoFilename = $('.filesupload img.d44').data('filename');
                }
            }
            
            // Проверка капчи
            if (!captcha) {
                alert(feedbackT('error_captcha', 'אנא אשר שאינך רובוט'));
                $form.find('input[name="captcha"]').focus();
                return false;
            }
            
            // Валидация имени
            if (!name || name.length < 2) {
                alert(feedbackT('error_name', 'אנא הזן את שמך (לפחות 2 תווים)'));
                $form.find('input[name="name"]').focus();
                return false;
            }
            
            // Проверка на валидные символы в имени (любые буквы Unicode, диакритика, пробелы, дефисы, апострофы и точки)
            const namePattern = /^[\p{L}\p{M}\s\-'.]+$/u;
            if (!namePattern.test(name)) {
                alert(feedbackT('error_invalid_name', 'השם מכיל תווים לא חוקיים'));
                $form.find('input[name="name"]').focus();
                return false;
            }
            
            // Валидация email (если указан)
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert(feedbackT('error_email', 'כתובת האימייל אינה תקינה'));
                $form.find('input[name="email"]').focus();
                return false;
            }
            
            // Валидация текста отзыва
            if (!text || text.length < 10) {
                alert(feedbackT('error_text', 'אנא כתוב את המשוב שלך (לפחות 10 תווים)'));
                $form.find('textarea[name="text"]').focus();
                return false;
            }
            
            // Определяем язык для отправки (берем из feedbackAjax.current_lang, который приходит из WordPress/Polylang)
            const currentLang = (typeof feedbackAjax !== 'undefined' && feedbackAjax.current_lang)
                ? feedbackAjax.current_lang
                : 'en';
            
            // Отправляем данные
            const formData = {
                action: 'submit_feedback_form',
                nonce: typeof feedbackAjax !== 'undefined' ? feedbackAjax.form_nonce : '',
                name: name,
                email: email,
                text: text,
                rating: starsRating,
                photo_filename: photoFilename,
                captcha: captcha ? 'on' : '',
                language: currentLang
            };

            console.log('[feedback] sending AJAX', formData);
            
            const $submitBtn = $form.find('.submit');
            const originalBtnText = $submitBtn.val();
            const sendingText = feedbackT('sending', 'שולח...');
            $submitBtn.prop('disabled', true).val(sendingText);
            
            $.ajax({
                url: typeof feedbackAjax !== 'undefined' ? feedbackAjax.ajax_url : ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $submitBtn.prop('disabled', false).val(originalBtnText);
                    console.log('[feedback] AJAX success response', response);

                    if (response && response.success) {
                        alert(response.data && response.data.message ? response.data.message : feedbackT('success_prefix', 'תודה!'));
                        // Очищаем форму
                        $form[0].reset();
                        // Сбрасываем аватар
                        $('.filesupload img.d44').attr('src', 'https://weddilove.com/uploads/avatar.svg');
                        $('.filesupload img.d44').removeData('filename');
                    } else {
                        const msg = response && response.data && response.data.message ? response.data.message : feedbackT('error_submit', 'שגיאה בשליחת הטופס');
                        console.warn('[feedback] AJAX logical error', msg, response);
                        alert(msg);
                    }
                },
                error: function(xhr, status, error) {
                    $submitBtn.prop('disabled', false).val(originalBtnText);
                    console.error('[feedback] AJAX transport error', { status, error, xhr });
                    alert(feedbackT('error_generic', 'שגיאה') + ': ' + error);
                }
            });
            
            return false;
        });
    });

})(jQuery);

// Stationary video frames open/close by triangle click
jQuery(function($) {
	$(document).on('click', '.video-review-trigger, .video-review-trigger .rec, .video-review-trigger .video-otziv, .video-review-trigger img.rec', function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $trigger = $(this).closest('.video-review-trigger');
		if (!$trigger.length) return;

		var date = $trigger.attr('data-date');
		if (!date) return;

		$('.video-stationary-panel').stop(true, true).slideUp(150);
		var $panel = $('#video-stationary-panel-' + date);
		if ($panel.length) {
			$panel.stop(true, true).slideDown(150);
		}
	});
});


(function ($) {
	$(document).on('click', '.new_order-mr, .flexibol-toggle', function (e) {
		e.preventDefault();
		e.stopPropagation();
		var $btn = $(this);
		var $wrapFb = $btn.closest('.flexibol-block');
		if ($wrapFb.length) {
			var $panel = $wrapFb.find('.s-flexibol-rewopen').first();
			if (!$panel.length) {
				$panel = $wrapFb.find('section.flexibol-open').first();
			}
			if (!$panel.length) {
				$panel = $btn.closest('.s-flexibol-tourist-reviews').nextAll('.s-flexibol-rewopen, section.flexibol-open').first();
			}
			if ($panel.length) {
				$panel.stop(true, true);
				if ($panel.is(':visible')) {
					$panel.slideUp(500);
				} else {
					$panel.slideDown(500);
				}
			}
			return;
		}
		$('.rewopen').not('.s-flexibol-rewopen').not('.flexibol-open').each(function () {
			var $p = $(this);
			$p.stop(true, true);
			if ($p.is(':visible')) {
				$p.slideUp(500);
			} else {
				$p.slideDown(500);
			}
		});
	});
})(jQuery);