<?php
/* Template Name: about us */

get_header();
?>
</div>
<?php
$header_background = get_field( 'header_background' );
$h1_r = get_field( 'h1_r' );
$text_under_h1_r = get_field( 'text_under_h1_r' );
$text_and_logo_r = get_field( 'text_and_logo_r' );
$title_block2 = get_field( 'title_block2' );
$text_under_title_block2_r = get_field( 'text_under_title_block2_r' );
$text_bottom_block2_r = get_field( 'text_bottom_block2_r' );
$title_r_block2 = get_field( 'title_r_block2' );

$yellow_button_r = get_field( 'yellow_button_r' );
$yellow_button_switcher_r = '';
$yellow_button_text_r = '';
$yellow_button_link_r = '';

if ( $yellow_button_r ) {
    $yellow_button_switcher_r = $yellow_button_r['yellow_button_switcher_r'] ?? '';
    $yellow_button_text_r = $yellow_button_r['yellow_button_text_r'] ?? '';
    $yellow_button_link_r = $yellow_button_r['yellow_button_link_r'] ?? '#';
}

$header_bg_url = '';
if ( $header_background ) {
    if ( is_array( $header_background ) && ! empty( $header_background['url'] ) ) {
        $header_bg_url = $header_background['url'];
    } elseif ( is_numeric( $header_background ) ) {
        $img_src = wp_get_attachment_image_src( $header_background, 'full' );
        if ( $img_src && ! empty( $img_src[0] ) ) {
            $header_bg_url = $img_src[0];
        }
    } elseif ( is_string( $header_background ) ) {
        $header_bg_url = $header_background;
    }
}

// Map-b block fields
$title_expert = get_field( 'title_expert' );
$text_expert = get_field( 'text_expert' );
$img_expert = get_field( 'img_expert' );
$year10_text = get_field( 'year10_text' );
$year10_digit = get_field( 'year10_digit' );
$countries_text = get_field( 'countries_text' );
$countries_digit = get_field( 'countries_digit' );
$rout_7000_text = get_field( 'rout_7000_text' );
$rout_7000_digit = get_field( 'rout_7000_digit' );

$img_expert_url = '';
if ( $img_expert ) {
    if ( is_array( $img_expert ) && ! empty( $img_expert['url'] ) ) {
        $img_expert_url = $img_expert['url'];
    } elseif ( is_numeric( $img_expert ) ) {
        $img_src = wp_get_attachment_image_src( $img_expert, 'full' );
        if ( $img_src && ! empty( $img_src[0] ) ) {
            $img_expert_url = $img_src[0];
        }
    } elseif ( is_string( $img_expert ) ) {
        $img_expert_url = $img_expert;
    }
}

// Five-video-ob block fields
$title_video_message = get_field( 'title_video_message' );
$text_video_message = get_field( 'text_video_message' );
$img_video_v = get_field( 'img_video_v' );
$img_elips_video_ob = get_field( 'img_elips_video_ob' );
$video_url_v = get_field( 'video_url_v' );

$yellow_button_v = get_field( 'yellow_button_v' );
$yellow_button_switcher_v = '';
$yellow_button_text_v = '';
$yellow_button_link_v = '';

if ( $yellow_button_v ) {
    $yellow_button_switcher_v = $yellow_button_v['yellow_button_switcher_v'] ?? '';
    $yellow_button_text_v = $yellow_button_v['yellow_button_text_v'] ?? '';
    $yellow_button_link_v = $yellow_button_v['yellow_button_link_v'] ?? '#';
}

$img_video_v_url = '';
if ( $img_video_v ) {
    if ( is_array( $img_video_v ) && ! empty( $img_video_v['url'] ) ) {
        $img_video_v_url = $img_video_v['url'];
    } elseif ( is_numeric( $img_video_v ) ) {
        $img_src = wp_get_attachment_image_src( $img_video_v, 'full' );
        if ( $img_src && ! empty( $img_src[0] ) ) {
            $img_video_v_url = $img_src[0];
        }
    } elseif ( is_string( $img_video_v ) ) {
        $img_video_v_url = $img_video_v;
    }
}

$img_elips_video_ob_url = '';
if ( $img_elips_video_ob ) {
    if ( is_array( $img_elips_video_ob ) && ! empty( $img_elips_video_ob['url'] ) ) {
        $img_elips_video_ob_url = $img_elips_video_ob['url'];
    } elseif ( is_numeric( $img_elips_video_ob ) ) {
        $img_src = wp_get_attachment_image_src( $img_elips_video_ob, 'full' );
        if ( $img_src && ! empty( $img_src[0] ) ) {
            $img_elips_video_ob_url = $img_src[0];
        }
    } elseif ( is_string( $img_elips_video_ob ) ) {
        $img_elips_video_ob_url = $img_elips_video_ob;
    }
}

