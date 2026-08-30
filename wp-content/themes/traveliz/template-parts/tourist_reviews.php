<?php
$tourist_reviews_background = get_field( 'tourist_reviews_background_image', 'option' );
$tourist_reviews_bg_url     = '';

if ( is_array( $tourist_reviews_background ) && ! empty( $tourist_reviews_background['url'] ) ) {
	$tourist_reviews_bg_url = (string) $tourist_reviews_background['url'];
} elseif ( is_numeric( $tourist_reviews_background ) ) {
	$tourist_reviews_bg_url = (string) wp_get_attachment_image_url( (int) $tourist_reviews_background, 'full' );
} elseif ( is_string( $tourist_reviews_background ) ) {
	$tourist_reviews_bg_url = $tourist_reviews_background;
}
?>
    <section
        <?php if ( $tourist_reviews_bg_url ) : ?>
            style="background-image: url('<?php echo esc_url( $tourist_reviews_bg_url ); ?>'); background-repeat: no-repeat; background-position: center; background-size: cover;"
        <?php endif; ?>
        class="reviews-section google treveler reviews2"
    >
               
               <div class="df-reviews2"> 
                <div class="container-3">

                <?php 
                    // Заголовок блока "Отзывы туристов" из ACF options "Tourist Reviews"
                    // Для options-subpage ACF post_id остаётся 'option'
                    $tourist_reviews_title = get_field( 'tourist_reviews_title', 'option' );
                ?>
                  <?php if ( $tourist_reviews_title ) : ?>
                      <h2 class="white"><?php echo wp_kses_post( $tourist_reviews_title ); ?></h2>
                  <?php endif; ?>
                 
                    <div class="reviews-container-into caruael_t reviews2_caruael_t googl2">
                        <div class="carousel_m shadow_m">
                            <div class="carousel-wrapper_m">
                                <div class="carousel-items_m caruael_tt">
                                    <?php
                                    // Получаем отзывы из плагина WT Reviews (как [wt_reviews]), но выводим в этот слайдер
                                    global $wpdb;
                                    $table_name = class_exists( 'WT_Reviews' ) ? WT_Reviews::get_table_name() : '';                                    $reviews    = array();

                                    if ( $table_name ) {
                                        $db_name      = DB_NAME;
                                        $table_exists = $wpdb->get_var(
                                            $wpdb->prepare(
                                                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s',
                                                $db_name,
                                                $table_name
                                            )
                                        );
                                        if ( ! $table_exists ) {
                                            $table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
                                        }

                                        if ( $table_exists ) {
                                            $reviews = $wpdb->get_results( "SELECT * FROM `{$table_name}` WHERE keywords = '1' AND name != '' AND name IS NOT NULL ORDER BY news_id DESC" );
                                        }
                                    }

                                    if ( ! empty( $reviews ) ) :
                                        $tourist_reviews_star_idx = 0;
                                        foreach ( $reviews as $item ) :
                                            $photo_url = '';
                                            if ( ! empty( $item->reiting ) ) {
                                                $photo_url = site_url( '/uploads/' . esc_attr( $item->reiting ) );
                                            }
                                            if ( empty( $photo_url ) ) {
                                                $photo_url = get_template_directory_uri() . '/img/avatar.webp';
                                            }

                                            $review_name = ! empty( $item->name ) ? $item->name : '';                                            $review_text = ! empty( $item->text ) ? $item->text : '';                                            // 65% карточек — 5 звёзд, 35% — 4 звезды (цикл по 100 позициям).
                                            $display_rating = ( ( $tourist_reviews_star_idx % 100 ) < 65 ) ? 5 : 4;
                                            ++$tourist_reviews_star_idx;
                                    ?>
                                    <div class="carousel-block_m">
                                        <div class="gogle3">
                                            <div class="image-rew2 googl3">
                                                 <div class="foto-otzv">
                                                    <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $review_name ) ); ?>">
                                                    <?php if ( $review_name ) : ?>
                                                        <span class="mg"><?php echo wp_kses_post( $review_name ); ?></span>
                                                    <?php endif; ?>
                                                 </div>
                                                 <div class="star-2 review-rating-display">
                                                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                                                        <span class="star <?php echo $i <= $display_rating ? 'star-filled' : 'star-empty'; ?>"><?php echo $i <= $display_rating ? '★' : '☆'; ?></span>
                                                    <?php endfor; ?>
                                                 </div>
                                             </div>
                                            <?php if ( $review_text ) : ?>
                                                <div class="sity-t">
                                                    <?php echo wp_kses_post( $review_text ); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                            <div class="wrap-dots-wra">
                                <div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a></div>
                                <div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a></div>
                            </div>
                        </div>
                        <div class="wrap-order-t gogle-button-open2">
                        <?php 
                            // Текст кнопки "Оставить отзыв" берём из опций Tourist Reviews
                            $tourist_reviews_button = get_field( 'tourist_reviews_button', 'option' );
                        ?>
                            <?php if ( $tourist_reviews_button ) : ?>
                                <a class="order-mr gogle-button new_order-mr" href="#ex1">
                                    <span><?php echo wp_kses_post( $tourist_reviews_button ); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>


                </div>
                </div>
            </section>
            <section class="rewopen">
                <div class="container-3"> 
                                <div style="background:#fff;" class="reviews_form">
                                    <div class="container otziv-9">
                                    
                                        
                                        <div class="z-z-z">
                                            <div class="z-image">
                                        
                                                <div class="nikolaev">
                                                    <div class="f471">
                                                        <div class="filesupload">
                                                            
                                                            <img class="d44" src="<?php echo get_template_directory_uri(); ?>/img/avatar.webp"/>
                                                        </div>
                                                        <div class="open-cropper-modal-btn" id="open-cropper-btn">
                                                            <?php echo esc_html( get_theme_translation( 'feedback_select_photo' ) ); ?>
                                                        </div>
                                                        <div class="vibrat"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="ex1" class="z-form">
                                                <form class="my-form">
                                                <div class="review_stars_wrap">
                                                        <div id="review_stars">
                                                            <input id="star-5" type="radio" name="stars" value="5" />
                                                            <label title="5" for="star-5">
                                                                <i class="fas fa-star"></i>
                                                            </label>
                                                            <input id="star-4" type="radio" name="stars" value="4" />
                                                            <label title="4" for="star-4">
                                                                <i class="fas fa-star"></i>
                                                            </label>
                                                            <input id="star-3" type="radio" name="stars" value="3" />
                                                            <label title="3" for="star-3">
                                                                <i class="fas fa-star"></i>
                                                            </label>
                                                            <input id="star-2" type="radio" name="stars" value="2" />
                                                            <label title="2" for="star-2">
                                                                <i class="fas fa-star"></i>
                                                            </label>
                                                            <input id="star-1" type="radio" name="stars" value="1" />
                                                            <label title="1" for="star-1">
                                                                <i class="fas fa-star"></i>
                                                            </label>
                                                        </div>
                                                </div>
                                                    <input class="title" placeholder="<?php echo esc_attr(get_theme_translation('feedback_name_placeholder')); ?>" name="name" type="text" required>
                                                    <input class="ss" placeholder="<?php echo esc_attr(get_theme_translation('feedback_email_placeholder')); ?>" name="email" type="email">
                                                    <textarea placeholder="<?php echo esc_attr(get_theme_translation('feedback_text_placeholder')); ?>" cols="30" rows="10" class="textaraa" name="text" required></textarea>

                                                    <div class="captcha-wrapper">
                                                        <label>
                                                            <input type="checkbox" name="captcha" required>
                                                            <span><?php echo esc_html( get_theme_translation( 'comment_captcha' ) ); ?></span>
                                                        </label>
                                                    </div>

                                                    <input type="submit" class="submit" value="<?php echo esc_attr(get_theme_translation('feedback_submit')); ?>">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Модальное окно для обрезки изображения -->
                                 <div id="cropper-modal" class="cropper-modal-hidden">
                                     <div class="cropper-modal-content">
                                         <button id="cancel-crop-btn" aria-label="<?php echo esc_attr( get_theme_translation( 'feedback_cancel' ) ); ?>" style="position: absolute; top: 10px; right: 10px; background: transparent; color: #000; border: none; padding: 0; cursor: pointer; font-size: 24px; z-index: 10001; line-height: 1;">&#10005;</button>
                                        <div class="cropper-file-select">
                                            <input type="file" id="file-input-in-modal" accept="image/*">
                                            <label for="file-input-in-modal" class="custom-file-button">
                                                <span class="file-button-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M21 19V5C21 3.9 20.1 3 19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19ZM8.5 13.5L11 16.51L14.5 12L19 18H5L8.5 13.5Z" fill="currentColor"/>
                                                    </svg>
                                                </span>
                                                <span class="file-button-text">
                                                    <?php echo esc_html( get_theme_translation( 'feedback_select_photo' ) ); ?>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="cropper-image-container">
                                            <img id="cropper-image" src="" style="max-width: 100%; display: block;">
                                        </div>
                                        <div class="cropper-actions">
                                            <button id="crop-upload-btn" style="background: #333; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-size: 16px; font-family: 'Heebo', sans-serif; display: none;"><?php echo esc_html(get_theme_translation('feedback_crop_upload')); ?></button>
                                        </div>
                                    </div>
                                </div>
                                </div>
            </section>