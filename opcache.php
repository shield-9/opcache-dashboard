<?php
/*
 * Plugin Name: OPcache Dashboard
 * Plugin URI: http://wordpress.org/plugins/opcache/
 * Description: OPcache dashboard designed for WordPress
 * Version: 0.2.1
 * Author: Daisuke Takahashi(Extend Wings)
 * Author URI: http://www.extendwings.com
 * License: AGPLv3 or later
 * Text Domain: opcache
 * Domain Path: /languages/
*/

if(!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if(!class_exists('WP_List_Table')) {
	require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

add_action('init', array('OPcache_dashboard', 'init'));
class OPcache_dashboard {
	static $instance;

	const PHP_URL = 'http://php.shield-9.org';

	const VERSION = '0.2.0';

	static function init() {
		if(!self::$instance) {
			if(did_action('plugins_loaded'))
				self::plugin_textdomain();
			else
				add_action('plugins_loaded', array(__CLASS__, 'plugin_textdomain'));

			self::$instance = new OPcache_dashboard;
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array(&$this, 'add_admin_menu'));
		if(is_multisite() && is_network_admin())
			add_action('network_admin_menu', array(&$this, 'add_admin_menu'));
		add_action('wp_loaded', array(&$this, 'register_assets'));
		add_filter('plugin_row_meta', array(&$this, 'plugin_row_meta'), 10, 2);
	}

	function register_assets() {
		if(!wp_script_is('d3js', 'registered'))
			wp_register_script('d3js', plugin_dir_url(__FILE__).'js/d3.min.js', false, '3.4.2');
		if(!wp_script_is('opcache', 'registered'))
			wp_register_script('opcache', plugin_dir_url(__FILE__).'js/chart.js', array('jquery', 'd3js'), self::VERSION, true);
		if(!wp_script_is('jquery-center', 'registered'))
			wp_register_script('jquery-center', plugin_dir_url(__FILE__).'js/jquery.center.min.js', array('jquery'), '1.1.1');
		if(!wp_style_is('opcache', 'registered'))
			wp_register_style('opcache', plugin_dir_url(__FILE__).'css/style.css', false, self::VERSION);
		if(!wp_style_is('genericons', 'registered'))
			wp_register_style('genericons', plugin_dir_url(__FILE__).'css/genericons.css', false, '3.0.3');
	}

	function add_admin_menu() {
		add_menu_page(
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('OPcache', 'opcache'),		// menu_title
			'manage_options',			// capability
			'opcache',				// menu_slug
			array(&$this, 'admin_page'),		// function
			'dashicons-backup',			// icon_url
			'3.14159265359'				// position
		);
		add_submenu_page(
			'opcache',				// parent_slug,
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('Dashboard', 'opcache'),		// menu_title,
			'manage_options',			// capability,
			'opcache',				// menu_slug,
			array(&$this, 'admin_page')		// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Status', 'opcache'),			// page_title
			__('Status', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-status',				// menu_slug,
			array(&$this, 'render_admin_status_page')	// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Scripts', 'opcache'),			// page_title
			__('Scripts', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-scripts',				// menu_slug,
			array(&$this, 'render_admin_scripts_page')	// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Configuration', 'opcache'),			// page_title
			__('Configuration', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-config',				// menu_slug,
			array(&$this, 'render_admin_config_page')	// function
		);

		add_action('admin_enqueue_scripts', array(&$this, 'admin_menu_assets'));
	}

	function admin_menu_assets($hook) {
		switch($hook) {
			case 'toplevel_page_opcache':
				wp_enqueue_script('opcache');
				wp_enqueue_script('jquery-center');
			case 'opcache_page_opcache-scripts':
			case 'opcache_page_opcache-status':
			case 'opcache_page_opcache-config':
				wp_enqueue_style('opcache');
				wp_enqueue_style('genericons');
				break;
		}
		return;
	}

	function admin_page() {
		if(isset($_GET['action']) && isset($_GET['_wpnonce']) && check_admin_referer('opcache_ctrl','_wpnonce')) {
			switch($_GET['action']) {
				case 'reset':
					opcache_reset();
					printf('<div class="updated"><p>%s</p></div>', esc_html__('Reseted!', 'opcache'));
					break;
				case 'invalidate':
					$status = opcache_get_status();
					foreach($status as $script)
						opcache_invalidate($script['full_path']);
					printf('<div class="updated"><p>%s</p></div>', esc_html__('Invalidated!', 'opcache'));
					break;
				case 'invalidate_force':
					$status = opcache_get_status();
					foreach($status as $script)
						opcache_invalidate($script['full_path'], true);
					printf('<div class="updated"><p>%s</p></div>', esc_html__('Force Invalidated!', 'opcache'));
					break;
			}
		}
		$config = opcache_get_configuration();
		$status = opcache_get_status(false);
		$stats = $status['opcache_statistics'];
		$mem_stats = $status['memory_usage'];
		$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];
		?>
		<div class="wrap">
			<h2><?php esc_html_e('OPcache Dashboard', 'opcache'); ?></h2>
			<div id="widgets-wrap">
				<div id="widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="hndle">
									<span>
										<?php printf('PHP: %1$s and OPcache: %2$s', phpversion(), $config['version']['version']); ?>
									</span>
								</h3>
								<div class="inside">
									<p id="hits">
										<?php printf('Hits: %s%%', $this->number_format($stats['opcache_hit_rate'], 2)); ?>
									</p>
									<p id="memory">
										<?php printf(
											'Memory: %1$s of %2$s',
											$this->size($mem_stats['used_memory'] + $mem_stats['wasted_memory']),
											$this->size($config['directives']['opcache.memory_consumption'])
										); ?>
									</p>
									<p id="keys">
										<?php printf(
											'Keys: %1$s of %2$s', $stats['num_cached_keys'], $stats['max_cached_keys']); ?>
									</p>
								</div>
							</div>
							<div class="postbox">
								<h3 class="hndle">
									<span><?php esc_html_e('Reset/Invalidate', 'opcache'); ?></span>
								</h3>
								<div class="inside">
									<?php printf(
										'<a href="%1$s" class="button button-primary button-large">%2$s</a>',
										esc_url(admin_url(sprintf(
												'admin.php?page=%1$s&action=reset&_wpnonce=%2$s&_wp_http_referer=%3$s',
												$_REQUEST['page'],
												wp_create_nonce('opcache_ctrl'),
												urlencode(wp_unslash($_SERVER['REQUEST_URI']))
										))),
										esc_html__('Reset', 'opcache')
									); ?>
									<?php printf(
										'<a href="%1$s" class="button button-large">%2$s</a>',
										esc_url(admin_url(sprintf(
												'admin.php?page=%1$s&action=invalidate&_wpnonce=%2$s',
												$_REQUEST['page'],
												wp_create_nonce('opcache_ctrl')
										))),
										esc_html__('Invalidate', 'opcache')
									); ?>
									<?php printf(
										'<a href="%1$s" class="button button-large">%2$s</a>',
										esc_url(admin_url(sprintf(
												'admin.php?page=%1$s&action=invalidate_force&_wpnonce=%2$s',
												$_REQUEST['page'],
												wp_create_nonce('opcache_ctrl')
										))),
										esc_html__('Force Invalidate', 'opcache')
									); ?>
									<p><strong><?php esc_html_e('These actions affect all cached opcodes.' ,'opcache'); ?></strong></p>
									<p>
										<?php printf(
											esc_html__('Please refer to %s for these difference information.', 'opcache'),
											sprintf('<a href="%1$s" target="_blank">%2$s</a>',
												esc_url(OPcache_dashboard::PHP_URL . '/ref.opcache'),
												esc_html__('the PHP.net', 'opcache')
											)
										); ?>
									</p>
								</div>
							</div>
							<div id="info-widget" class="postbox">
								<h3 class="hndle">
									<span><?php esc_html_e('Information', 'opcache'); ?></span>
								</h3>
								<div class="inside">
									<div class="info-widget">
										<h4><?php esc_html_e('Copyright', 'opcache'); ?></h4>
										<p>
											&copy;2012-2014 <a href="http://www.extendwings.com/" target="_blank">Daisuke Takahashi(Extend Wings)</a>
											Portions &copy;2010-2012 Web Online.
										</p>
										<p>
											<?php printf(
												esc_html__('This software is licensed under %s.', 'opcache'),
												sprintf(
													'<a href="%1$s"><img id="agpl-logo" src="%2$s" alt="GNU AFFERO GENERAL PUBLIC LICENSE, Version 3"></a>',
													esc_url(plugin_dir_url(__FILE__) . 'LICENSE'),
													esc_url(plugin_dir_url(__FILE__) . 'images/agpl.svg')
												)
											); ?>
										</p>
									</div>
									<div class="info-widget">
										<h4><?php esc_html_e('Contact', 'opcache'); ?></h4>
										<div>
											<?php esc_html_e('If you want to contact Daisuke Takahashi(Extend Wings), you can use:', 'opcache'); ?>
											<ul class="contact-list">
												<li>
													<?php printf(
														'<a href="%1$s" target="_blank">%2$s</a> %3$s',
														'https://wordpress.org/support/plugin/opcache',
														esc_html__('Plugin Support Forum', 'opcache'),
														esc_html__('(This forum is visible for everyone.)', 'opcache')
													); ?>
												</li>
												<li>
													<?php printf(
														esc_html__('%1$sFor Confidential information%2$s, %3$s or %4$s is recommended due to security considerations.', 'opcache'),
														'<strong>',
														'</strong>',
														sprintf(
															'<a href="https://plus.google.com/+DaisukeTakahashi0120" target="_blank">%s</a>',
															esc_html__('Google Hangouts (Message)', 'opcache')
														),
														sprintf(
															'<a href="https://www.facebook.com/messages/daisuke.takahashi.0120" target="_blank">%s</a>',
															esc_html__('Facebook Message', 'opcache')
														)
													); ?>
												</li>
											</ul>
										</div>
									</div>
									<div class="info-widget">
										<h4>
											<span class="genericon genericon-github"></span>
											<img id="github-logo" src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'images/github.svg'); ?>">
										</h4>
										<p>
											<iframe class="github-button" seamless src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'github-btn.html?user=shield-9&repo=opcache-dashboard&type=watch&count=true'); ?>" style="width: 85px;"></iframe>
											<iframe class="github-button" seamless src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'github-btn.html?user=shield-9&repo=opcache-dashboard&type=fork&count=true'); ?>" style="width: 85px;"></iframe>
											<iframe class="github-button" seamless src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'github-btn.html?user=shield-9&type=follow'); ?>" style="width: 135px;"></iframe>
										</p>
									</div>
									<div class="info-widget">
										<h4><?php esc_html_e('Feedback', 'opcache'); ?></h4>
										<p>
											<?php printf(
												'We are waiting for your feedback at %1$sPlugin Review%2$s.',
												'<a href="https://wordpress.org/support/view/plugin-reviews/opcache" target="_blank">',
												'</a>'
											); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="postbox-container-2" class="postbox-container">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<div class="inside">
									<form id="graph_ctrl">
										<label><input type="radio" name="dataset" value="memory" checked><?php esc_html_e('Memory', 'opcache'); ?></label>
										<label><input type="radio" name="dataset" value="keys"><?php esc_html_e('Keys', 'opcache'); ?></label>
										<label><input type="radio" name="dataset" value="hits"><?php esc_html_e('Hits', 'opcache'); ?></label>
									</form>
									<div id="graph">
										<div id="stats"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div><!-- wrap -->
		<script>
			var dataset={
				memory:[<?php echo esc_js($mem_stats['used_memory']); ?>, <?php echo esc_js($mem_stats['free_memory']); ?>, <?php echo esc_js($mem_stats['wasted_memory']); ?>],
				keys:[<?php echo esc_js($stats['num_cached_keys']); ?>, <?php echo esc_js($stats['num_free_keys']); ?>, 0],
				hits:[<?php echo esc_js($stats['misses']); ?>, <?php echo esc_js($stats['hits']); ?>, 0]
			};
			var mem_stats=[
				'<?php echo esc_js($this->size($mem_stats['used_memory'])); ?>',
				'<?php echo esc_js($this->size($mem_stats['free_memory'])); ?>',
				'<?php echo esc_js($this->size($mem_stats['wasted_memory'])); ?>',
				'<?php echo esc_js($this->number_format($mem_stats['current_wasted_percentage'],2)); ?>'
			];
			var label={
				memory:['<?php esc_html_e('Used', 'opcache'); ?>', '<?php esc_html_e('Free', 'opcache'); ?>', '<?php esc_html_e('Wasted', 'opcache'); ?>'],
				keys:['<?php esc_html_e('Cached Keys', 'opcache'); ?>', '<?php esc_html_e('Free Keys', 'opcache'); ?>', 0],
				hits:['<?php esc_html_e('Misses', 'opcache'); ?>', '<?php esc_html_e('Cache Hits', 'opcache'); ?>', 0]
			};
		</script>
		<?php
	}

	function render_admin_status_page() {
		$raw_status = opcache_get_status(false);

		require_once(plugin_dir_path(__FILE__).'class.status-list-table.php');
		foreach($raw_status as $key => $value) {
			if($key === 'scripts')
				continue;

			if(is_bool($value))
				$value = ($value === true) ? 'true' : 'false';

			if(is_array($value)) {
				foreach($value as $k => $v) {
					if(is_bool($v)) $v = ($v === true) ? 'true' : 'false';
					$status[] = array('name' => $k, 'value' => $v);
				}
			} else
				$status[] = array('name' => $key, 'value' => $value);
		}
		$list_table = new OPcache_List_Table($status);
		$list_table->prepare_items();
		?>
		<div class="wrap"><h2><?php _e('OPcache Status', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
	}

	function render_admin_scripts_page() {
		$status = opcache_get_status();

		require_once(plugin_dir_path(__FILE__).'class.script-list-table.php');
		$list_table = new OPcache_List_Table($status['scripts']);
		$list_table->prepare_items();
		?>
		<div class="wrap"><h2><?php _e('OPcache Scripts', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
	}

	function render_admin_config_page() {
		$raw_config = opcache_get_configuration();

		require_once(plugin_dir_path(__FILE__).'class.config-list-table.php');
		foreach($raw_config as $key => $value) {
			if(is_array($value)) {
				foreach($value as $k => $v) {
					if(is_bool($v)) $v = ($v === true) ? 'true' : 'false';
					$config[] = array('name' => $key.'.'.$k, 'value' => $v);
				}
			}
		}
		$list_table = new OPcache_List_Table($config);
		$list_table->prepare_items();
		?>
		<div class="wrap"><h2><?php _e('OPcache Configurations', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
	}

	static function plugin_textdomain() {
		load_plugin_textdomain('opcache', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	static function size($size) {
		$si_units = array('', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
		$i = 0;
		while($size >= 1024 && $i < count($si_units)) {
			$size = round($size / 1024, 2);
			$i++;
		}

		return OPcache_dashboard::number_format($size) . $si_units[$i] . 'B';
	}

	static function number_format($number, $decimals = 2) {
		return number_format($number, $decimals, '.', ',');
	}

	function plugin_row_meta($links, $file) {
		if(plugin_basename(__FILE__) === $file) {
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				admin_url('admin.php?page=opcache'),
				__('Dashboard', 'opcache')
			);
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url('http://www.extendwings.com/donate/'),
				__('Donate', 'opcache')
			);
		}
		return $links;
	}
}

?>
