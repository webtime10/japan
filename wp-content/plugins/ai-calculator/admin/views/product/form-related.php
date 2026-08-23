	<div class="panel panel-default">
		<div class="panel-heading"><h3 class="panel-title"><?php esc_html_e( 'Рекомендуемые товары', 'ai-calculator' ); ?></h3></div>
		<div class="panel-body">
			<?php
			$related_items = isset( $related_items ) ? $related_items : array();
			$lang_for_related = isset( $admin_language_id ) ? (int) $admin_language_id : 0;
			?>
			<div
				id="ai-product-related-picker"
				class="ai-product-related-picker"
				data-product-id="<?php echo (int) $id; ?>"
				data-language-id="<?php echo (int) $lang_for_related; ?>"
			>
				<div class="ai-related-chips" id="ai-related-chips">
					<?php foreach ( $related_items as $item ) : ?>
						<span class="ai-related-chip" data-id="<?php echo (int) $item['id']; ?>">
							<span class="ai-related-chip__label"><?php echo esc_html( $item['name'] ); ?></span>
							<button type="button" class="ai-related-chip__remove" aria-label="<?php esc_attr_e( 'Remove', 'ai-calculator' ); ?>">&times;</button>
							<input type="hidden" name="related_product_ids[]" value="<?php echo (int) $item['id']; ?>">
						</span>
					<?php endforeach; ?>
				</div>

				<div class="ai-related-search-wrap">
					<input
						type="text"
						id="ai-related-search"
						class="form-control ai-related-search"
						autocomplete="off"
						placeholder="<?php esc_attr_e( 'Введите минимум 2 буквы…', 'ai-calculator' ); ?>"
						<?php echo $category_id <= 0 ? 'disabled' : ''; ?>
					>
					<ul class="ai-related-suggestions" id="ai-related-suggestions" hidden></ul>
				</div>

				<p class="help-block ai-related-hint">
					<?php if ( $category_id <= 0 ) : ?>
						<?php esc_html_e( 'Сначала выберите категорию — рекомендуемые только из неё.', 'ai-calculator' ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Только товары из той же категории. Введите 2+ буквы, выберите из подсказок.', 'ai-calculator' ); ?>
					<?php endif; ?>
				</p>
			</div>
		</div>
	</div>
</form>
