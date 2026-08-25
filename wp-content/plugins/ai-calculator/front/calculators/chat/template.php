<?php
/**
 * Chat calculator template (skeleton UI).
 *
 * @var array<string, mixed> $ai_chat
 *
 * @package ai-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$labels = isset( $ai_chat['labels'] ) && is_array( $ai_chat['labels'] ) ? $ai_chat['labels'] : array();
$title  = isset( $labels['title'] ) ? (string) $labels['title'] : '';
$summary_label = isset( $labels['summary'] ) ? (string) $labels['summary'] : '';
$regions_label = isset( $labels['regions'] ) ? (string) $labels['regions'] : '';
$cost_label    = isset( $labels['cost'] ) ? (string) $labels['cost'] : '';
$route_label   = isset( $labels['route'] ) ? (string) $labels['route'] : '';
$time_label    = wp_date( 'H:i' );
$img_base      = plugins_url( 'img/chat/', AI_CALCULATOR_FILE );
?>

<section class="ai-chat" aria-labelledby="ai-chat-title">
	<div class="ai-chat__inner">
		<h2 id="ai-chat-title" class="ai-chat__title"><?php echo esc_html( $title ); ?></h2>

		<div class="ai-chat__actions" role="list">
			<button type="button" class="ai-chat__action" role="listitem" data-ai-chat-action="summary">
				<span class="ai-chat__action-icon" aria-hidden="true">
					<img src="<?php echo esc_url( $img_base . 'lamp2.webp' ); ?>" alt="" width="56" height="56" loading="lazy" decoding="async">
				</span>
				<span class="ai-chat__action-label"><?php echo esc_html( $summary_label ); ?></span>
			</button>

			<button type="button" class="ai-chat__action" role="listitem" data-ai-chat-action="regions">
				<span class="ai-chat__action-icon" aria-hidden="true">
					<img src="<?php echo esc_url( $img_base . 'marker2.webp' ); ?>" alt="" width="56" height="56" loading="lazy" decoding="async">
				</span>
				<span class="ai-chat__action-label"><?php echo esc_html( $regions_label ); ?></span>
			</button>

			<button type="button" class="ai-chat__action" role="listitem" data-ai-chat-action="cost">
				<span class="ai-chat__action-icon" aria-hidden="true">
					<img src="<?php echo esc_url( $img_base . 'calc2.webp' ); ?>" alt="" width="56" height="56" loading="lazy" decoding="async">
				</span>
				<span class="ai-chat__action-label"><?php echo esc_html( $cost_label ); ?></span>
			</button>

			<button type="button" class="ai-chat__action" role="listitem" data-ai-chat-action="route">
				<span class="ai-chat__action-icon" aria-hidden="true">
					<img src="<?php echo esc_url( $img_base . 'marhrut2.webp' ); ?>" alt="" width="56" height="56" loading="lazy" decoding="async">
				</span>
				<span class="ai-chat__action-label"><?php echo esc_html( $route_label ); ?></span>
			</button>
		</div>

		<div class="ai-chat__panel">
			<div class="ai-chat__messages" aria-live="polite">
				<div class="ai-chat__row">
					<div class="ai-chat__avatar" aria-hidden="true">
						<img src="<?php echo esc_url( $img_base . 'ai_robot.webp' ); ?>" alt="" width="88" height="88" loading="lazy" decoding="async">
					</div>

					<div class="ai-chat__bubble">
						<p class="ai-chat__bubble-text"><?php esc_html_e( 'Привет! Чем могу вам помочь сегодня?', 'ai-calculator' ); ?></p>
						<div class="ai-chat__bubble-meta">
							<span class="ai-chat__time"><?php echo esc_html( $time_label ); ?></span>
							<span class="ai-chat__status" aria-hidden="true">
								<svg width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M1 5.2L4.2 8.4L12.8 1" stroke="#9aa3b2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						</div>
					</div>
				</div>
			</div>

			<form class="ai-chat__composer" action="#" method="post" onsubmit="return false;">
				<div class="ai-chat__composer-bar">
					<input
						id="ai-chat-input"
						class="ai-chat__input"
						type="text"
						name="ai_chat_question"
						placeholder="<?php esc_attr_e( 'Введите свой вопрос', 'ai-calculator' ); ?>"
						autocomplete="off"
					>
					<button type="button" class="ai-chat__send" aria-label="<?php esc_attr_e( 'Отправить', 'ai-calculator' ); ?>">
						<img src="<?php echo esc_url( $img_base . 'poisk.svg' ); ?>" alt="" width="20" height="18" decoding="async">
					</button>
				</div>
			</form>
		</div>
	</div>
</section>
