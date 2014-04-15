<?php
class OPcache_List_Table extends WP_List_Table {
	private $data = array();

	function __construct($data) {
		global $status, $page;
		$this->data = $data;

		parent::__construct(array(
			'singular'	=> 'config',
			'plural'	=> 'configs',
			'ajax'		=> false
		));
	}

	function extra_tablenav($which) {
		switch($which) {
			case 'top':
				//esc_html_e('Extra Table Navigation(Top)', 'opcache');
				break;
			case 'bottom':
				//esc_html_e('Extra Table Navigation(Bottom)', 'opcache');
				break;
		}
	}

	function get_columns() {
		$columns = array(
			'name'	=> __('Config Name', 'opcache'),
			'value'	=> _x('Value', 'Value of Config', 'opcache')
		);
		return $columns;
	}

	function column_name($item) {
		$actions = NULL;
		if(strpos($item['name'], 'directives.')===0) {
			$manual = sprintf(
				'<a href="%1$s/opcache.configuration#ini.%2$s" title="%3$s" target="_blank"><span class="genericon genericon-info"></span></a>',
				OPcache_dashboard::PHP_URL,
				str_replace(array('directives.', '_'), array(NULL, '-'), $item['name']),
				__('PHP.net Document')
			);
		} else
			$manual = NULL;

		switch($item['name']) {
			case 'directives.opcache.enable':
				if($item['value']!=='true') $actions['notice'] = __('You should enabled opcache');
				break;
			case 'directives.opcache.enable_cli':
				if($item['value']!=='true') {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should enabled it')
					);
				}
				break;
			case 'directives.opcache.memory_consumption':
				if($item['value'] < 134217728) {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should set larger than 128.00MB')
					);
				}
				break;
			case 'directives.opcache.interned_strings_buffer':
				if($item['value'] < 8) {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should set larger than 8.00MB')
					);
				}
				break;
			case 'directives.opcache.max_accelerated_files':
				if($item['value'] < 4000) {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should set greater than 4000')
					);
				}
				break;
			case 'directives.opcache.max_wasted_percentage':
				break;
			case 'directives.opcache.use_cwd':
				break;
			case 'directives.opcache.validate_timestamps':
				if($item['value']==='true') $actions['notice'] = __('If you are in a production environment you should disabled it');
				break;
			case 'directives.opcache.revalidate_freq':
				if($item['value'] < 60) {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should set longer than 60 sec.')
					);
				}
				break;
			case 'directives.opcache.revalidate_path':
				break;
			case 'directives.opcache.save_comments':
				if($item['value']!=='true') {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should enabled it')
					);
				}
				break;
			case 'directives.opcache.load_comments':
				break;
			case 'directives.opcache.fast_shutdown':
				if($item['value']!=='true') {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should enabled it')
					);
				}
				break;
			case 'directives.opcache.enable_file_override':
				if($item['value']==='true') {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s" target="_blank">%3$s</a>',
						OPcache_dashboard::PHP_URL,
						__('Recommended Settings'),
						__('If you are in a production environment you should disabled it')
					);
				}
				break;
			case 'directives.opcache.optimization_level':
				break;
			case 'directives.opcache.inherited_hack':
				break;
			case 'directives.opcache.dups_fix':
				break;
			case 'directives.opcache.blacklist_filename':
				break;
			case 'directives.opcache.max_file_size':
				break;
			case 'directives.opcache.consistency_checks':
				if($item['value']!==0) {
					$actions['notice'] = __('If you are in a production environment you should disabled it');
				}
				break;
			case 'directives.opcache.force_restart_timeout':
				break;
			case 'directives.opcache.error_log':
				break;
			case 'directives.opcache.log_verbosity_level':
				break;
			case 'directives.opcache.preferred_memory_model':
				break;
			case 'directives.opcache.protect_memory':
				break;
			case 'directives.opcache.mmap_base':
				break;
		}
		return sprintf('<strong><span class="row-name">%1$s</span></strong> %2$s %3$s', $item['name'], $manual, $this->row_actions($actions));
	}

	function column_value($item) {
		switch($item['name']) {
			case 'directives.opcache.memory_consumption':
				return OPcache_dashboard::size($item['value']);
			case 'directives.opcache.interned_strings_buffer':
				return OPcache_dashboard::size($item['value']*1024*1024);
			case 'directives.opcache.max_wasted_percentage':
				return OPcache_dashboard::number_format($item['value']) . '%';
			default:
				return $item['value'];
		}
	}

	function column_default($item, $column_name) {
		switch($column_name) {
			default:
				return $item[$column_name];
		}
	}

	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$current_page = $this->get_pagenum();

		$total_items = count($this->data);

		$this->items = $this->data;

		$this->set_pagination_args(array(
			'total_items'	=> $total_items,
			'per_page'	=> $total_items,
			'total_pages'	=> 1
		));
	}
}
