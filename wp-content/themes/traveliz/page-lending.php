<?php
/* Template Name: Lending Page */

get_header();
?>

<?php
// Landing Page - переменные
$header_background = get_field('header_background');
$hero_title        = get_field('hero_title');
$hero_subtitle     = get_field('hero_subtitle');

$button_1_text     = get_field('button_1_text');
$button_1_type     = get_field('button_1_type');
$button_1_link     = get_field('button_1_link');

$button_2_text     = get_field('button_2_text');
$button_2_type     = get_field('button_2_type');
$button_2_link     = get_field('button_2_link');

// Получаем URL фонового изображения
$bg_image_url = '';
if ( $header_background ) {
	if ( is_array( $header_background ) && ! empty( $header_background['url'] ) ) {
		$bg_image_url = $header_background['url'];
	} elseif ( is_numeric( $header_background ) ) {
		$img_src = wp_get_attachment_image_src( $header_background, 'full' );
		if ( $img_src && ! empty( $img_src[0] ) ) {
			$bg_image_url = $img_src[0];
		}
	} elseif ( is_string( $header_background ) ) {
		$bg_image_url = $header_background;
	}
}
?>

        <?php if ( $hero_title ) : ?>
            <section class="landing-hero-section l">
                <div class="container-4">
                    <div class="landing-hero-content">


                        <div class="landing-hero-text">

                            <img class="ellipse312" src="<?php echo get_template_directory_uri(); ?>/img/ellipse312.webp" alt="">

                            <?php if ( $hero_title ) : ?>
                                <h1><?php echo wp_kses_post( $hero_title ); ?></h1>
                                <?php endif; ?>

                                    <?php if ( $hero_subtitle ) : ?>
                                        <p>
                                            <?php echo wp_kses_post( $hero_subtitle ); ?>
                                        </p>
                                        <?php endif; ?>

                                            <?php if ( $button_1_text || $button_2_text ) : ?>
                                                <div class="landing-hero-buttons">
                                                    <div class="hero-btns-group2">
                                                        <?php if ( $button_1_text ) : ?>
                                                            <?php if ( $button_1_type == 'popup' ) : ?>
                                                                <button class="order-mr landing_link modal-trigger_wt" data-popup="button-1">
                                                                    <span class="btn-text"><?php echo wp_kses_post( $button_1_text ); ?></span>
                                                                    <img src="<?php echo get_template_directory_uri(); ?>/img/plus-circle.svg" alt="">
                                                                </button>
                                                                <?php else : ?>
                                                                    <a href="<?php echo esc_url( $button_1_link ); ?>" class="order-mr landing_link">
                                                                        <span class="btn-text"><?php echo wp_kses_post( $button_1_text ); ?></span>
                                                                        <img src="<?php echo get_template_directory_uri(); ?>/img/plus-circle.svg" alt="">
                                                                    </a>
                                                                    <?php endif; ?>
                                                                        <?php endif; ?>

                                                                            <?php if ( $button_2_text ) : ?>
                                                                                <?php if ( $button_2_type == 'popup' ) : ?>
                                                                                    <button class="whatsapp-button land_w modal-trigger_wt" data-popup="button-2">

                                                                                        <span class="btn-text"><?php echo wp_kses_post( $button_2_text ); ?></span>
                                                                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M17.3492 2.96406C15.4372 1.05859 12.888 0 10.1971 0C4.60284 0 0.0708119 4.51666 0.0708119 10.0919C0.0708119 11.8562 0.566504 13.6205 1.41626 15.1026L0 20.325L5.38179 18.9135C6.86886 19.6898 8.49756 20.1132 10.1971 20.1132C15.7913 20.1132 20.3233 15.5966 20.3233 10.0213C20.2525 7.41014 19.2611 4.86952 17.3492 2.96406ZM15.0832 13.6911C14.8707 14.2557 13.8793 14.8203 13.3837 14.8909C12.9588 14.9614 12.3923 14.9614 11.8258 14.8203C11.4717 14.6791 10.976 14.538 10.4095 14.2557C7.86024 13.1971 6.23155 10.6565 6.08992 10.4448C5.94829 10.3036 5.02773 9.10389 5.02773 7.83358C5.02773 6.56327 5.66504 5.99868 5.87748 5.71639C6.08992 5.4341 6.37317 5.4341 6.58561 5.4341C6.72723 5.4341 6.93967 5.4341 7.0813 5.4341C7.22292 5.4341 7.43537 5.36353 7.6478 5.85754C7.86024 6.35155 8.35593 7.62186 8.42675 7.69243C8.49756 7.83358 8.49756 7.97472 8.42675 8.11587C8.35593 8.25701 8.28512 8.39816 8.14349 8.5393C8.00187 8.68045 7.86024 8.89217 7.78943 8.96274C7.6478 9.10389 7.50618 9.24503 7.6478 9.45675C7.78943 9.73904 8.28512 10.5153 9.06406 11.2211C10.0554 12.0679 10.8344 12.3502 11.1176 12.4914C11.4009 12.6325 11.5425 12.562 11.6841 12.4208C11.8258 12.2797 12.3215 11.7151 12.4631 11.4328C12.6047 11.1505 12.8172 11.2211 13.0296 11.2916C13.242 11.3622 14.5167 11.9974 14.7291 12.1385C15.0124 12.2797 15.154 12.3502 15.2248 12.4208C15.2956 12.6325 15.2956 13.1265 15.0832 13.6911Z" fill="white" />
                                                                                        </svg>
                                                                                    </button>
                                                                                    <?php else : ?>
                                                                                        <a href="<?php echo esc_url( $button_2_link ); ?>" class="whatsapp-button land_w">

                                                                                            <span class="btn-text"><?php echo wp_kses_post( $button_2_text ); ?></span>
                                                                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <path d="M17.3492 2.96406C15.4372 1.05859 12.888 0 10.1971 0C4.60284 0 0.0708119 4.51666 0.0708119 10.0919C0.0708119 11.8562 0.566504 13.6205 1.41626 15.1026L0 20.325L5.38179 18.9135C6.86886 19.6898 8.49756 20.1132 10.1971 20.1132C15.7913 20.1132 20.3233 15.5966 20.3233 10.0213C20.2525 7.41014 19.2611 4.86952 17.3492 2.96406ZM15.0832 13.6911C14.8707 14.2557 13.8793 14.8203 13.3837 14.8909C12.9588 14.9614 12.3923 14.9614 11.8258 14.8203C11.4717 14.6791 10.976 14.538 10.4095 14.2557C7.86024 13.1971 6.23155 10.6565 6.08992 10.4448C5.94829 10.3036 5.02773 9.10389 5.02773 7.83358C5.02773 6.56327 5.66504 5.99868 5.87748 5.71639C6.08992 5.4341 6.37317 5.4341 6.58561 5.4341C6.72723 5.4341 6.93967 5.4341 7.0813 5.4341C7.22292 5.4341 7.43537 5.36353 7.6478 5.85754C7.86024 6.35155 8.35593 7.62186 8.42675 7.69243C8.49756 7.83358 8.49756 7.97472 8.42675 8.11587C8.35593 8.25701 8.28512 8.39816 8.14349 8.5393C8.00187 8.68045 7.86024 8.89217 7.78943 8.96274C7.6478 9.10389 7.50618 9.24503 7.6478 9.45675C7.78943 9.73904 8.28512 10.5153 9.06406 11.2211C10.0554 12.0679 10.8344 12.3502 11.1176 12.4914C11.4009 12.6325 11.5425 12.562 11.6841 12.4208C11.8258 12.2797 12.3215 11.7151 12.4631 11.4328C12.6047 11.1505 12.8172 11.2211 13.0296 11.2916C13.242 11.3622 14.5167 11.9974 14.7291 12.1385C15.0124 12.2797 15.154 12.3502 15.2248 12.4208C15.2956 12.6325 15.2956 13.1265 15.0832 13.6911Z" fill="white" />
                                                                                            </svg>
                                                                                        </a>
                                                                                        <?php endif; ?>
                                                                                            <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
            </div>
            <?php endif; ?>
   

