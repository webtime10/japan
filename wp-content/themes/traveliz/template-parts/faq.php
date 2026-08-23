<?php 
// Получаем заголовок секции из Опций
$faq_title = get_field('title_faq', 'option'); 
?>
<?php if ( $faq_title ) : ?>
<?php if ( have_rows( 'faq', 'option' ) ) : ?>
<section class="faq l">
    <div class="container-6">
        <?php if ( $faq_title ) : ?>
            <h2><?php echo esc_html( $faq_title ); ?></h2>
      
        <?php endif; ?>

        <div class="into-faq">
            <div class="faq-container" id="faq-wrapper">
        
                <?php while ( have_rows( 'faq', 'option' ) ) : the_row(); 
                    // Получаем поля из повторителя
                    $question = get_sub_field( 'question' );
                    $answer   = get_sub_field( 'answer' );
                ?>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span class="title-text"><?php echo esc_html( $question ); ?></span>
                            <div class="status-box"></div>
                        </div>
                        <div class="faq-answer">
                            <div class="answer-inner">
                                <?php echo wp_kses_post( $answer ); // Используем wp_kses_post для сохранения форматирования текста ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php endif; ?>