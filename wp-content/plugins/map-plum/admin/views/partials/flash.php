<?php
if ( ! empty( $flash ) && ! empty( $flash['text'] ) ) :
	$type = ( isset( $flash['type'] ) && $flash['type'] === 'success' ) ? 'success' : 'error';
	$icon = ( 'success' === $type ) ? 'check-circle' : 'exclamation-circle';
	?>
	<div class="alert alert-<?php echo esc_attr( $type ); ?> map-plum-alert">
		<i class="fa fa-<?php echo esc_attr( $icon ); ?>"></i>
		<?php if ( is_array( $flash['text'] ) ) : ?>
			<ul class="map-plum-alert-list">
				<?php foreach ( $flash['text'] as $line ) : ?>
					<li><?php echo esc_html( (string) $line ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<?php echo esc_html( (string) $flash['text'] ); ?>
		<?php endif; ?>
		<button type="button" class="close" onclick="this.parentElement.remove();">&times;</button>
	</div>
<?php endif; ?>