<?php
// Guarantees and Trust блок - переменные (берутся с options-страницы "Guarantees and Trust")
$garanty_title        = get_field('_garanty_title', 'option');
$garanty_subtitle     = get_field('_garanty_subtitle', 'option');
$garanty_card_1_title = get_field('_garanty_card1_title', 'option');
$garanty_card_1_text  = get_field('_garanty_card1_text', 'option');
$garanty_card_2_title = get_field('_garanty_card2_title', 'option');
$garanty_card_2_text  = get_field('_garanty_card2_text', 'option');
$garanty_card_3_title = get_field('_garanty_card3_title', 'option');
$garanty_card_3_text  = get_field('_garanty_card3_text', 'option');
$garanty_trust_text   = get_field('_garanty_trust_text', 'option');
?>
<?php get_template_part('template-parts/swiss_experience'); ?>
<p class="dop-rew"></p>
                    <?php get_template_part('template-parts/reviews'); ?>
                     <?php get_template_part( 'template-parts/what_you_will_get', null, array( 'omit_buttons' => true ) ); ?>
<?php

$travelers_title    = get_field('travelers_stories_title', 'option');
$travelers_subtitle = get_field('travelers_stories_subtitle', 'option');

// Получаем записи кастомного типа "Travelers Stories" с учетом текущего языка
$travelers_stories_args = array(
    'post_type'      => 'travelers_stories',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
);

