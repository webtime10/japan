<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">

<?php
comment_form( array(
    'title_reply'          => '', 
    'comment_notes_before' => '',
    'logged_in_as'         => '', 
    'label_submit'         => get_theme_translation('comment_submit'), // Перевод кнопки
    'class_submit'         => 'submit-btn',
    
    'submit_field' => '<div class="comment-form-footer">
                            <div class="captcha-zone">
                                <input type="checkbox" id="human-check" name="human-check" required>
                                <label for="human-check">' . get_theme_translation('comment_captcha') . '</label>
                            </div>
                            %1$s %2$s
                        </div>',
    
    'fields' => array(
        'author' => '<div class="comment-form-top">
                        <div class="input-wrapper">
                            <input id="author" name="author" type="text" placeholder="' . esc_attr( get_theme_translation( 'comment_name' ) ) . '" required />
                        </div>',
        'email'  => '   <div class="input-wrapper">
                            <input id="email" name="email" type="email" placeholder="' . esc_attr( get_theme_translation( 'comment_email' ) ) . '" required />
                        </div>
                     </div>',
        'url'     => '', 
        'cookies' => '', 
    ),

    'comment_field' => '<div class="comment-form-container">
                            <div class="comment-form-into">
                                <div class="comment-avatar">
                                    <img src="' . get_stylesheet_directory_uri() . '/img/avatar.png" alt="Аватар" width="93" height="93">
                                </div>
                                <div class="comment-textarea-block">
                                    <textarea id="comment" name="comment" placeholder="' . esc_attr( get_theme_translation( 'comment_placeholder' ) ) . '" required></textarea>
                                </div>
                            </div>
                        </div>',
)); 
?>

    <?php if ( have_comments() ) : ?>
        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 48,
            ) );
            ?>
        </ol>
    <?php endif; ?>

</div>
