<?php
extract( $data );

$stats = $status['opcache_statistics'];
$mem_stats = $status['memory_usage'];
$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];

wp_enqueue_script('opcache');
wp_enqueue_script('jquery-center');
?>
<form id="graph_ctrl">
	<label><input type="radio" name="dataset" value="memory" checked><?php esc_html_e('Memory', 'opcache'); ?></label>
	<label><input type="radio" name="dataset" value="keys"><?php esc_html_e('Keys', 'opcache'); ?></label>
	<label><input type="radio" name="dataset" value="hits"><?php esc_html_e('Hits', 'opcache'); ?></label>
</form>
<div id="graph">
	<div id="stats"></div>
</div>
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
