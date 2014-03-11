<?php
/*
 * Plugin Name: OPcache Dashboard
 * Plugin URI: http://wordpress.org/plugins/opcache/
 * Description: OPcache dashboard designed for WordPress
 * Version: 0.2.0
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

	static $php_url = "http://php.shield-9.org";

	static function init() {
		if(!self::$instance) {
		/*
			if(did_action('plugins_loaded'))
				self::plugin_textdomain();
			else
				add_action('plugins_loaded', array(__CLASS__, 'plugin_textdomain'));
		*/

			self::$instance = new OPcache_dashboard;
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array($this, 'add_admin_menu'));
		if(is_multisite() && is_network_admin())
			add_action('network_admin_menu', array($this, 'add_admin_menu'));
		add_action('wp_loaded', array($this, 'register_assets'));
	}

	function register_assets() {
		if(!wp_script_is('d3js', 'registered'))
			wp_register_script('d3js', plugin_dir_url(__FILE__).'js/d3.min.js', false, '3.4.2');
		if(!wp_script_is('opcache', 'registered'))
			wp_register_script('opcache', plugin_dir_url(__FILE__).'js/chart.js', array('jquery', 'd3js'), '0.1.0', true);
		if(!wp_script_is('jquery-center', 'registered'))
			wp_register_script('jquery-center', plugin_dir_url(__FILE__).'js/jquery.center.min.js', array('jquery'), '1.1.1');
		if(!wp_style_is('opcache', 'registered'))
			wp_register_style('opcache', plugin_dir_url(__FILE__).'css/style.css', false, '0.1.0');
		if(!wp_style_is('genericons', 'registered'))
			wp_register_style('genericons', plugin_dir_url(__FILE__).'css/genericons.css', false, '3.0.3');
	}

	function add_admin_menu() {
		add_menu_page(
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('OPcache', 'opcache'),		// menu_title
			'manage_options',			// capability
			'opcache',				// menu_slug
			array($this, 'admin_page'),		// function
			'dashicons-backup',			// icon_url
			'3.14159265359'				// position
		);
		add_submenu_page(
			'opcache',				// parent_slug,
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('Dashboard', 'opcache'),		// menu_title,
			'manage_options',			// capability,
			'opcache',				// menu_slug,
			array($this, 'admin_page')		// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Status', 'opcache'),			// page_title
			__('Status', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-status',				// menu_slug,
			array($this, 'render_admin_status_page')	// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Scripts', 'opcache'),			// page_title
			__('Scripts', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-scripts',				// menu_slug,
			array($this, 'render_admin_scripts_page')	// function
		);
		add_submenu_page(
			'opcache',					// parent_slug,
			__('Configuration', 'opcache'),			// page_title
			__('Configuration', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-config',				// menu_slug,
			array($this, 'render_admin_config_page')	// function
		);

		add_action('admin_enqueue_scripts', array($this, 'admin_menu_assets'));
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
		if(isset($_GET['_wpnonce']) && check_admin_referer('opcache_ctrl','_wpnonce')) {
			print_r($_REQUEST);
			switch($_GET['action']) {
				case 'reset':
					opcache_reset();
					echo '<div class="updated"><p>Reseted!</p></div>';
					break;
				case 'invalidate':
					$status = opcache_get_status();
					foreach($status as $script)
						opcache_invalidate($script['full_path']);
					echo '<div class="updated"><p>Invalidated!</p></div>';
					break;
				case 'invalidate_force':
					$status = opcache_get_status();
					foreach($status as $script)
						opcache_invalidate($script['full_path'], true);
					echo '<div class="updated"><p>Force Invalidated!</p></div>';
					break;
			}
		}
		$config = opcache_get_configuration();
		$status = opcache_get_status(false);
		$stats = $status['opcache_statistics'];
		$mem_stats = $status['memory_usage'];
		$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];
		?>
		<div class="wrap"><h2><?php _e('OPcache Dashboard', 'opcache'); ?></h2>
			<div id="widgets-wrap">
				<div id="widgets" class="metabox-holder">
					<div id="postbox-container-1" class="postbox-container">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="hndle">
									<span>PHP: <?php echo phpversion(); ?> and OPcache: <?php echo $config['version']['version']; ?></span>
								</h3>
								<div class="inside">
									<p id="hits">Hits: <?php echo $this->number_format($stats['opcache_hit_rate'], 2); ?>%</p>
									<p id="memory">
										Memory: <?php echo $this->size($mem_stats['used_memory'] + $mem_stats['wasted_memory']); ?>
											of <?php echo $this->size($config['directives']['opcache.memory_consumption']); ?>
									</p>
									<p id="keys">Keys: <?php echo $stats['num_cached_keys']; ?> of <?php echo $stats['max_cached_keys']; ?></p>
								</div>
							</div>
							<div class="postbox">
								<h3 class="hndle">
									<span>Reset/Invalidate</span>
								</h3>
								<div class="inside">
									<a href="?page=<?php echo $_REQUEST['page']; ?>&action=reset&_wpnonce=<?php echo wp_create_nonce('opcache_ctrl'); ?>&_wp_http_referer=<?php echo urlencode(wp_unslash($_SERVER['REQUEST_URI'])); ?>" class="button button-primary button-large">Reset</a>
									<a href="?page=<?php echo $_REQUEST['page']; ?>&action=invalidate&_wpnonce=<?php echo wp_create_nonce('opcache_ctrl'); ?>" class="button button-large">Invalidate</a>
									<a href="?page=<?php echo $_REQUEST['page']; ?>&action=invalidate_force&_wpnonce=<?php echo wp_create_nonce('opcache_ctrl'); ?>" class="button button-large">Force Invalidate</a>
									<p><strong>These actions affect all cached opcodes.</strong></p>
									<p>Please refer to <a href="<?php echo OPcache_dashboard::$php_url; ?>/ref.opcache">the PHP.net</a> for these difference information.</p>
								</div>
							</div>
							<div id="info-widget" class="postbox">
								<h3 class="hndle">
									<span>Information</span>
								</h3>
								<div class="inside">
									<div class="info-widget">
										<h4>Copyright</h4>
										<p>
											&copy;2012-2014 <a href="http://www.extendwings.com/">Daisuke Takahashi(Extend Wings)</a>. Portions &copy;2010-2012 Web Online.<br />
											This software is licensed under <a href="<?php echo plugin_dir_url(__FILE__); ?>LICENSE"><img id="agpl-logo" src="<?php echo plugin_dir_url(__FILE__); ?>images/agpl.svg" alt="GNU AGPLv3"></a>.
										</p>
									</div>
									<div class="info-widget">
										<h4>Contact</h4>
										<p>If you want to contact Daisuke Takahashi(Extend Wings), you can use:</p>
										<div>
											<a href="https://wordpress.org/support/plugin/opcache">Plugin Support Forum</a> (This forum is visible for everyone.)
										</div>
										<div>
											<strong>For Confidential information</strong>, <a href="https://plus.google.com/+DaisukeTakahashi0120">Google Hangouts (Message)</a> or <a href="https://www.beta.facebook.com/messages/daisuke.takahashi.0120">Facebook Message</a> is recommended due to security considerations.
										</div>
									</div>
									<div class="info-widget">
										<h4>
											<span class="genericon genericon-github"></span>
											<img id="github-logo" src="<?php echo plugin_dir_url(__FILE__); ?>images/github.svg">
										</h4>
										<p>
											<iframe class="github-button" seamless src="<?php echo plugin_dir_url(__FILE__); ?>github-btn.html?user=shield-9&repo=opcache-dashboard&type=watch&count=true" style="width: 85px;"></iframe>
											<iframe class="github-button" seamless src="<?php echo plugin_dir_url(__FILE__); ?>github-btn.html?user=shield-9&repo=opcache-dashboard&type=fork&count=true" style="width: 85px;"></iframe>
											<iframe class="github-button" seamless src="<?php echo plugin_dir_url(__FILE__); ?>github-btn.html?user=shield-9&type=follow" style="width: 135px;"></iframe>
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
										<label><input type="radio" name="dataset" value="memory" checked>Memory</label>
										<label><input type="radio" name="dataset" value="keys">Keys</label>
										<label><input type="radio" name="dataset" value="hits">Hits</label>
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
				memory:[<?php echo $mem_stats['used_memory']; ?>,<?php echo $mem_stats['free_memory'];?>,<?php echo $mem_stats['wasted_memory']; ?>],
				keys:[<?php echo $stats['num_cached_keys']; ?>,<?php echo $stats['num_free_keys']; ?>,0],
				hits:[<?php echo $stats['misses']; ?>,<?php echo $stats['hits']; ?>,0]
			};
			var mem_stats=[
				'<?php echo $this->size($mem_stats['used_memory']); ?>',
				'<?php echo $this->size($mem_stats['free_memory']); ?>',
				'<?php echo $this->size($mem_stats['wasted_memory']); ?>',
				'<?php echo $this->number_format($mem_stats['current_wasted_percentage'],2); ?>'
			];
		</script>
		<?php
	}

	function render_admin_status_page() {
	//	$config = opcache_get_configuration();
		$raw_status = opcache_get_status(false);
	//	$stats = $status['opcache_statistics'];
	//	$mem_stats = $status['memory_usage'];
	//	$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];

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
		<div class="wrap"><h2><?php _e('OPcache Dashboard', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
	}

	function render_admin_scripts_page() {
	//	$config = opcache_get_configuration();
		$status = opcache_get_status();
	//	$stats = $status['opcache_statistics'];
	//	$mem_stats = $status['memory_usage'];
	//	$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];

		require_once(plugin_dir_path(__FILE__).'class.script-list-table.php');
		$list_table = new OPcache_List_Table($status['scripts']);
		$list_table->prepare_items();
		?>
		<div class="wrap"><h2><?php _e('OPcache Dashboard', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
	}

	function render_admin_config_page() {
		$raw_config = opcache_get_configuration();
	//	$status = opcache_get_status();
	//	$stats = $status['opcache_statistics'];
	//	$mem_stats = $status['memory_usage'];
	//	$stats['num_free_keys'] = $stats['max_cached_keys'] - $stats['num_cached_keys'];

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
		<div class="wrap"><h2><?php _e('OPcache Dashboard', 'opcache'); ?></h2>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
				<?php $list_table->display() ?>
			</form>
		</div><!-- wrap -->
		<?php
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
}

?>
