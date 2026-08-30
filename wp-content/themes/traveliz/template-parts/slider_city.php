<?php 
// Основной заголовок секции (если он всё еще title_11 в опциях)
$title_main = get_field('title_22', 'option'); 
?>
<?php if($title_main):?>
<section class="region region2">
    <div class="container-5">
        <?php if ( $title_main ) : ?>
            <h2><?php echo wp_kses_post( $title_main ); ?></h2>
        <?php endif; ?>

        <div class="into-region">
            <div class="carousel shadow"> 
                <div class="carousel-wrapper">
                    <div class="carousel-items">
                        <?php 
                        // Проверяем наличие рядов в новом повторителе 'slider_2'
                        if ( have_rows( 'slider_2', 'option' ) ) : 
                            while ( have_rows( 'slider_2', 'option' ) ) : the_row(); 
                                
                                // Получаем вложенные поля по новым именам со скриншота
                                $s_img   = get_sub_field( 'img_2' );
                                $s_title = get_sub_field( 'title_2' );
                                $s_text  = get_sub_field( 'text_2' );

                                // Обработка URL картинки
                                $s_img_url = '';
                                if ( is_array( $s_img ) ) {
                                    $s_img_url = $s_img['url'];
                                } elseif ( is_numeric( $s_img ) ) {
                                    $s_img_url = wp_get_attachment_image_url( $s_img, 'full' );
                                } else {
                                    $s_img_url = $s_img;
                                }

                                // Заглушка, если пусто
                                if ( ! $s_img_url ) {
                                    $s_img_url = get_template_directory_uri() . '/img/no.webp';
                                }
                                ?>
                                
                                <div class="carousel-block">
                                    <div class="wrap-carusel-block-into">
                                        <img src="<?php echo esc_url( $s_img_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $s_title ) ); ?>">
                                        
                                        <?php if ( $s_title || $s_text ) : ?>
                                            <div class="carusel-wrap-t">
                                                <?php if ( $s_title ) : ?>
                                                    <div class="title-carusel"><?php echo wp_kses_post( $s_title ); ?></div>
                                                <?php endif; ?>
                                                
                                                <?php if ( $s_text ) : ?>
                                                    <div class="text-carusel2"><?php echo wp_kses_post( $s_text ); ?></div>
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