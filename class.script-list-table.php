<?php
class OPcache_List_Table extends WP_List_Table {
	private $data = array();

	function __construct($data) {
		global $status, $page;
		$this->data = $data;

		parent::__construct(array(
			'singular'	=> 'script',
			'plural'	=> 'scripts',
			'ajax'		=> false
		));
	}

	function extra_tablenav($which) {
		switch($which) {
			case 'top':
				//esc_html_e('Extra Table Navigation(Top)');
				break;
			case 'bottom':
				//esc_html_e('Extra Table Navigation(Bottom)');
				break;
		}
	}

	function get_columns() {
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'script_path'		=> __('Script Path', 'opcache'),
			'hits'			=> _x('Hits', 'Number of hits', 'opcache'),
			'memory_consumption'	=> __('Memory Consumption', 'opcache'),
			'last_used_timestamp'	=> __('Last Used', 'opcache'),
			'timestamp'		=> __('Timestamp', 'opcache'),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'script_path'		=> array('full_path', true),
			'hits'			=> array('hits', false),
			'memory_consumption'	=> array('memory_consumption', false),
			'last_used_timestamp'	=> array('last_used_timestamp', false),
			'timestamp'		=> array('timestamp', false)
		);
		return $sortable_columns;
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['full_path']);
	}

	function column_script_path($item){
		return sprintf('<strong><span class="row-path">%1$s</span></strong>', $item['full_path']);
	}

	function column_default($item, $column_name){
		switch($column_name) {
			case 'last_used_timestamp':
			case 'timestamp':
				return isset($item[$column_name]) ? date(__('j M, Y @ G:i:s', 'opcache'), $item[$column_name]) : __('Not Set', 'opcache');
			case 'memory_consumption':
				return OPcache_dashboard::size($item[$column_name]);
			default:
				return $item[$column_name];
		}
	}

	function get_bulk_actions() {
		$actions = array(
			'invalidate'		=> __('Invalidate', 'opcache'),
			'invalidate_force'	=> __('Force Invalidate', 'opcache')
		);
		return $actions;
	}

	function process_bulk_action() {
		if(!isset($_GET['script']) or !is_array($_GET['script']))
			return false;
		switch($this->current_action()) {
			case 'invalidate':
				foreach($_GET['script'] as $script)
					opcache_invalidate($script);
				break;
			case 'invalidate_force':
				foreach($_GET['script'] as $script)
					opcache_invalidate($script, true);
				break;
		}
	}

	function prepare_items() {
		$per_page = 20;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		function usort_reorder($a,$b){
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'full_path'; //If no sort, default to title
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
			$result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
			return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}

		usort($this->data, 'usort_reorder');

		$current_page = $this->get_pagenum();

		$total_items = count($this->data);

		$this->data = array_slice($this->data,(($current_page-1)*$per_page),$per_page);

		$this->items = $this->data;

		$this->set_pagination_args(array(
			'total_items'	=> $total_items,
			'per_page'	=> $per_page,
			'total_pages'	=> ceil($total_items/$per_page)
		));
	}
}
