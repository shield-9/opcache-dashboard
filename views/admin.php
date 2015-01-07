<?php
extract( $data );

$stats = $status['opcache_statistics'];
$mem_stats = $status['memory_usage'];
$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];
?>
		<div class="wrap">
			<h2><?php esc_html_e('OPcache Dashboard', 'opcache'); ?></h2>
			<div id="widgets-wrap">
				<div id="widgets" class="metabox-holder">
					<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
					<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
					<script type="text/javascript">
						jQuery(document).ready( function($) {
							jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
							if(typeof postboxes !== 'undefined')
								postboxes.add_postbox_toggles(pagenow);
						});
					</script>
					<div id="postbox-container-1" class="postbox-container">
						<?php do_meta_boxes( $screen->id, 'normal', null); ?>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<?php do_meta_boxes( $screen->id, 'side', null); ?>
					</div>
					<div id='postbox-container-3' class='postbox-container'>
						<div id="column3-sortables" class="meta-box-sortables"></div>
					</div>
					<div id='postbox-container-4' class='postbox-container'>
						<div id="column4-sortables" class="meta-box-sortables"></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div><!-- wrap -->
		<script>
			var dataset={
				memory:[<?php echo esc_js( $mem_stats['used_memory'] ); ?>, <?php echo esc_js( $mem_stats['free_memory'] ); ?>, <?php echo esc_js( $mem_stats['wasted_memory'] ); ?>],
				keys:[<?php echo esc_js( $stats['num_cached_keys'] ); ?>, <?php echo esc_js( $stats['num_free_keys'] ); ?>, 0],
				hits:[<?php echo esc_js( $stats['misses'] ); ?>, <?php echo esc_js( $stats['hits'] ); ?>, 0]
			};
			var mem_stats=[
				'<?php echo esc_js( $this->size( $mem_stats['used_memory'] ) ); ?>',
				'<?php echo esc_js( $this->size( $mem_stats['free_memory'] ) ); ?>',
				'<?php echo esc_js( $this->size( $mem_stats['wasted_memory'] ) ); ?>',
				'<?php echo esc_js( $this->number_format( $mem_stats['current_wasted_percentage'], 2) ); ?>'
			];
			var label={
				memory:['<?php esc_html_e('Used', 'opcache'); ?>', '<?php esc_html_e('Free', 'opcache'); ?>', '<?php esc_html_e('Wasted', 'opcache'); ?>'],
				keys:['<?php esc_html_e('Cached Keys', 'opcache'); ?>', '<?php esc_html_e('Free Keys', 'opcache'); ?>', 0],
				hits:['<?php esc_html_e('Misses', 'opcache'); ?>', '<?php esc_html_e('Cache Hits', 'opcache'); ?>', 0]
			};
		</script>
