<?php
/**
 * Admin layout header (OpenCart / Map Plum style).
 *
 * @var string $title
 * @var string $heading_title
 * @var string $route
 * @var string $header_buttons
 * @var array|null $flash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nav_items = array(
	'dashboard'       => __( 'Главная', 'ai-calculator' ),
	'language'        => __( 'Языки', 'ai-calculator' ),
	'category'        => __( 'Категории', 'ai-calculator' ),
	'manufacturer'    => __( 'Калькуляторы', 'ai-calculator' ),
	'attribute_group' => __( 'Группы атрибутов', 'ai-calculator' ),
	'attribute'       => __( 'Атрибуты', 'ai-calculator' ),
	'product'         => __( 'Товары', 'ai-calculator' ),
);

if ( empty( $heading_title ) && ! empty( $title ) ) {
	$heading_title = $title;
}
?>
<div class="wrap ai-calculator-admin">
	<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
	<hr class="wp-header-end">

	<nav class="ai-calculator-nav">
		<ul>
			<?php foreach ( $nav_items as $nav_route => $label ) : ?>
				<li class="<?php echo $route === $nav_route ? 'active' : ''; ?>">
					<a href="<?php echo esc_url( AI_Calculator_Router::url( $nav_route, 'index' ) ); ?>">
						<?php echo esc_html( $label ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php include AI_CALCULATOR_PATH . 'admin/views/partials/flash.php'; ?>

	<div id="content" class="ai-calculator-content">
		<div class="page-header">
			<div class="container-fluid">
				<h1><?php echo esc_html( $heading_title ); ?></h1>
				<div class="pull-right page-header-actions">
					<?php if ( ! empty( $header_buttons ) ) : ?>
						<?php echo $header_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
