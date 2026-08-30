<?php 
// Получаем заголовок и настройки кнопки из Опций
$slider_atr = get_field('title_atraction', 'option'); 
$button_slider_link = get_field('button_link_atraction', 'option'); 
$button_slider_text = get_field('button_atraction', 'option'); 
?>

<?php if($slider_atr): ?>
<section class="landmark">
    <div class="container-4">
        <h2><?php echo wp_kses_post($slider_atr); ?></h2>

        <div class="landpark-into">
            <div class="carousel_m shadow_m"> 
                <div class="carousel-wrapper_m"> 
                    <div class="carousel-items_m caruael_tt"> 
                        
                        <?php 
                
                        if ( have_rows( 'slider_atraction', 'option' ) ) : 
                            while ( have_rows( 'slider_atraction', 'option' ) ) : the_row(); 
                                
                                $a_title = get_sub_field( 'title_attractions' );
                                $a_text  = get_sub_field( 'text_attractions' );
                                $a_img   = get_sub_field( 'img_attractions' );

                                // Логика картинки
                                $a_img_url = '';
                                if ( is_array( $a_img ) ) {
                                    $a_img_url = $a_img['url'];
                                } elseif ( is_numeric( $a_img ) ) {
                                    $a_img_url = wp_get_attachment_image_url( $a_img, 'full' );
                                } else {
                                    $a_img_url = $a_img; 
                                }

                                if ( ! $a_img_url ) {
                                    $a_img_url = get_template_directory_uri() . '/img/no.webp';
                                }
                                ?>

                                <div class="carousel-block_m">
                                    <div>

                                        <a class="attractions-r" href="<?php echo esc_url($button_slider_link); ?>">
                                           
                                        <img class="forma11" src="<?php echo get_template_directory_uri(); ?>/img/forma1.webp" alt="">
                                        <img class="attractions-img" src="<?php echo esc_url( $a_img_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $a_title ) ); ?>" />
                                        </a>
                                        <div>
                                            <?php if ( $a_title ) : ?>
                                                <h3><?php echo wp_kses_post( $a_title ); ?></h3>
                                            <?php endif; ?>

                                            <?php if ( $a_text ) : ?>
                                                <div class="attractions-card-text"><?php
													$a_text_out = (string) $a_text;
													if ( false === strpos( $a_text_out, '<' ) ) {
														$a_text_out = wpautop( $a_text_out );
													}
													echo wp_kses_post( $a_text_out );
												?></div>
                                            <?php endif; ?>

                                            <?php if ( $button_slider_text ) : ?>
                                                <a class="button-slider-text" href="<?php echo esc_url($button_slider_link); ?>">
                                                    <span><?php echo wp_kses_post($button_slider_text); ?></span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else : ?>
                            <p>Слайды не найдены в базе данных (проверьте ID поля slider_atraction).</p>
                        <?php endif; ?>
                        
                    </div>
                </div>

                <div class="wrap-dots-wra">
                    <div class="carousel-button-left_m">
                        <a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-l.webp" alt=""></a>
                    </div> 
                    <div class="carousel-button-right_m">
                        <a href="#"><img width="53" height="53" src="<?php echo get_template_directory_uri(); ?>/img/arrow-r.webp" alt=""></a>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>