<?php
/**
 * Template part for "Swiss Experience" block
 */

// Get fields from ACF options page
$title_block2_home = get_field('title_block2_home', 'option');
$title_h3_home     = get_field('title_h3_home', 'option');
$text_h3_block2_home = get_field('text_h3_block2_home', 'option');
$forma112 = get_field('forma112', 'option');

$img_1_text_home  = get_field('img_1_text_home', 'option');
$img_1_digit_home = get_field('img_1_digit_home', 'option');

$img_2_text_home  = get_field('img_2_text_home', 'option');
$img_2_digit_home = get_field('img_2_digit_home', 'option');

// Video field
$video_swiss = get_field('video_swiss', 'option');
$video_embed_url = function_exists( 'traveliz_youtube_embed_url' )
	? traveliz_youtube_embed_url( (string) $video_swiss )
	: '';$video_id = function_exists( 'traveliz_youtube_video_id_from_url' )
	? traveliz_youtube_video_id_from_url( (string) $video_swiss )
	: '';$mini_img_ytub = get_field('mini_img_ytub', 'option');
$text_under_the_video = get_field('text_under_the_video', 'option');

$forma112_url = get_template_directory_uri() . '/img/deva.webp';
$forma112_alt = '';
if ( is_array( $forma112 ) && ! empty( $forma112['url'] ) ) {
    $forma112_url = $forma112['url'];
    $forma112_alt = $forma112['alt'] ?? '';
}

$mini_img_ytub_url = get_template_directory_uri() . '/img/zamok.webp';
$mini_img_ytub_alt = '';
if ( is_array( $mini_img_ytub ) && ! empty( $mini_img_ytub['url'] ) ) {
    $mini_img_ytub_url = $mini_img_ytub['url'];
    $mini_img_ytub_alt = $mini_img_ytub['alt'] ?? '';
}
?>

<?php if($title_block2_home):?>
<section class="swiss-experience">
   <div class="container-4">
   <h2><?php echo wp_kses_post($title_block2_home); ?></h2>
            <div class="into-swiss">
                 <div class="left-into-swiss">
                    <div class="wrap-left-into-swiss">
                        <div class="attractions-r2">
                            <img class="forma111" src="<?php echo esc_url( get_template_directory_uri() . '/img/forma1.webp' ); ?>" alt="">
                            <img class="forma112"  src="<?php echo esc_url( $forma112_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $forma112_alt ) ); ?>"/> 
                        </div>
                        <div class="wrap-button-swiss">      
                            <button class="button-swiss modal-trigger_vt modal-trigger_vt--swiss" <?php if ( $video_id ) : ?>data-video-id="<?php echo esc_attr( $video_id ); ?>"<?php endif; ?>>
                                <img class="zamok-webp" src="<?php echo esc_url( $mini_img_ytub_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $mini_img_ytub_alt ) ); ?>" />
                                <svg width="41" height="47" viewBox="0 0 41 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M39.7461 21.1878C41.352 22.115 41.352 24.4329 39.7461 25.36L3.61297 46.2215C2.00709 47.1487 -0.000276915 45.9897 -0.000275834 44.1354L-0.00027501 2.41244C-0.000274929 0.558123 2.00709 -0.60083 3.61298 0.32633L39.7461 21.1878Z" fill="white"/>
                                </svg>
                            </button>
                           <div class="see-video ltr-1">
                             <svg width="400" height="400" viewBox="0 0 400 400">
                                <defs>
                                    <!-- Semicircle path around the bottom of the circle -->
<path id="text-curve-swiss" d="M 70,345 A 130,130 0 0,0 330,345" fill="none" />
                                </defs>
                               <text transform="translate(-4,-238)" direction="ltr">
    <textPath href="#text-curve-swiss" startOffset="50%" text-anchor="middle">
        Watch the expert’s video
    </textPath>
</text>
                             </svg>
                            </div>
                     <div class="see-video rtl-1">
  <svg width="400" height="400" viewBox="0 0 400 400">
    <defs>
      <path id="text-curve-swiss-rtl"
            d="M 350,320 A 150,150 0 0,0 50,320"
            fill="none" />
    </defs>

    <text direction="rtl" unicode-bidi="bidi-override">
      <textPath href="#text-curve-swiss-rtl" startOffset="50%" text-anchor="middle">
      <?php echo $text_under_the_video ? wp_kses_post($text_under_the_video) : ''; ?> 
      </textPath>
    </text>
  </svg>
