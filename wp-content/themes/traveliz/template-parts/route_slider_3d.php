   <?php
        $title_route_slider_3d = get_field('title_route_slider_3d', 'option');
        $text_route_slider_3d = get_field('text_route_slider_3d', 'option');
        $digit_route_slider_3d = get_field('digit_route_slider_3d', 'option');
        $route_title_route_slider_3d = get_field('route_title_route_slider_3d', 'option');
        $button_route_slider_3d = get_field('button_route_slider_3d', 'option');
        $link_route_slider_3d = get_field('link_route_slider_3d', 'option');
        $route_slider_3d_background = get_field('route_slider_3d_background_image', 'option');
        $route_slider_3d_background_url = '';

        if ( is_array( $route_slider_3d_background ) && ! empty( $route_slider_3d_background['url'] ) ) {
            $route_slider_3d_background_url = $route_slider_3d_background['url'];
        } elseif ( is_numeric( $route_slider_3d_background ) ) {
            $route_slider_3d_background_url = wp_get_attachment_image_url( (int) $route_slider_3d_background, 'full' );
        } elseif ( is_string( $route_slider_3d_background ) && '' !== trim( $route_slider_3d_background ) ) {
            $route_slider_3d_background_url = $route_slider_3d_background;
        }

        $route_slider_3d_background_style = $route_slider_3d_background_url ? 'background-image: url(' . esc_url( $route_slider_3d_background_url ) . '); background-size: cover; background-repeat: no-repeat; background-position: center center;' : '';
        // Один раз собираем слайды из repeater Options — та же логика URL, что в desktop carousel_d3 ниже.
        $route_slider_3d_slide_urls = array();
        if ( function_exists( 'have_rows' ) && have_rows( 'slider_route_slider_3d', 'option' ) ) {
            while ( have_rows( 'slider_route_slider_3d', 'option' ) ) {
                the_row();
                $slide_image = get_sub_field( 'img_route_slider_3d' );
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
                if ( $slide_img_url !== '' ) {
                    $route_slider_3d_slide_urls[] = $slide_img_url;
                }
            }
        }
       ?>
  
<section class="route l" style="<?php echo esc_attr( $route_slider_3d_background_style ); ?>">
<div class="container-4">
        <?php if ( ! empty( $route_slider_3d_slide_urls ) ) : ?>
    <div class="into-route2">
        <?php $theme_img = esc_url( get_template_directory_uri() . '/img' ); ?>
        <div class="reviews-section route-into-route2-dup l">
            <div class="reviews-container-into caruael_t">
                <div class="carousel_m shadow_m">
                    <div class="carousel-wrapper_m">
                        <div class="carousel-items_m caruael_tt">
                            <?php foreach ( $route_slider_3d_slide_urls as $slide_img_url ) : ?>
                            <div class="carousel-block_m">
                                <div><img src="<?php echo esc_url( $slide_img_url ); ?>" alt=""></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="wrap-dots-wra">
                        <div class="carousel-button-left_m"><a href="#"><img width="53" height="53" src="<?php echo $theme_img; ?>/arrow-l.webp" alt=""></a></div>
                        <div class="carousel-button-right_m"><a href="#"><img width="53" height="53" src="<?php echo $theme_img; ?>/arrow-r.webp" alt=""></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
   <div class="into-route">
       <div class="route-left">
     
        <div class="wrap-rout">
             <h2><?php echo$title_route_slider_3d; ?></h2>
 <p><?php echo $text_route_slider_3d; ?></p>
            </div>
        <div class="wrap-rout-ptice">
            <div class="sostav">
                
            <span><?php echo $route_title_route_slider_3d; ?></span><span><?php echo $digit_route_slider_3d; ?></span>
            </div>  
           <a href="<?php echo $link_route_slider_3d; ?>" class="zakaz"><span><?php echo $button_route_slider_3d; ?></span>
<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.707 1C12.707 0.447715 12.2593 2.00008e-07 11.707 -5.28728e-08L2.70703 7.47917e-07C2.15475 4.10743e-07 1.70703 0.447716 1.70703 1C1.70703 1.55228 2.15475 2 2.70703 2L10.707 2L10.707 10C10.707 10.5523 11.1547 11 11.707 11C12.2593 11 12.707 10.5523 12.707 10L12.707 1ZM0.707031 12L1.41414 12.7071L12.4141 1.70711L11.707 1L10.9999 0.292894L-7.55191e-05 11.2929L0.707031 12Z" fill="#695729"/>
</svg></a>
        </div>
       </div>
       <div class="route-right">
   <div class="carousel_d3 shadow_d3"> 
      <div class="carousel-button-left_d3"><button>
        <img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt="" />
       

      </button></div> 
      <div class="carousel-button-right_d3"><button>
        <img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""> 
      </button></div> 
		<div class="carousel-wrapper_d3"> 
		   <div class="carousel-items_d3">
			
                        <?php foreach ( $route_slider_3d_slide_urls as $slide_img_url ) : ?>
                                    <div class="carousel-block_d3">
                                        <img src="<?php echo esc_url( $slide_img_url ); ?>" alt="">
                                    </div>
                        <?php endforeach; ?>
			 
		   </div>
		</div>
		<div class="carousel-dots_d3"></div>
   </div>

       
       </div>
   </div>

</div>
</section>
