<?php


get_header();
?>
<div class="site-main-2">
    <div class="container-4">
        <div class="into-site-main-2">

		<div class="main-block-22">	
<?php
	// PageTop (from ACF: inc/acf/page.php).
	$page_top_title_1   = get_field( 'page_top_title_1' );


	$page_top_title_2   = get_field( 'page_top_title_2' );
	$page_top_subtitle  = get_field( 'page_top_subtitle' );
	$shadow_under_letters = get_field( 'shadow_under_letters' );
	$page_top_button_text = get_field( 'page_top_button_text' );
	$page_top_button_type = get_field( 'page_top_button_type' );
	$page_top_button_link = get_field( 'page_top_button_link' );

	$shadow_url = is_array( $shadow_under_letters ) && ! empty( $shadow_under_letters['url'] ) ? $shadow_under_letters['url'] : '';
	// Fallback to keep current look if ACF fields are empty.
	$h1_1 = $page_top_title_1 ? $page_top_title_1 : '';	$h1_2 = $page_top_title_2 ? $page_top_title_2 : '';	$sub  = $page_top_subtitle ? $page_top_subtitle : '';	$btn_t = $page_top_button_text ? $page_top_button_text : '';	$btn_type = $page_top_button_type ? $page_top_button_type : 'link';
	$btn_link = $page_top_button_link ? $page_top_button_link : '#';
?>
    <img
        class="elips35"
        src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/Ellipse35.webp' ) ); ?>"
        alt=""
    >
    <h1>
        <span><?php echo wp_kses_post( $h1_1 ); ?></span>
        <span><?php echo wp_kses_post( $h1_2 ); ?></span>
    </h1>
    <div class="site-main-title-2">
        <strong><?php echo wp_kses_post( $sub ); ?></strong>
    </div>
<?php if ( $btn_type === 'popup' ) : ?>
	<button type="button" class="yl-region js-open-popup modal-trigger_wt" data-source="general_request">
		<span><?php echo wp_kses_post( $btn_t ); ?></span>
		<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
		</svg>
	</button>
<?php else : ?>
	<a class="yl-region" href="<?php echo esc_url( $btn_link ); ?>">
		<span><?php echo wp_kses_post( $btn_t ); ?></span>
		<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
		</svg>
	</a>
<?php endif; ?>
</div>


</div>
    </div>
    </div>
</div>
    <?php
     
     $has_seasons_line = false;
     $has_our_experience = false;
     $has_attractions_slider = false;
     $has_route_one_day = false;
     $has_price_table = false;
     $has_advice = false;
     $has_expert = false;
     $has_active_otd = false;
     $has_where_to_stay = false;
     $has_parking = false;
     $has_city_slider = false;
     $has_footer_expert = false;
     $footer_expert_html = '';
    if ( function_exists( 'have_rows' ) && have_rows( 's_flexibol_constructor' ) ) :
        // Map ACF flexible layout names -> template files in flexibol/
        $flexibol_map = array(
            
            's_flexibol_editor'        => 'editor',
            's_flexibol_short_editor'  => 'short_editor',
            's_flexibol_video_reviews' => 'video',
            's_flexibol_tourist_reviews' => 'tourist_reviews_flex',
            's_flexibol_seasons_line'  => 'seasons_line',
            's_flexibol_regions_comparison'  => 'regions_comparison',
            's_flexibol_faq'           => 'faq',
            's_flexibol_country_text'  => 'text_cuntry', // file has typo: text_cuntry.php
            's_flexibol_map'           => 'map',
            's_flexibol_custom_reviews' => 'custom_reviews',
            's_flexibol_what_you_will_get' => 'what_you_will_get',
            's_flexibol_how_it_works'   => 'how_it_works',
            's_flexibol_route_example'  => 'route_example',
            's_flexibol_custom_regions' => 'custom_regions',
            's_flexibol_custom_city_slider' => 'custom_city_slider',
            's_flexibol_attractions_slider' => 'attractions_slider',
            's_flexibol_route_one_day' => 'route_one_day',
            's_flexibol_price_table'   => 'price_table',
            's_flexibol_advice'        => 'advice',
            's_flexibol_expert'        => 'expert',
            's_flexibol_active_otd'    => 'active_otd',
            's_flexibol_where_to_stay' => 'where_to_stay',
            's_flexibol_parking'       => 'parking',
            's_flexibol_footer_expert' => 'footer_expert',
        );

        while ( have_rows( 's_flexibol_constructor' ) ) : the_row();
            $layout = get_row_layout();
            if ( empty( $layout ) || empty( $flexibol_map[ $layout ] ) ) {
                continue;
            }
            if ( $layout === 's_flexibol_seasons_line' ) {
                // If the layout exists in ACF, hide the legacy hardcoded block.
                $has_seasons_line = true;
            }
            if ( $layout === 's_flexibol_attractions_slider' ) {
                $has_attractions_slider = true;
            }
            if ( $layout === 's_flexibol_route_one_day' ) {
                $has_route_one_day = true;
            }
            if ( $layout === 's_flexibol_price_table' ) {
                $has_price_table = true;
            }
            if ( $layout === 's_flexibol_advice' ) {
                $has_advice = true;
            }
            if ( $layout === 's_flexibol_expert' ) {
                $has_expert = true;
            }
            if ( $layout === 's_flexibol_active_otd' ) {
                $has_active_otd = true;
            }
            if ( $layout === 's_flexibol_where_to_stay' ) {
                $has_where_to_stay = true;
            }
            if ( $layout === 's_flexibol_parking' ) {
                $has_parking = true;
            }
            if ( $layout === 's_flexibol_footer_expert' ) {
                $has_footer_expert = true;
              
            }

            // Каждый layout — свой .flexibol-block (как у остальных страниц), чтобы кривой HTML одного блока не ломал соседние.
           echo '<div class="flexibol-block">';
            get_template_part( 'flexibol/' . $flexibol_map[ $layout ] );
               echo '</div>'; 
       
        endwhile;
    endif;

    ?>



















<div class="comment-wrap">
<div class="container-4">    
     <div class="into-comment">
        <h2><?php echo get_theme_translation('comments_title'); ?></h2>
    </div>
</div>
</div>

<?php
if ( have_posts() ) : 
    while ( have_posts() ) : the_post(); ?>

    <div class="container-4 comment-t">
        <?php comments_template(); ?>
    </div>

<?php 
    endwhile; 
endif; 
?>

<?php
// Footer expert: вывод внизу страницы (после комментариев), порядок в flexible сохраняется при нескольких блоках.
if ( ! empty( $footer_expert_html ) ) {
	echo $footer_expert_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme partial HTML
} elseif ( ! $has_footer_expert ) {
	?>
<section class="web-expert">

       <div class='container-4'>
          <div class='web-expert-into'>
             <img src="<?php echo get_template_directory_uri(); ?>/img/Ellipse531.webp" alt="">
             <div class="the_web">
                <h3>Theresa Webb</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
             </div>
         </div>
       </div>

</section>
	<?php
}
?>


</div>
<?php

get_footer();