</div>
                        
                        </div>   
                    </div>
                 </div>
                 <div class="right-into-swiss">
                    <div class="right-into-sw">
                        <h3><?php echo wp_kses_post($title_h3_home); ?></h3>
                         <p><?php echo wp_kses_post($text_h3_block2_home); ?></p>
                    </div>

                    
                      <div class="into-1-bas-swiss">
                                <div class="bas-swiss">
                                    <img class="ltr-1" width="68px" height="48px" src="<?php echo get_template_directory_uri(); ?>/img/vecteezy_train-png-with-ai-generated_26773229.webp" alt="" />
                                    <img style="display:none;" class="rtl-1" width="68px" height="48px" src="<?php echo get_template_directory_uri(); ?>/img/vecteezy_tra.webp" alt=""/>
                                    <div>
                                        <p><?php echo wp_kses_post($img_1_digit_home); ?></p>
                                       <span><?php echo wp_kses_post($img_1_text_home); ?></span>
                                    </div>
                                </div>
                                <div class="flag-swiss">
                                    <img width="111px" height="62" src="<?php echo get_template_directory_uri(); ?>/img/vecteezy_switzerland.webp" alt="" />
                                    <div>
                                        <p><?php echo wp_kses_post($img_2_digit_home); ?></p>
                                        <span><?php echo wp_kses_post($img_2_text_home); ?></span> 
                                    </div>
                                </div>
                       </div> 
                       <div class="into-2-bas-swiss">
                            
                        <div class="carousel_m shadow_m"> 
                       
                        <div class="carousel-wrapper_m"> 
    <div class="carousel-items_m">
        <?php if ( function_exists( 'have_rows' ) && have_rows( 'slider_img_block2_home', 'option' ) ) : ?>
            <?php while ( have_rows( 'slider_img_block2_home', 'option' ) ) : the_row(); ?>
                <?php
                $slide_image = get_sub_field( 'img_block2_slider' );
                $slide_title = get_sub_field( 'h3_block2_slider' );
                $slide_text  = get_sub_field( 'text_block2_slider' );

                $slide_img_url = '';
                if ( $slide_image ) {
                    if ( is_array( $slide_image ) && ! empty( $slide_image['url'] ) ) {
                        $slide_img_url = $slide_image['url'];
                    } elseif ( is_numeric( $slide_image ) ) {
                        $img_src = wp_get_attachment_image_src( $slide_image, 'full' );
                        if ( $img_src && ! empty( $img_src[0] ) ) {
                            $slide_img_url = $img_src[0];
                        }
                    } elseif ( is_string( $slide_image ) ) {
                        $slide_img_url = $slide_image;
                    }
                }
                ?>
                <div class="carousel-block_m">
                    <?php if ( $slide_img_url ) : ?>
                        <img src="<?php echo esc_url( $slide_img_url ); ?>" alt="">
                    <?php endif; ?>
                    <div>
                        <h3><?php echo $slide_title; ?></h3>
                        <?php if ( $slide_text ) : ?>
                            <span class="span-te"><?php echo wp_kses_post( $slide_text ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
                            <div class="wrap-dots-wra">
                                <div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a></div> 
                                <div class="carousel-dots_m"></div>
                                <div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a></div> 
                            </div>
                        </div>

                       
                       </div>
                </div>    
               
            </div>
    </div>
</section>
<?php endif; ?>

<?php if ( $video_embed_url !== '' ) : ?>
<div class="overlay_vt swiss-video-overlay_vt" id="swiss-video-overlay_vt"></div>
<div class="modal_vt" id="swiss-video-modal_vt">
    <div class="modal-content_vt">
        <iframe class="swiss-video-iframe" width="1280" height="720" src="about:blank" data-src="<?php echo esc_url( $video_embed_url . '?rel=0&modestbranding=1&playsinline=1&enablejsapi=1' ); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </div>
    <button class="modal-close_vt" aria-label="Закрыть"></button>
</div>
<?php endif; ?>