// Добавляем фильтр по языку, если Polylang активен
if ( function_exists('pll_current_language') ) {
    $current_lang = pll_current_language();
    if ( $current_lang ) {
        $travelers_stories_args['lang'] = $current_lang;
    }
}

$travelers_stories_query = new WP_Query( $travelers_stories_args );
?>

<?php if ( $travelers_stories_query->have_posts() ) : ?>
                                <section class="reviews-section history">
                                    <div class="container-4">
                                        <?php if ( $travelers_title ) : ?>
                                            <h2 class="gip-0"><?php echo wp_kses_post( $travelers_title ); ?></h2>
                                        <?php else : ?>
                                            <h2 class="gip-0">Истории путешественников</h2>
                                        <?php endif; ?>
                                        
                                        <?php if ( $travelers_subtitle ) : ?>
                                            <p class="gip-1"><?php echo wp_kses_post( $travelers_subtitle ); ?></p>
                                        <?php else : ?>
                                            <p class="gip-1">Реальные истории наших клиентов. Вдохновляйтесь и планируйте свою мечту!</p>
                                        <?php endif; ?>
                                        
                                        <div class="reviews-container-into caruael_t history2">
                                            <div class="carousel_m shadow_m history3">
                                                <div class="carousel-wrapper_m">
                                                    <div class="carousel-items_m caruael_tt history3">
                                                        <?php while ( $travelers_stories_query->have_posts() ) : $travelers_stories_query->the_post(); 
                                                            $route       = get_field( 'route' );
                                                            $link_text   = get_field( 'link_text' );
                                                            $story_image = get_field( 'story_image' );

                                                            $img_url = '';
                                                            if ( $story_image ) {
                                                                if ( is_array( $story_image ) && ! empty( $story_image['url'] ) ) {
                                                                    $img_url = $story_image['url'];
                                                                } elseif ( is_numeric( $story_image ) ) {
                                                                    $img_src = wp_get_attachment_image_src( $story_image, 'full' );
                                                                    if ( $img_src && ! empty( $img_src[0] ) ) {
                                                                        $img_url = $img_src[0];
                                                                    }
                                                                } elseif ( is_string( $story_image ) ) {
                                                                    $img_url = $story_image;
                                                                }
                                                            }
                                                            if ( ! $img_url && has_post_thumbnail() ) {
                                                                $img_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                                                            }

                                                            if ( $img_url ) :
                                                        ?>
                                                            <?php
                                                            $story_permalink = get_permalink( get_the_ID() );
                                                            $story_btn_label = $link_text ? $link_text : __( 'Хочу также', 'traveliz' );
                                                            ?>
                                                            <div class="carousel-block_m history4">
                                                                <a class="story-card-link" href="<?php echo esc_url( $story_permalink ); ?>">
                                                                    <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                                                                    <div class="absolut-rew">
                                                                        <p>
                                                                            <span class="anna"><?php echo wp_kses_post( get_the_title() ); ?></span>
                                                                            <?php if ( $route ) : ?>
                                                                                <span class="anna_route"><?php echo wp_kses_post( $route ); ?></span>
                                                                            <?php endif; ?>
                                                                        </p>
                                                                        <span class="story-card-btn"><?php echo wp_kses_post( $story_btn_label ); ?></span>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        <?php 
                                                            endif; // конец условия if ( $img_url )
                                                            endwhile; 
                                                        ?>
                                                        <?php wp_reset_postdata(); ?>
                                                    </div>
                                                </div>
                                                <div class="wrap-dots-wra">
                                                    <div class="carousel-button-left_m turist">
                                                        <a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a>
                                                    </div>
                                                  
                                                    <div class="carousel-button-right_m turist2">
                                                        <a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
