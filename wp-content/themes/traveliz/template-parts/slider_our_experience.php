<?php 
$title_11 = get_field('title_11', 'option'); 
$field_name = 'slider_11';
$source = 'option'; // Источник данных
?>
<?php if($title_11):?>
<section class="region l">
    <div class="container-5">
        <?php if ($title_11): ?>
            <h2><?php echo esc_html($title_11); ?></h2>
        <?php endif; ?>

        <div class="into-region">
            <div class="carousel shadow"> 
                <div class="carousel-wrapper">
                    <div class="carousel-items">
                        <?php if ( have_rows( $field_name, $source ) ) : ?>
                            <?php while ( have_rows( $field_name, $source ) ) : the_row(); 
                                $slide_image = get_sub_field( 'img_1' );
                                $slide_title = get_sub_field( 'title_1' );
                                $slide_text  = get_sub_field( 'text_1' );
                                
                                // Логика обработки картинки (можно оставить вашу или упростить)
                                $slide_img_url = is_array($slide_image) ? $slide_image['url'] : $slide_image;
                                if ( ! $slide_img_url ) {
                                    $slide_img_url = get_template_directory_uri() . '/img/no.webp';
                                }
                            ?>
                                <div class="carousel-block">
                                    <div class="wrap-carusel-block-into">
                                        <div class="experience-img">
                                            
                                           
                                            <img src="<?php echo esc_url( $slide_img_url ); ?>" alt="<?php echo esc_attr( $slide_title ); ?>" />
                                        </div>
                                        <?php if ( $slide_title || $slide_text ) : ?>
                                            <div class="carusel-wrap-t">
                                                <?php if ( $slide_title ) : ?>
                                                    <div class="title-carusel"><?php echo esc_html( $slide_title ); ?></div>
                                                <?php endif; ?>
                                                <?php if ( $slide_text ) : ?>
                                                    <div class="text-carusel2"><?php echo esc_html( $slide_text ); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
			<div class="wrap-str">
					<div class="carousel-button-left"><a href="javascript:void(0)"></a></div> 
					<div class="carousel-button-right"><a href="javascript:void(0)"></a></div> 
            </div>
        </div>

      
    </div>
</section>
<?php endif; ?> 