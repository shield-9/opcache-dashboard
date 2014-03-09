<?php
class OPcache_List_Table extends WP_List_Table {
	public $data = array();

	function __construct($data) {
		global $status, $page;
		$this->data = $data;

		parent::__construct(array(
			'singular'	=> 'status',
			'plural'	=> 'status',
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
			'name'	=> __('Status Name', 'opcache'),
			'value'	=> _x('Value', 'Value of Status', 'opcache')
		);
		return $columns;
	}

	function column_value($item){
		switch($item['name']) {
			case 'start_time':
			case 'last_restart_time':
				return $item['value'] ? date(__('j M, Y @ G:i:s', 'opcache'), $item['value']) : 'never';
			case 'used_memory':
			case 'free_memory':
			case 'wasted_memory':
				return OPcache_dashboard::size($item['value']);
			case 'current_wasted_percentage':
			case 'blacklist_miss_ratio':
			case 'opcache_hit_rate':
				return OPcache_dashboard::number_format($item['value']) . '%';
			default:
				return $item['value'];
		}
	}

	function column_default($item, $column_name){
		switch($column_name) {
			default:
				return $item[$column_name];
		}
	}

	function prepare_items() {
		$per_page = 50;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();

		$this->_column_headers = array($columns, $hidden, $sortable);

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

?>