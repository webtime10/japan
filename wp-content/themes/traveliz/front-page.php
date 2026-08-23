<?php

/* Template Name: Front Page */
get_header();


?>


<?php
$title_1    = get_field('title_1_home');
$title_2    = get_field('title_2_home');
$title_text = get_field('title_text_home');
$button_1   = get_field('button_1_home');
$button_2   = get_field('button_2_home');

$button_1_link = get_field('button_1_link_home');
$button_2_link = get_field('button_2_link_home');

$item_1     = get_field('item_text_1');
$pre_1      = get_field('item_text_1_prefix');
$item_2     = get_field('item_text_2');
$pre_2      = get_field('item_text_2_prefix');
$item_3     = get_field('item_text_3');
$pre_3      = get_field('item_text_3_pefix'); // внимание: в админке опечатка 'pefix'
$item_4     = get_field('item_text_4');
$pre_4      = get_field('item_text_4_prefix');
?>
<?php if($title_1):?>
<div  class="site-main">
    <div class="container-4">
        <div class="into-site-main">
<div class="main-block-1 sw-1">	
    <h1><span><?php echo $title_1; ?></span><span><?php echo $title_2; ?></span></h1>
    <div class="site-main-title">
        <strong><?php echo $title_text; ?></strong>
    </div>
    <div class="site-main-button">
            <div class="hero-btns-group">
                <a href="<?php echo $button_1_link; ?>" class="btn-action btn-yellow">
                    <span class="btn-text"><?php echo $button_1; ?></span>
                    <img src="<?php echo get_template_directory_uri(); ?>/img/star.svg" alt="">
                </a>
                <a href="<?php echo $button_2_link; ?>" class="btn-action btn-blue">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/plus-circle.svg" alt="">
                    <span class="btn-text"><?php echo $button_2; ?></span>
                </a>

                
            </div>
    </div>
</div>
<div class="main-block-2 sw-2">
	
	<div class="item-block-2 sw-22">
    <span><?php echo $item_4; ?><br><?php echo $pre_4; ?></span>
    <img src="<?php echo get_template_directory_uri(); ?>/img/Group2004.svg" alt="">
</div>

<div class="item-block-2 sw-23">
    <span><?php echo $item_3; ?><br><?php echo $pre_3; ?></span>
    <img src="<?php echo get_template_directory_uri(); ?>/img/Group2007.svg" alt="">
</div>		
</div>
<div class="main-block-2 block-3">
<div class="item-block-2 sw-24 item-block-3">
    <span><?php echo $item_2; ?><br><?php echo $pre_2; ?></span>
    <img src="<?php echo get_template_directory_uri(); ?>/img/Group2005.svg" alt="">
</div>
<div class="item-block-2 sw-25 item-block-3">
    <span><?php echo $item_1; ?><br><?php echo $pre_1; ?></span>
    <img src="<?php echo get_template_directory_uri(); ?>/img/Group2006.svg" alt="">
</div>
		
</div>

</div>
</div>
</div>


</div>
<?php endif; ?>

<?php get_template_part('template-parts/swiss_experience'); ?> 

<?php get_template_part('template-parts/reviews'); ?>

<?php get_template_part( 'template-parts/what_you_will_get', null, array( 'omit_buttons' => true ) ); ?>

<?php get_template_part('template-parts/route_slider_3d'); ?>
<?php get_template_part('template-parts/slider_our_experience'); ?>



<?php get_template_part('template-parts/map'); ?>

<?php get_template_part('template-parts/slider_city'); ?>
<?php get_template_part('template-parts/slider_attractions'); ?>
<?php get_template_part('template-parts/faq'); ?>
<?php get_template_part('template-parts/how_it_works'); ?>






   
    <div class="overlay_vt"></div>
    <div class="modal_vt" id="contact-modal_vt">
        <div class="modal-content_vt">
  
           <iframe width="770" height="500" src="https://www.youtube.com/embed/8kPT9x5_2CU?si=jDmI5X3a4dt0nQDv" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div> 
 
        <button class="modal-close_vt" aria-label="Закрыть"></button>
    </div>


<?php

get_footer();

?>
