<?php
/**
 * Admin home.
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AI_CALCULATOR_PATH . 'inc/class-ai-calculator-settings.php';

$rest_index_path   = (string) wp_parse_url( rest_url( 'ai-calculator/v1/' ), PHP_URL_PATH );
$active_url        = AI_Calculator_Settings::get_active_url();
$api_key           = AI_Calculator_Settings::get_api_key();
$api_key_in_config = defined( 'AI_CALCULATOR_LARA_API_KEY' );
$laravel_paths     = array();
$rest_paths        = array();
$calculator_meta   = array();

if ( class_exists( 'AI_Calculator_Manager' ) ) {
	foreach ( AI_Calculator_Manager::slugs() as $slug ) {
		$laravel_paths[] = AI_Calculator_Settings::get_laravel_plugin_path( $slug );
		$rest_paths[]    = (string) wp_parse_url( rest_url( 'ai-calculator/v1/' . $slug ), PHP_URL_PATH );
	}
	$calculator_meta = AI_Calculator_Manager::all_meta();
}
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Куда отправлять запрос (Laravel)', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<form id="ai-calculator-remote-site-form" class="form-horizontal">
			<div class="form-group">
				<label class="control-label" for="ai-calculator-remote-url"><?php esc_html_e( 'Laravel (хост)', 'ai-calculator' ); ?></label>
				<input type="text" class="form-control" id="ai-calculator-remote-url" value="<?php echo esc_attr( $active_url ); ?>" placeholder="lara2.loc" autocomplete="off">
			</div>
			<div class="form-group">
				<label class="control-label" for="ai-calculator-remote-api-key"><?php esc_html_e( 'API ключ Laravel', 'ai-calculator' ); ?></label>
				<input type="password" class="form-control" id="ai-calculator-remote-api-key" value="<?php echo esc_attr( $api_key ); ?>" placeholder="PLUGIN_WEATHER_API_KEY из .env" autocomplete="off" <?php echo $api_key_in_config ? 'readonly' : ''; ?>>
				<?php if ( $api_key_in_config ) : ?>
					<p class="help-block"><?php esc_html_e( 'Задан в wp-config.php (AI_CALCULATOR_LARA_API_KEY).', 'ai-calculator' ); ?></p>
				<?php endif; ?>
			</div>

			<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Сохранить', 'ai-calculator' ); ?></button>
		</form>
		<?php if ( ! empty( $laravel_paths ) ) : ?>
			<ul style="margin-top:16px;margin-bottom:0;">
				<?php foreach ( $laravel_paths as $laravel_path ) : ?>
					<li><code><?php echo esc_html( $laravel_path ); ?></code></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>

<?php if ( ! empty( $calculator_meta ) ) : ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Шорткоды калькуляторов', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Калькулятор', 'ai-calculator' ); ?></th>
					<th><?php esc_html_e( 'Шорткоды', 'ai-calculator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $calculator_meta as $slug => $meta ) : ?>
					<tr>
						<td>
							<strong><?php echo esc_html( $meta['title'] ); ?></strong>
							<br><code><?php echo esc_html( $slug ); ?></code>
						</td>
						<td>
							<?php if ( ! empty( $meta['shortcodes'] ) ) : ?>
								<ul style="margin:0;">
									<?php foreach ( $meta['shortcodes'] as $shortcode ) : ?>
										<li><code>[<?php echo esc_html( $shortcode ); ?>]</code></li>
									<?php endforeach; ?>
								</ul>
							<?php else : ?>
								&mdash;
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php endif; ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php esc_html_e( 'Куда отправляется запрос (WordPress)', 'ai-calculator' ); ?></h3>
	</div>
	<div class="panel-body">
		<ul>
			<li><code><?php echo esc_html( $rest_index_path ); ?></code></li>
			<?php foreach ( $rest_paths as $rest_path ) : ?>
				<li><code><?php echo esc_html( $rest_path ); ?></code></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
