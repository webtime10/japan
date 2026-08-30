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
	$page_top_defaults = array(
		'title_1'     => 'יפן: חופשות בהתאמה אישית',
		'title_2'     => 'ערים, מסלולים וחוויות ביפן',
		'subtitle'    => 'מסלולי טיול אישיים, שנבנים על בסיס ניסיון אמיתי בשטח',
		'button_text' => 'לפרטים נוספים',
	);

	// Defaults for japan.loc hero; custom ACF values override when filled in.
	$h1_1     = trim( (string) $page_top_title_1 ) !== '' ? $page_top_title_1 : $page_top_defaults['title_1'];
	$h1_2     = trim( (string) $page_top_title_2 ) !== '' ? $page_top_title_2 : $page_top_defaults['title_2'];
	$sub      = trim( (string) $page_top_subtitle ) !== '' ? $page_top_subtitle : $page_top_defaults['subtitle'];
	$btn_t    = trim( (string) $page_top_button_text ) !== '' ? $page_top_button_text : $page_top_defaults['button_text'];
	$btn_type = $page_top_button_type ? $page_top_button_type : 'link';
	$btn_link = trim( (string) $page_top_button_link ) !== '' ? $page_top_button_link : '#';
?>
    <img
        class="elips35"
        src="<?php echo esc_url( $shadow_url ? $shadow_url : ( get_template_directory_uri() . '/img/Ellipse35.webp' ) ); ?>"
        alt=""
    >
    <h1>
        <?php echo wp_kses_post( $h1_1 ); ?>

    </h1>
    

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
            's_flexibol_what_we_offer'  => 'what_we_offer',
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
// Footer expert: fallback from options / shortcode template when flex block is not on the page.
if ( ! empty( $footer_expert_html ) ) {
	echo $footer_expert_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme partial HTML
} elseif ( ! $has_footer_expert ) {
	get_template_part( 'template-parts/web_expert' );
}
?>


</div>
<?php

get_footer();