<?php
/**
 * Template part for "Ready to create a route" block
 */

// Get fields from ACF options page
$ready_title = get_field('ready_title', 'option');

$ready_button_1_text = get_field('ready_button_1_text', 'option');
$ready_button_1_type  = get_field('ready_button_1_type', 'option');
$ready_button_1_link  = get_field('ready_button_1_link', 'option');

$ready_button_2_text = get_field('ready_button_2_text', 'option');
$ready_button_2_type  = get_field('ready_button_2_type', 'option');
$ready_button_2_link  = get_field('ready_button_2_link', 'option');
?>

<?php if ($ready_title) : ?>
<section class="ready l">
    <div class="container-4">
        <h2><?php echo esc_html($ready_title); ?></h2>
        
        <div class="ready-buttons">
            <?php if ($ready_button_1_text) : ?>
                <?php if ($ready_button_1_type === 'link' && $ready_button_1_link) : ?>
                    <a href="<?php echo esc_url($ready_button_1_link); ?>" class="btn-action btn-yellow">
                        <span class="btn-text"><?php echo esc_html($ready_button_1_text); ?></span>
                    </a>
                <?php elseif ($ready_button_1_type === 'popup') : ?>
                    <a href="#" class="btn-action btn-yellow popup-trigger" data-popup="ready-button1">
                        <span class="btn-text"><?php echo esc_html($ready_button_1_text); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($ready_button_2_text) : ?>
                <?php if ($ready_button_2_type === 'link' && $ready_button_2_link) : ?>
                    <a href="<?php echo esc_url($ready_button_2_link); ?>" class="btn-action btn-blue">
                        <span class="btn-text"><?php echo esc_html($ready_button_2_text); ?></span>
                    </a>
                <?php elseif ($ready_button_2_type === 'popup') : ?>
                    <a href="#" class="btn-action btn-blue popup-trigger" data-popup="ready-button2">
                        <span class="btn-text"><?php echo esc_html($ready_button_2_text); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

