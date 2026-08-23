<?php
/**
 * Hidden fields for admin POST forms (WordPress needs page in request).
 *
 * @var string $page_slug
 * @var string $action
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input type="hidden" name="page" value="<?php echo esc_attr( $page_slug ); ?>">
<?php if ( ! empty( $action ) && 'index' !== $action ) : ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
<?php endif; ?>
