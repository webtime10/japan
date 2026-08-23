<?php
/**
 * Flash messages.
 *
 * @var array|null $flash
 */

if ( ! defined( 'ABSPATH' ) || empty( $flash ) ) {
	return;
}

$text = isset( $flash['text'] ) ? $flash['text'] : '';
if ( '' === $text || ( is_array( $text ) && empty( $text ) ) ) {
	return;
}

$type = ( isset( $flash['type'] ) && 'success' === $flash['type'] ) ? 'success' : 'error';
$icon = ( 'success' === $type ) ? 'check-circle' : 'exclamation-circle';
?>
<div class="alert alert-<?php echo esc_attr( $type ); ?> ai-calculator-alert">
	<i class="fa fa-<?php echo esc_attr( $icon ); ?>"></i>
	<?php if ( is_array( $text ) ) : ?>
		<ul class="ai-calculator-alert-list">
			<?php foreach ( $text as $line ) : ?>
				<li><?php echo esc_html( (string) $line ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<?php echo esc_html( (string) $text ); ?>
	<?php endif; ?>
	<button type="button" class="close" onclick="this.parentElement.remove();">&times;</button>
</div>
