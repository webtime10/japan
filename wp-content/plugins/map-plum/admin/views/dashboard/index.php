<?php
/**
 * @var array $stats
 * @var array<int, array{slug: string, name: string, tags: array<int, string>}> $shortcodes
 */
$marker_url = Map_Plum_Router::url( 'marker' );
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-dashboard"></i> Обзор</h3>
	</div>
	<div class="panel-body">
		<div class="row map-plum-dashboard-tiles">
			<div class="col-md-3">
				<div class="tile">
					<div class="tile-heading">Округа</div>
					<div class="tile-body"><i class="fa fa-cube"></i></div>
					<div class="tile-footer"><a href="<?php echo esc_url( Map_Plum_Router::url( 'product' ) ); ?>"><?php echo esc_html( (string) $stats['products'] ); ?></a></div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="tile">
					<div class="tile-heading">Категории</div>
					<div class="tile-body"><i class="fa fa-folder"></i></div>
					<div class="tile-footer"><a href="<?php echo esc_url( Map_Plum_Router::url( 'category' ) ); ?>"><?php echo esc_html( (string) $stats['categories'] ); ?></a></div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="tile">
					<div class="tile-heading">Регионы</div>
					<div class="tile-body"><i class="fa fa-map-marker"></i></div>
					<div class="tile-footer"><a href="<?php echo esc_url( Map_Plum_Router::url( 'manufacturer' ) ); ?>"><?php echo esc_html( (string) $stats['manufacturers'] ); ?></a></div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="tile">
					<div class="tile-heading">Маркеры</div>
					<div class="tile-body"><i class="fa fa-map-pin"></i></div>
					<div class="tile-footer"><a href="<?php echo esc_url( $marker_url ); ?>"><?php echo esc_html( (string) $stats['markers'] ); ?></a></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default map-plum-shortcodes-panel">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-code"></i> Шорткоды карт</h3>
	</div>
	<div class="panel-body">
		<p class="map-plum-shortcodes-hint">Вставьте шорткод на страницу или в блок. Параметр высоты: <code>[tokyo height="600"]</code></p>
		<div class="table-responsive">
			<table class="table table-bordered table-hover map-plum-table map-plum-shortcodes-table">
				<thead>
					<tr>
						<th>Префектура</th>
						<th>Шорткоды</th>
						<th class="text-right" style="width: 120px;">Копировать</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $shortcodes as $row ) : ?>
						<?php $primary = '[' . $row['slug'] . ']'; ?>
						<tr>
							<td><strong><?php echo esc_html( $row['name'] ); ?></strong></td>
							<td>
								<?php foreach ( $row['tags'] as $i => $tag ) : ?>
									<?php if ( $i > 0 ) : ?><span class="map-plum-shortcode-sep"> · </span><?php endif; ?>
									<code class="map-plum-shortcode-tag">[<?php echo esc_html( $tag ); ?>]</code>
								<?php endforeach; ?>
							</td>
							<td class="text-right">
								<button type="button" class="btn btn-default btn-sm map-plum-copy-shortcode" data-copy="<?php echo esc_attr( $primary ); ?>" title="Копировать <?php echo esc_attr( $primary ); ?>">
									<i class="fa fa-clipboard"></i> <?php echo esc_html( $primary ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<p class="map-plum-shortcodes-hint">
			<button type="button" class="button map-plum-copy-shortcode" data-copy='[japan height="600"]'>Пример с высотой</button>
			<span class="map-plum-copy-feedback" id="map-plum-copy-feedback" aria-live="polite"></span>
		</p>
	</div>
</div>
