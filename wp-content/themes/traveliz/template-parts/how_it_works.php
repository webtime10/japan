<?php
$hiw_title = get_field('title_how_it_works', 'option');

$hiw_item_1 = get_field('item_1', 'option');
$hiw_item_2 = get_field('item_2', 'option');
$hiw_item_3 = get_field('item_3', 'option');
$hiw_item_4 = get_field('item_4', 'option');
$hiw_item_5 = get_field('item_5', 'option');
$hiw_item_6 = get_field('item_6', 'option');

$title_form = get_field('title_form_home', 'option');
$hiw_bg_array = get_field('background_image_how_it_works', 'option'); 
$bg_bg = $hiw_bg_array['url']; 

?>

<section style="background-image: url(<?php echo $bg_bg; ?>)" class="work">
   
<div class="container-8">
   

        <div class="into-work">
                 <h2><?php echo $hiw_title; ?></h2>    
<div class="wrap-work">
    <div class="left-work">
          <div class="wrap-img-work">
            <p><?php echo $hiw_item_1;?></p>

            <img src="<?php echo get_template_directory_uri(); ?>/img/a1.png" alt="" />
          </div>

          <img class="st1" src="<?php echo get_template_directory_uri(); ?>/img/Vector11.png" alt="" />
           
          <div class="wrap-img-work">
            <p><?php echo $hiw_item_2;?></p>
            <img  src="<?php echo get_template_directory_uri(); ?>/img/a2.png" alt="" />
          </div>

          <img class="st2" src="<?php echo get_template_directory_uri(); ?>/img/Vector22.png" alt=""> 

          <div class="wrap-img-work">
            <p><?php echo $hiw_item_3;?></p>
            <img  src="<?php echo get_template_directory_uri(); ?>/img/a3.png" alt="" />
          </div>
    </div>
    <div class="center-work">




  



<div class="form-wrapper">
    <h3><?php echo $title_form; ?></h3>
    <?php
    $lang = function_exists('pll_current_language') ? pll_current_language() : traveliz_pll_default_slug();

    $forms = [
        'he' => '9954f00',
        'en' => '1b5e883',
        'ar' => 'f2e5507',
    ];

    $current_form_id = traveliz_resolve_cf7_form_id($forms, is_string($lang) ? $lang : traveliz_pll_current_slug());

    // Выводим форму напрямую, игнорируя ACF и его слеши
    echo do_shortcode('[contact-form-7 id="' . $current_form_id . '"]'); 
    ?>
</div>

</div>
   






   
    <div class="right-work">

          <div class="wrap-img-work-right">
            <img  src="<?php echo get_template_directory_uri(); ?>/img/a4.png" alt="" />
            <p><?php echo $hiw_item_4;?></p>
          </div>
          <img class="st3" src="<?php echo get_template_directory_uri(); ?>/img/Vector33.png" alt="">
          <div class="wrap-img-work-right">
            <img src="<?php echo get_template_directory_uri(); ?>/img/a5.png" alt="" />
            <p><?php echo $hiw_item_5;?></p>
          </div>
          <img class="st4" src="<?php echo get_template_directory_uri(); ?>/img/Vector44.png" alt="">
          <div class="wrap-img-work-right st44">
            <img src="<?php echo get_template_directory_uri(); ?>/img/a6.png" alt="" />
            <p><?php echo $hiw_item_6;?></p>
          </div>

    </div>
</div>
        </div>
</div>
</section>