?>
<div  class="site-main-reviews-block-1">
    <div class="container-3">
        <div class="into-reviews-block-1" <?php if ( $header_bg_url ) : ?>style="background-image: url('<?php echo esc_url( $header_bg_url ); ?>');"<?php endif; ?>>
<?php if ( $h1_r ) : ?>
<h1><?php echo esc_html( $h1_r ); ?></h1>
<?php endif; ?>
<?php if ( $text_under_h1_r ) : ?>
<p><?php echo esc_html( $text_under_h1_r ); ?></p>
<?php endif; ?>
<?php if ( $yellow_button_text_r ) : ?>
    <?php if ( $yellow_button_switcher_r !== 'link' ) : ?>
        <button type="button" data-source="general_request" class="order-mr js-open-popup modal-trigger_wt">
            <span><?php echo esc_html( $yellow_button_text_r ); ?></span>
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"></path>
            </svg>
        </button>
    <?php else : ?>
        <a class="order-mr" href="<?php echo esc_url( $yellow_button_link_r ); ?>">
            <span><?php echo esc_html( $yellow_button_text_r ); ?></span>
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"></path>
            </svg>
        </a>
    <?php endif; ?>
<?php endif; ?>
<div class="inter-text">

    <img src="<?php echo esc_url( traveliz_get_lang_logo2_url() ); ?>" alt="<?php bloginfo( 'name' ); ?>">
    <?php if ( $text_and_logo_r ) : ?>
    <p>
        <?php echo wp_kses_post( $text_and_logo_r ); ?>
    </p>
    <?php endif; ?>
</div>
</div>
</div>
</div>
<div class='reviews-oleksandr'>
   <div class='container-3'>
    <?php if ( $title_block2 ) : ?>
    <h2><?php echo esc_html( $title_block2 ); ?></h2>
    <?php endif; ?>
      <div class='reviews-oleksandr-into'>
             <?php if ( $text_under_title_block2_r ) : ?>
             <div class="oleksandr-text">
                 <?php echo esc_html( $text_under_title_block2_r ); ?>
             </div>
             
             <?php endif; ?>
      <div class="oleksandr-wrap">
             <?php if ( function_exists( 'have_rows' ) && have_rows( 'block_2_foto_and_text_r' ) ) : ?>
                 <?php while ( have_rows( 'block_2_foto_and_text_r' ) ) : the_row(); ?>
                     <?php
                     $img_r_block2 = get_sub_field( 'img_r_block2' );
                     $title_r_block2 = get_sub_field( 'title_r_block2' );
                     $text_r_block2 = get_sub_field( 'text_r_block2' );

                     $img_r_block2_url = '';
                     if ( $img_r_block2 ) {
                         if ( is_array( $img_r_block2 ) && ! empty( $img_r_block2['url'] ) ) {
                             $img_r_block2_url = $img_r_block2['url'];
                         } elseif ( is_numeric( $img_r_block2 ) ) {
                             $img_src = wp_get_attachment_image_src( $img_r_block2, 'full' );
                             if ( $img_src && ! empty( $img_src[0] ) ) {
                                 $img_r_block2_url = $img_src[0];
                             }
                         } elseif ( is_string( $img_r_block2 ) ) {
                             $img_r_block2_url = $img_r_block2;
                         }
                     }
                     ?>
                     <div class="oleksandr-card">
                         <?php if ( $img_r_block2_url ) : ?>
                             <div class="oleksandr-card-image">
                                 <img src="<?php echo esc_url( $img_r_block2_url ); ?>" alt="<?php echo esc_attr( $title_r_block2 ); ?>">
                             </div>
                         <?php endif; ?>
                         <div class="oleksandr-card-content">
                             <?php if ( $title_r_block2 ) : ?>
                                 <h3 class="oleksandr-card-title"><?php echo esc_html( $title_r_block2); ?></h3>
                             <?php endif; ?>
                             <?php if ( $text_r_block2 ) : ?>
                                 <p class="oleksandr-card-text"><?php echo esc_html( $text_r_block2 ); ?></p>
                             <?php endif; ?>
                         </div>
                     </div>
                 <?php endwhile; ?>
             <?php endif; ?>
             <?php if ( $text_bottom_block2_r ) : ?>
        <div class="oleksandr-dop-text">
            <?php echo wp_kses_post( $text_bottom_block2_r ); ?>
        </div>
     <?php endif; ?>
     </div>
     <div class="oleksandr-nav" aria-label="Oleksandr cards navigation">
     <button type="button" class="oleksandr-nav-btn oleksandr-nav-btn--next" aria-label="Next slide"><img src="<?php echo get_template_directory_uri(); ?>/img/right-1.webp" alt=""></button>
        <button type="button" class="oleksandr-nav-btn oleksandr-nav-btn--prev" aria-label="Previous slide"><img src="<?php echo get_template_directory_uri(); ?>/img/left-1.webp" alt=""> </button>
       
     </div>
     <div class="oleksandr-dop-text2">

     </div>
   </div>
   
</div>
</div>
  