<?php endif; ?>                     
                    <section class="plat-carta">
                        <div class="container-4">
                            <div class="into-plat-carta">
                                <?php if ( $garanty_title ) : ?>
                                    <h2><?php echo wp_kses_post( $garanty_title ); ?></h2>
                                    <?php endif; ?>

                                        <?php if ( $garanty_subtitle ) : ?>
                                            <p class="plat-carta-subtitle">
                                                <?php echo wp_kses_post( $garanty_subtitle ); ?>
                                            </p>
                                            <?php endif; ?>

                                                <div class="plat-carta-cards">
                                                    <?php if ( $garanty_card_1_title ) : ?>
                                                        <div class="plat-carta-card">
                                                            <div class="plat-carta-icon">
                                                                <img class="get_template1" src="<?php echo get_template_directory_uri(); ?>/img/ico/1.webp" alt="">
                                                            </div>
                                                            <?php if ( $garanty_card_1_title ) : ?>
                                                                <h3><?php echo wp_kses_post( $garanty_card_1_title ); ?></h3>
                                                                <?php endif; ?>
                                                                    <?php if ( $garanty_card_1_text ) : ?>
                                                                        <p>
                                                                            <?php echo wp_kses_post( $garanty_card_1_text ); ?>
                                                                        </p>
                                                                        <?php endif; ?>
                                                        </div>
                                                        <?php endif; ?>

                                                            <?php if ( $garanty_card_2_title  ) : ?>
                                                                <div class="plat-carta-card">
                                                                    <div class="plat-carta-icon">
                                                                        <img class="get_template2" src="<?php echo get_template_directory_uri(); ?>/img/ico/2.webp" alt="">
                                                                    </div>
                                                                    <?php if ( $garanty_card_2_title ) : ?>
                                                                        <h3><?php echo wp_kses_post( $garanty_card_2_title ); ?></h3>
                                                                        <?php endif; ?>
                                                                            <?php if ( $garanty_card_2_text ) : ?>
                                                                                <p>
                                                                                    <?php echo wp_kses_post( $garanty_card_2_text ); ?>
                                                                                </p>
                                                                                <?php endif; ?>
                                                                </div>
                                                                <?php endif; ?>

                                                                    <?php if ( $garanty_card_3_title || $garanty_card_3_text ) : ?>
                                                                        <div class="plat-carta-card">
                                                                            <div class="plat-carta-icon">
                                                                                <img class="get_template3" src="<?php echo get_template_directory_uri(); ?>/img/ico/3.webp" alt="">
                                                                            </div>
                                                                            <?php if ( $garanty_card_3_title ) : ?>
                                                                                <h3><?php echo wp_kses_post( $garanty_card_3_title ); ?></h3>
                                                                                <?php endif; ?>
                                                                                    <?php if ( $garanty_card_3_text ) : ?>
                                                                                        <p>
                                                                                            <?php echo wp_kses_post( $garanty_card_3_text ); ?>
                                                                                        </p>
                                                                                        <?php endif; ?>
                                                                        </div>
                                                                        <?php endif; ?>
                                                </div>

                                                <?php if ( $garanty_trust_text ) : ?>
                                                    <p class="plat-carta-trust">
                                                        <?php echo wp_kses_post( $garanty_trust_text ); ?>
                                                    </p>
                                                    <?php endif; ?>

                                                        <div class="plat-carta-payments">
                                                            <div class="plat-carta-payment-item">
                                                                <img class="visa" src="<?php echo get_template_directory_uri(); ?>/img/ico/visa.svg" alt="VISA">
                                                            </div>
                                                            <div class="plat-carta-payment-item">
                                                                <img class="master_card" src="<?php echo get_template_directory_uri(); ?>/img/ico/master_card.svg" alt="MasterCard">
                                                            </div>
                                                            <div class="plat-carta-payment-item">
                                                                <img class="paypal" src="<?php echo get_template_directory_uri(); ?>/img/ico/paypal.svg" alt="PayPal">
                                                            </div>
                                                            <div class="plat-carta-payment-item">
                                                                <img class="gpay" src="<?php echo get_template_directory_uri(); ?>/img/ico/gpay.svg" alt="GPay">
                                                            </div>
                                                            <div class="plat-carta-payment-item">
                                                                <img class="paypass" class="paypass" src="<?php echo get_template_directory_uri(); ?>/img/ico/paypass.svg" alt="PayPass" />
                                                            </div>
                                                            <div class="plat-carta-payment-item">
                                                                <img class="sracard" src="<?php echo get_template_directory_uri(); ?>/img/ico/Isracard2.png" alt="Isracard">
                                                            </div>
                                                        </div>
                            </div>
                        </div>
                    </section>

                       
                            <?php get_template_part('template-parts/route_slider_3d'); ?>


                          



  <?php get_template_part('template-parts/faq'); ?>

