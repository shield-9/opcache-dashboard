<?php
$config = $data['config'];
$stats = $data['status']['opcache_statistics'];
$mem_stats = $data['status']['memory_usage'];
?>
			<p id="hits"><?php printf('Hits: %s%%', $this->number_format( $stats['opcache_hit_rate'], 2) ); ?>
			<p id="memory"><?php printf(
					'Memory: %1$s of %2$s',
					$this->size( $mem_stats['used_memory'] + $mem_stats['wasted_memory'] ),
					$this->size( $config['directives']['opcache.memory_consumption'] )
				); ?>
			<p id="keys"><?php printf('Keys: %1$s of %2$s', $stats['num_cached_keys'], $stats['max_cached_keys'] ); ?>