<?php get_template_part('template-parts/reviews'); ?>
<div class="map-b ee">
    <div class="container-4">
        <?php if ( $title_expert ) : ?>
        <h2 ><?php echo esc_html( $title_expert ); ?></h2>
        <?php endif; ?>
        <?php if ( $text_expert ) : ?>
        <p><?php echo esc_html( $text_expert ); ?></p>
        <?php endif; ?>
        <div class="into-map-b">
            <?php if ( $img_expert_url ) : ?>
            <img src="<?php echo esc_url( $img_expert_url ); ?>" alt="<?php echo esc_attr( $title_expert ); ?>"/>
            <?php endif; ?>
            
            <div class="work-map">
                <div class="work-map-card">
                    <div class="work-map-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/image1855.webp" alt="<?php echo esc_attr( $year10_text ); ?>">
                    </div>
                    <div class="work-map-content">
                        <?php if ( $year10_digit ) : ?>
                        <span class="work-map-number"><?php echo esc_html( $year10_digit ); ?></span>
                        <?php endif; ?>
                        <?php if ( $year10_text ) : ?>
                        <span class="work-map-text"><?php echo esc_html( $year10_text ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="work-map-card">
                    <div class="work-map-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/image1856.webp" alt="<?php echo esc_attr( $countries_text ); ?>">
                    </div>
                    <div class="work-map-content">
                        <?php if ( $countries_digit ) : ?>
                        <span class="work-map-number"><?php echo esc_html( $countries_digit ); ?></span>
                        <?php endif; ?>
                        <?php if ( $countries_text ) : ?>
                        <span class="work-map-text"><?php echo esc_html( $countries_text ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="work-map-card">
                    <div class="work-map-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/img/image1857.webp" alt="<?php echo esc_attr( $rout_7000_text ); ?>">
                    </div>
                    <div class="work-map-content">
                        <?php if ( $rout_7000_digit ) : ?>
                        <span class="work-map-number"><?php echo esc_html( $rout_7000_digit ); ?></span>
                        <?php endif; ?>
                        <?php if ( $rout_7000_text ) : ?>
                        <span class="work-map-text"><?php echo esc_html( $rout_7000_text ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='five-video-ob'>
   <div class='container-4'>
    <?php if ( $title_video_message ) : ?>
    <h2><?php echo esc_html( $title_video_message ); ?></h2>
    <?php endif; ?>
      <div class='five-into-video-ob'>
        <?php if ( $img_video_v_url ) : ?>
        <img src="<?php echo esc_url( $img_video_v_url ); ?>" alt="<?php echo esc_attr( $title_video_message ); ?>">
        <?php endif; ?>
        <div class="video-button-ot modal-trigger_vt">
            <svg width="77" height="87" viewBox="0 0 77 87" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M73.5 38.0519C77.5 40.3613 77.5 46.1348 73.5 48.4442L9 85.6833C5 87.9927 -4.23379e-06 85.1059 -4.0319e-06 80.4871L-7.76354e-07 6.00896C-5.7446e-07 1.39016 5 -1.4966 9 0.812798L73.5 38.0519Z" fill="white"/>
            </svg>
        </div>
     </div>
     <?php if ( $text_video_message ) : ?>
     <div class="obr-text">
        <?php echo esc_html( $text_video_message ); ?>
     </div>
     <?php endif; ?>
     <?php if ( $yellow_button_text_v ) : ?>
        <?php if ( $yellow_button_switcher_v !== 'link' ) : ?>
            <button type="button" data-source="general_request" class="btn-video js-open-popup modal-trigger_wt">
                <span><?php echo esc_html( $yellow_button_text_v ); ?></span>
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
                </svg>
            </button>
        <?php else : ?>
            <a class="btn-video" href="<?php echo esc_url( $yellow_button_link_v ); ?>">
                <span><?php echo esc_html( $yellow_button_text_v ); ?></span>
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
                </svg>
            </a>
        <?php endif; ?>
     <?php endif; ?>
   
    <img class="elips" src="<?php echo get_template_directory_uri(); ?>/img/Ellipse61.webp" alt=""/>
   

   </div>
</div>
<?php get_template_part( 'template-parts/what_you_will_get', null, array( 'omit_buttons' => true ) ); ?>
<?php get_template_part('template-parts/how_it_works'); ?>





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





    <?php if ( $video_url_v ) : ?>
    <div class="overlay_vt"></div>
    <div class="modal_vt" id="contact-modal_vt">
        <div class="modal-content_vt">
            <iframe
                id="video-iframe"
                width="770"
                height="500"
                src=""
                data-src="<?php echo esc_url( $video_url_v ); ?>"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                loading="lazy"
                style="width: 100%; height: auto; aspect-ratio: 16 / 9; display: block;"
                allowfullscreen>
            </iframe>
        </div>

        <button class="modal-close_vt" aria-label="Закрыть"></button>
    </div>
    <?php endif; ?>
<?php get_footer(); ?>







