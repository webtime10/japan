<?php
/* Template Name: test */

get_header();
?>
<style>

</style>
<div class="ai-calculators-test">

	

		<?php echo do_shortcode( '[ai_weather_calculator]' ); ?>


		<?php echo do_shortcode( '[ai_budget_calculator]' ); ?>


		<?php echo do_shortcode( '[fcc_family_directions]' ); ?>

		<?php echo do_shortcode( '[ai_ideal_region_calculator]' ); ?>

</div>

<?php get_footer(); ?>