<?php

$ready_title = get_field('ready_title', 'option');
$ready_button_1_text = get_field('ready_button_1_text', 'option');
$ready_button_1_type = get_field('ready_button_1_type', 'option');
$ready_button_1_link = get_field('ready_button_1_link', 'option');
$ready_button_2_text = get_field('ready_button_2_text', 'option');
$ready_button_2_type = get_field('ready_button_2_type', 'option');
$ready_button_2_link = get_field('ready_button_2_link', 'option');

$ready_background_image = get_field('ready_background_image', 'option');
$ready_decor_image      = get_field('ready_decor_image', 'option');

$ready_background_url = '';
if (is_array($ready_background_image) && !empty($ready_background_image['url'])) {
    $ready_background_url = (string) $ready_background_image['url'];
} elseif (is_numeric($ready_background_image)) {
    $ready_background_url = (string) wp_get_attachment_image_url((int) $ready_background_image, 'full');
} elseif (is_string($ready_background_image) && $ready_background_image !== '') {
    $ready_background_url = $ready_background_image;
}

$ready_decor_url = '';
$ready_decor_alt = '';
if (is_array($ready_decor_image) && !empty($ready_decor_image['url'])) {
    $ready_decor_url = (string) $ready_decor_image['url'];
    if (!empty($ready_decor_image['alt'])) {
        $ready_decor_alt = (string) $ready_decor_image['alt'];
    }
} elseif (is_numeric($ready_decor_image)) {
    $ready_decor_url = (string) wp_get_attachment_image_url((int) $ready_decor_image, 'full');
    $ready_decor_alt = (string) get_post_meta((int) $ready_decor_image, '_wp_attachment_image_alt', true);
} elseif (is_string($ready_decor_image) && $ready_decor_image !== '') {
    $ready_decor_url = $ready_decor_image;
}

$into_ready_style = $ready_background_url
    ? "background-image: url('" . esc_url($ready_background_url) . "');"
    : 'background-image: none;';
?>

<?php if ($ready_title || $ready_button_1_text || $ready_button_2_text) : ?>
                                <section class="ready">
                                    <div class="container-4">
                                        <div
                                            class="into-ready"
                                            style="<?php echo esc_attr($into_ready_style); ?>"
                                        >

                                            <?php if ($ready_decor_url) : ?>
                                                <img class="elips11" src="<?php echo esc_url($ready_decor_url); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $ready_decor_alt ) ); ?>">
                                            <?php endif; ?>
                                            <?php if ($ready_title) : ?>
                                                <h2><?php echo wp_kses_post($ready_title); ?></h2>
                                            <?php else : ?>
                                                <h2>Готовы создать маршрут мечты по Швейцарии?</h2>
                                            <?php endif; ?>
                                            
                                            <div class="wrap-ready">
                                                <?php if ($ready_button_1_text) : ?>
                                                    <?php if ($ready_button_1_type === 'popup') : ?>
                                                        <button class="order-mr landing_link modal-trigger_wt" data-popup="button-1">
                                                            <span class="btn-text"><?php echo wp_kses_post($ready_button_1_text); ?></span>
                                                            <img src="<?php echo get_template_directory_uri(); ?>/img/plus-circle.svg" alt="">
                                                        </button>
                                                    <?php else : ?>
                                                        <a href="<?php echo esc_url($ready_button_1_link); ?>" class="order-mr landing_link">
                                                            <span class="btn-text"><?php echo wp_kses_post($ready_button_1_text); ?></span>
                                                            <img src="<?php echo get_template_directory_uri(); ?>/img/plus-circle.svg" alt="">
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <?php if ($ready_button_2_text) : ?>
                                                    <?php if ($ready_button_2_type === 'popup') : ?>
                                                        <button class="btn-action btn-blue landblue modal-trigger_wt" data-popup="button-2">
                                                            <span class="btn-text"><?php echo wp_kses_post($ready_button_2_text); ?></span>
                                                        </button>
                                                    <?php else : ?>
                                                        <a href="<?php echo esc_url($ready_button_2_link); ?>" class="btn-action btn-blue landblue">
                                                            <span class="btn-text"><?php echo wp_kses_post($ready_button_2_text); ?></span>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                </section>
<?php endif; ?>


                              
                                    <?php get_template_part('template-parts/how_it_works'); ?>
                                        <?php get_footer(); ?>