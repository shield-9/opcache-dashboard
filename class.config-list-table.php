<?php
class OPcache_List_Table extends WP_List_Table {
	public $data = array();

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
				//echo 'Extra Table Navigation(Top)';
				break;
			case 'bottom':
				//echo 'Extra Table Navigation(Bottom)';
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
				'<a href="%1$s/opcache.configuration#ini.%2$s" title="%3$s"><span class="genericon genericon-info"></span></a>',
				OPcache_dashboard::$php_url,
				str_replace(array('directives.', '_'), array(NULL, '-'), $item['name']),
				__('PHP.net Document')
			);
		} else
			$manual = NULL;

		switch($item['name']) {
			case 'directives.opcache.enable':
				if($item['value']!=='true') $actions['notice'] = __('You should enabled opcache');
				break;
			case 'directives.opcache.validate_timestamps':
				if($item['value']==='true') {
					$actions['notice'] = sprintf(
						'<a href="%1$s/opcache.installation#opcache.installation.recommended" title="%2$s">%3$s</a>',
						OPcache_dashboard::$php_url,
						__('Recommended Settings'),
						__('If you are in a production environment you should disabled it')
					);
				}
				break;
		}
		return sprintf('<strong><span class="row-title">%1$s</span></strong> %2$s %3$s', $item['name'], $manual, $this->row_actions($actions));
	}

	function column_value($item) {
		switch($item['name']) {
			case 'directives.opcache.memory_consumption':
				return OPcache_dashboard::size($item['value']);
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

?>
