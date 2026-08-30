<?php
/**
 * Block: Web expert (section.web-expert)
 *
 * Shortcode: [web_expert]
 * ACF options: Settings → Web Expert (web_expert_image, web_expert_title, web_expert_text)
 *
 * @var array $args Optional overrides: image, title, text.
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();

$img_field = array_key_exists( 'image', $args ) ? $args['image'] : get_field( 'web_expert_image', 'option' );
$title     = array_key_exists( 'title', $args ) ? (string) $args['title'] : (string) get_field( 'web_expert_title', 'option' );
$text      = array_key_exists( 'text', $args ) ? (string) $args['text'] : (string) get_field( 'web_expert_text', 'option' );

$img_url = '';
$img_alt = '';
if ( is_array( $img_field ) && ! empty( $img_field['url'] ) ) {
	$img_url = (string) $img_field['url'];
	$img_alt = ! empty( $img_field['alt'] ) ? (string) $img_field['alt'] : $title;
} elseif ( is_numeric( $img_field ) ) {
	$img_url = (string) wp_get_attachment_image_url( (int) $img_field, 'full' );
	$img_alt = (string) get_post_meta( (int) $img_field, '_wp_attachment_image_alt', true );
	if ( $img_alt === '' ) {
		$img_alt = $title;
	}
} elseif ( is_string( $img_field ) && $img_field !== '' ) {
	$img_url = $img_field;
}

if ( $img_url === '' ) {
	$img_url = get_template_directory_uri() . '/img/Ellipse531.webp';
}

if ( $title === '' && $text === '' ) {
	return;
}
?>

<section class="web-expert">
	<div class="container-4">
		<div class="web-expert-into">
			<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $img_alt ) ); ?>">
			<div class="the_web">
				<?php if ( $title !== '' ) : ?>
					<h3><?php echo wp_kses_post( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( $text !== '' ) : ?>
					<p><?php echo wp_kses_post( $text ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
