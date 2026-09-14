<?php
/**
 * 404 — Japan brand, logo centered.
 *
 * @package traveliz
 */

get_header();

$lang = function_exists( 'traveliz_pll_current_slug' ) ? traveliz_pll_current_slug() : 'he';

$copy = array(
	'he' => array(
		'code'    => '404',
		'title'   => 'העמוד לא נמצא',
		'text'    => 'נראה שהגעתם לנתיב שלא קיים. בואו נחזור להתחיל את המסע ביפן.',
		'cta'     => 'חזרה לדף הבית',
	),
	'en' => array(
		'code'    => '404',
		'title'   => 'Page not found',
		'text'    => 'This path doesn’t exist. Let’s head back and start your Japan journey.',
		'cta'     => 'Back to home',
	),
	'ar' => array(
		'code'    => '404',
		'title'   => 'الصفحة غير موجودة',
		'text'    => 'يبدو أن هذا المسار غير موجود. لنعد ونبدأ رحلتكم إلى اليابان.',
		'cta'     => 'العودة للرئيسية',
	),
);

$t = isset( $copy[ $lang ] ) ? $copy[ $lang ] : $copy['he'];
?>

<main id="primary" class="site-main site-main--404">
	<section class="error-404 error-404--japan not-found" aria-labelledby="error-404-title">
		<div class="error-404__inner">
			<p class="error-404__code" aria-hidden="true"><?php echo esc_html( $t['code'] ); ?></p>
			<h1 id="error-404-title" class="error-404__title"><?php echo esc_html( $t['title'] ); ?></h1>
			<p class="error-404__text"><?php echo esc_html( $t['text'] ); ?></p>

			<a class="error-404__cta yl" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo esc_html( $t['cta'] ); ?>
			</a>
		</div>
	</section>
</main>

<?php
get_footer();
