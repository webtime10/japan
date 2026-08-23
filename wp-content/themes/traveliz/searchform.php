<?php
/**
 * Custom full-screen search form.
 *
 * @package traveliz
 */
$search_is_rtl = function_exists( 'traveliz_pll_is_rtl' ) ? traveliz_pll_is_rtl() : is_rtl();
?>
<form
	role="search"
	method="get"
	class="search-form search-overlay-form <?php echo $search_is_rtl ? 'search-overlay-form--rtl' : 'search-overlay-form--ltr'; ?>"
	dir="<?php echo $search_is_rtl ? 'rtl' : 'ltr'; ?>"
	action="<?php echo esc_url( home_url( '/' ) ); ?>"
>
	<label class="screen-reader-text" for="overlay-search-field"><?php echo esc_html( get_theme_translation( 'search_for_label' ) ); ?></label>
	<div class="search-overlay-form-inner">
		<input
			id="overlay-search-field"
			type="search"
			class="search-field"
			placeholder="<?php echo esc_attr( get_theme_translation( 'search_placeholder' ) ); ?>"
			value="<?php echo get_search_query(); ?>"
			name="s"
			autocomplete="off"
			required
		/>
		<button type="submit" class="search-submit" aria-label="<?php echo esc_attr( get_theme_translation( 'search_submit_aria' ) ); ?>">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/img/zoom-in.svg' ); ?>" alt="">
		</button>
	</div>
</form>
