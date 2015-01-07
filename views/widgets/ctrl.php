<?php
function make_button( $label, $action, $referer = false, $level = 'low') {
	$url = sprintf(
		'admin.php?page=%1$s&action=%2$s' . ( $referer ? '&_wp_http_referer=%3$s' : NULL),
		$_REQUEST['page'],
		$action,
		urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) )
	);
	if( is_network_admin() ) {
		$url = network_admin_url( $url );
	} else {
		$url = admin_url( $url );
	}

	printf(
		'<a href="%1$s" class="button ' . ( ( $level == 'high') ? 'button-primary ' : '') . 'button-large">%2$s</a>',
		wp_nonce_url( $url, 'opcache_ctrl'),
		$label
	);
}

make_button( esc_html__('Reset', 'opcache'), 'reset', true, 'high');
make_button( esc_html__('Invalidate', 'opcache'), 'invalidate');
make_button( esc_html__('Force Invalidate', 'opcache'), 'invalidate_force');
?>
			<p><strong><?php esc_html_e('These actions affect all cached opcodes.' ,'opcache'); ?></strong>
			<p>
				<?php printf(
					esc_html__('Please refer to %s for these difference information.', 'opcache'),
					sprintf('<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( OPcache_dashboard::PHP_URL . '/ref.opcache'),
						esc_html__('the PHP.net', 'opcache')
					)
				); ?>
