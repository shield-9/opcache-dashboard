<?php
/*
 * Plugin Name: OPcache Dashboard
 * Plugin URI: http://wordpress.org/plugins/opcache/
 * Description: OPcache dashboard designed for WordPress
 * Version: 0.1.0
 * Author: Daisuke Takahashi(Extend Wings)
 * Author URI: http://www.extendwings.com
 * License: AGPLv3 or later
 * Text Domain: opcache-dashboard
 * Domain Path: /languages/
*/

if(!function_exists('add_action')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}



class opcache_dashboard {
	private function __construct() {
		if(current_user_can('update_core')) {
			if(is_admin())
				add_action('network_admin_menu', array($this, 'add_admin_menu'));
			if(is_multisite() && is_network_admin())
				add_action('admin_menu', array($this, 'add_admin_menu'));
		}
	}

	function add_admin_menu() {
		add_utility_page(
			__('OPcache Dashboard', 'opcache'),	//page_title
			__('OPcache', 'opcache'),		//menu_title
			'update_core',				//capability
			'opcache',				//menu_slug
			array($this, 'admin_page'),		//function
			'dashicons-backup'			//icon_url
		);
	}

	function admin_page() {
		
	}
}

?>
