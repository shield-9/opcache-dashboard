<?php
/*
 * Plugin Name: OPcache Dashboard
 * Plugin URI: http://wordpress.org/plugins/opcache/
 * Description: OPcache dashboard designed for WordPress
 * Version: 0.3.1
 * Author: Daisuke Takahashi(Extend Wings)
 * Author URI: http://www.extendwings.com
 * License: AGPLv3 or later
 * Text Domain: opcache
 * Domain Path: /languages/
*/

if( !function_exists('add_action') ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

if( version_compare( get_bloginfo('version'), '3.8', '<') ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php');
	deactivate_plugins( __FILE__ );
}

if( !class_exists('WP_List_Table') )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

add_action('init', array('OPcache_dashboard', 'init') );
class OPcache_dashboard {
	static $instance;

	const PHP_URL = 'http://php.shield-9.org';

	const VERSION = '0.3.1';

	static $PLUGIN_URL;
	static $PLUGIN_DIR;
	static $PLUGIN_FILE;

	private $data;
	private $hooks;

	static function init() {
		if( !self::$instance ) {
			self::$PLUGIN_URL = plugin_dir_url( __FILE__ );
			self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );
			self::$PLUGIN_FILE = __FILE__;

			if( did_action('plugins_loaded') )
				self::plugin_textdomain();
			else
				add_action('plugins_loaded', array( __CLASS__, 'plugin_textdomain') );

			self::$instance = new OPcache_dashboard;
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('admin_menu', array( &$this, 'add_admin_menu') );
		if( is_multisite() && is_network_admin() )
			add_action('network_admin_menu', array( &$this, 'add_admin_menu') );
		add_action('wp_loaded', array( &$this, 'register_assets') );
		add_filter('plugin_row_meta', array( &$this, 'plugin_row_meta'), 10, 2);

		add_action('wp_dashboard_setup', array( &$this, 'add_dashboard_widgets') );

		// Reset all cache when Upgrader Process complete
		add_action('upgrader_process_complete', array( &$this, 'version_up_reset'), 10, 2);
	}

	function version_up_reset() {
		opcache_reset();

		$hook_extra =  array(
			'action' => 'update',
			'type' => 'core',
			'bulk' => true,
		);
		if( func_num_args() >= 2)
			$hook_extra = array_merge( $hook_extra, func_get_arg(1) );

		trigger_error("Your WordPress is successfully updated! Detail:\n" . var_export( $hook_extra, true), E_USER_NOTICE );
	}

	function register_assets() {
		if( is_admin() ) {
			if( !wp_script_is('d3js', 'registered') )
				wp_register_script(
					'd3js',
					self::$PLUGIN_URL . 'js/d3.min.js',
					false,
					'3.4.4'
				);

			if( !wp_script_is('opcache', 'registered'))
				wp_register_script(
					'opcache',
					self::$PLUGIN_URL . 'js/chart.js',
					array('jquery', 'd3js'),
					self::VERSION,
					true
				);

			if( !wp_script_is('jquery-center', 'registered') )
				wp_register_script(
					'jquery-center',
					self::$PLUGIN_URL . 'js/jquery.center.min.js',
					array('jquery'),
					'1.1.1'
				);

			if( !wp_style_is('opcache', 'registered') )
				wp_register_style(
					'opcache',
					self::$PLUGIN_URL . 'css/style.css',
					false,
					self::VERSION
				);

			if( !wp_style_is('genericons', 'registered') )
				wp_register_style(
					'genericons',
					self::$PLUGIN_URL . 'css/genericons.css',
					false,
					'3.0.3'
				);
		}
	}

	function add_admin_menu() {
		$this->hooks[] = add_menu_page(
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('OPcache', 'opcache'),		// menu_title
			'manage_options',			// capability
			'opcache',				// menu_slug
			array( &$this, 'render_admin_page'),		// function
			'dashicons-backup',			// icon_url
			'3.14159265359'				// position
		);
		$this->hooks[] = add_submenu_page(
			'opcache',				// parent_slug,
			__('OPcache Dashboard', 'opcache'),	// page_title
			__('Dashboard', 'opcache'),		// menu_title,
			'manage_options',			// capability,
			'opcache',				// menu_slug,
			array( &$this, 'render_admin_page')	// function
		);
		$this->hooks[] = add_submenu_page(
			'opcache',					// parent_slug,
			__('Status', 'opcache'),			// page_title
			__('Status', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-status',				// menu_slug,
			array( &$this, 'render_admin_status_page')	// function
		);
		$this->hooks[] = add_submenu_page(
			'opcache',					// parent_slug,
			__('Scripts', 'opcache'),			// page_title
			__('Scripts', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-scripts',				// menu_slug,
			array( &$this, 'render_admin_scripts_page')	// function
		);
		$this->hooks[] = add_submenu_page(
			'opcache',					// parent_slug,
			__('Configuration', 'opcache'),			// page_title
			__('Configuration', 'opcache'),			// menu_title,
			'manage_options',				// capability,
			'opcache-config',				// menu_slug,
			array( &$this, 'render_admin_config_page')	// function
		);
		if( version_compare( PHP_VERSION, '5.5.5') >= 0)
			$this->hooks[] = add_submenu_page(
				'opcache',					// parent_slug,
				__('Manual Cache Control', 'opcache'),		// page_title
				__('Manual Control', 'opcache'),		// menu_title,
				'manage_options',				// capability,
				'opcache-manual',				// menu_slug,
				array( &$this, 'render_admin_manual_page')	// function
			);

		add_action('admin_enqueue_scripts', array( &$this, 'admin_menu_assets') );
	}

	function admin_menu_assets( $hook ) {
		if( in_array( $hook, $this->hooks ) ) {
			wp_enqueue_style('opcache');
			wp_enqueue_style('genericons');
		}

		switch( $hook ) {
			case 'toplevel_page_opcache':
				wp_enqueue_script('opcache');
				wp_enqueue_script('jquery-center');
				wp_enqueue_script('postbox');
			case 'opcache_page_opcache-scripts':
			case 'opcache_page_opcache-status':
			case 'opcache_page_opcache-config':
			case 'opcache_page_opcache-manual':
		}
	}

	function load_view( $template, $data = array() ) {
		$views_dir = self::$PLUGIN_DIR . 'views/';
		if( file_exists( $views_dir . $template ) ) {
			require_once( $views_dir . $template );
			return true;
		}
		error_log( "OPcache Dashboard: Unable to find view file $views_dir$template" );
		return false;
	}

	function render_admin_page() {
		$screen = get_current_screen();

		if( isset( $_GET['action'] ) && isset( $_GET['_wpnonce'] ) && check_admin_referer('opcache_ctrl','_wpnonce') ) {
			$url = sprintf('admin.php?page=%1$s', $_REQUEST['page'] );
			$url = is_network_admin() ? network_admin_url( $url ) : admin_url( $url );

			switch($_GET['action']) {
				case 'reset':
					opcache_reset();
					break;
				case 'invalidate':
					$status = opcache_get_status();
					foreach( $status['scripts'] as $script )
						opcache_invalidate( $script['full_path'] );
					break;
				case 'invalidate_force':
					$status = opcache_get_status();
					foreach( $status['scripts'] as $script )
						opcache_invalidate( $script['full_path'], true);
					break;
			}

			wp_safe_redirect($url);
		}
		$config = $this->data['config'] = opcache_get_configuration();
		$status = $this->data['status'] = opcache_get_status(false);

		add_meta_box(
			'version-info',					// widget_id
			sprintf(
				'PHP: %1$s and OPcache: %2$s',
				phpversion(),
				$config['version']['version']
			),						// widget_name
			array( &$this, 'render_widget_version_info'),	// callback
			$screen->id,					// screen
			'normal'					// location
		);

		add_meta_box(
			'ctrl',						// widget_id
			esc_html__('Reset/Invalidate', 'opcache'),	// widget_name
			array( &$this, 'render_widget_ctrl'),		// callback
			$screen->id,					// screen
			'normal'					// location
		);

		add_meta_box(
			'info-widget',					// widget_id
			esc_html__('Information', 'opcache'),		// widget_name
			array( &$this, 'render_widget_info_widget'),	// callback
			$screen->id,					// screen
			'normal'					// location
		);

		add_meta_box(
			'graphbox',				// widget_id
			'Status Graph',				// widget_name
			array( &$this, 'render_widget_graph'),	// callback
			$screen->id,				// screen
			'side'					// location
		);

		$data = array(
			'screen' => $screen,
			'status' => $status,
		);

		$this->load_view('admin.php', $data );
	}

	function render_widget_version_info() {
		$this->load_view('widgets/version-info.php', $this->data );
	}

	function render_widget_ctrl() {
		$this->load_view('widgets/ctrl.php');
	}

	function render_widget_info_widget() {
		$this->load_view('widgets/info.php');
	}

	function render_widget_graph() {
		$this->load_view('widgets/graph.php');
	}

	function render_admin_status_page() {
		$raw_status = opcache_get_status( false );

		require_once( self::$PLUGIN_DIR . 'class.status-list-table.php');
		foreach( $raw_status as $key => $value ) {
			if( $key === 'scripts')
				continue;

			if( is_bool( $value ) )
				$value = ( $value === true ) ? 'true' : 'false';

			if( is_array( $value ) ) {
				foreach( $value as $k => $v ) {
					if( is_bool( $v ) ) $v = ( $v === true ) ? 'true' : 'false';
					$status[] = array('name' => $k, 'value' => $v );
				}
			} else
				$status[] = array('name' => $key, 'value' => $value );
		}
		$list_table = new OPcache_List_Table( $status );
		$list_table->prepare_items();

		$this->load_view('admin-status.php', $list_table );

	}

	function render_admin_scripts_page() {
		$status = opcache_get_status();

		require_once( self::$PLUGIN_DIR . 'class.script-list-table.php');
		$list_table = new OPcache_List_Table( $status['scripts'] );
		$list_table->prepare_items();

		$this->load_view('admin-scripts.php', $list_table );
	}

	function render_admin_config_page() {
		$raw_config = opcache_get_configuration();

		require_once( self::$PLUGIN_DIR . 'class.config-list-table.php');
		foreach( $raw_config as $key => $value ) {
			if( is_array( $value ) ) {
				foreach( $value as $k => $v ) {
					if( is_bool( $v ) ) $v = ( $v === true ) ? 'true' : 'false';
					$config[] = array('name' => $key.'.'.$k, 'value' => $v );
				}
			}
		}
		$list_table = new OPcache_List_Table( $config );
		$list_table->prepare_items();

		$this->load_view('admin-config.php', $list_table );
	}

	function render_admin_manual_page() {
		if(isset( $_POST['action'] ) && isset( $_POST['_wpnonce'] ) && check_admin_referer('opcache_ctrl','_wpnonce') ) {
			switch( $_POST['action'] ) {
				case 'compile':
					if( isset( $_POST['file'] ) && file_exists( $_POST['file'] ) && !is_dir( $_POST['file'] ) ) {
						if( version_compare( PHP_VERSION, '5.5.11') < 0 or !opcache_is_script_cached( $_POST['file'] ) ) {
							opcache_compile_file( $_POST['file'] );
							printf('<div class="updated"><p>%s</p></div>', esc_html__('Compiled!', 'opcache') );
						} else
							printf('<div class="error"><p>%s</p></div>', esc_html__('The script is already cached.', 'opcache') );
					} else
						printf('<div class="error"><p>%s</p></div>', esc_html__('No such file or directory.', 'opcache') );
					break;
				case 'invalidate':
					if( isset( $_POST['file'] ) && file_exists( $_POST['file'] ) && !is_dir( $_POST['file'] ) ) {
						if( version_compare( PHP_VERSION, '5.5.11') < 0 or opcache_is_script_cached( $_POST['file'] ) ) {
							if( isset( $_POST['force'] ) && $_POST['force'] == 'on') {
								opcache_invalidate( $_POST['file'], true );
								printf('<div class="updated"><p>%s</p></div>', esc_html__('Force Invalidated!', 'opcache') );
							} else {
								opcache_invalidate( $_POST['file'] );
								printf('<div class="updated"><p>%s</p></div>', esc_html__('Invalidated!', 'opcache') );
							}
						} else
							printf('<div class="error"><p>%s</p></div>', esc_html__('The script is not cached yet.', 'opcache') );
					} else
						printf('<div class="error"><p>%s</p></div>', esc_html__('No such file or directory.', 'opcache') );
					break;
			}
		}

		$this->load_view('admin-manual.php');
	}

	function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'opcache_graph',				// slug
			esc_html__('OPcahce Status', 'opcache'),	// title
			array( &$this, 'render_dashboard_widget')	// display function
		);
	}

	function render_dashboard_widget() {
		$this->data['config'] = opcache_get_configuration();
		$this->data['status'] = opcache_get_status(false);

		$this->load_view('widgets/dashboard.php', $this->data );
	}

	static function plugin_textdomain() {
		load_plugin_textdomain('opcache', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
	}

	static function size( $size ) {
		$si_units = array('', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
		$i = 0;
		while( $size >= 1024 && $i < count( $si_units ) ) {
			$size = round( $size / 1024, 2);
			$i++;
		}

		return OPcache_dashboard::number_format( $size ) . $si_units[ $i ] . 'B';
	}

	static function number_format( $number, $decimals = 2) {
		return number_format( $number, $decimals, '.', ',');
	}

	function plugin_row_meta( $links, $file ) {
		if( plugin_basename( __FILE__ ) === $file ) {
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				( is_network_admin() ? network_admin_url('admin.php?page=opcache') : admin_url('admin.php?page=opcache') ),
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
