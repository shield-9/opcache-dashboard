<?php
class OPcache_List_Table extends WP_List_Table {
	public $data = array ();

	function __construct($data) {
		global $status, $page;
		$this->data = $data;

		parent::__construct(array(
			'singular'	=> 'script',
			'plural'	=> 'scripts',
			'ajax'		=> false
		));
	}

	function get_columns() {
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'script_path'		=> __('Script Path', 'opcache'),
			'hits'			=> _x('Hits', 'Number of hits', 'opcache'),
			'memory_consumption'	=> __('Memory Consumption', 'opcache'),
			'invalidate'		=> __('Invalidate', 'opcache')
		);
		return $columns;
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],	//Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['full_path']		//The value of the checkbox should be the record's id
		);
	}

	function column_script_path($item){
		$actions = array(
		//	'edit'	  => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
		//	'delete'	=> sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
		);
		return sprintf('%1$s %2$s', $item['full_path'], $this->row_actions($actions));
	}

	function column_default($item, $column_name){
		switch($column_name) {
			case "invalidate":
				return get_submit_button(
					__('Invalidate', 'opcache'),
					'primary delete',
					'invalidate['.$item['full_path'].']',
					false,
					array('id' => '')
				);
			default:
				return $item[$column_name];
		}
	}

	function get_bulk_actions() {
		$actions = array(
			'invalidate'	=> __('Invalidate', 'opcache')
		);
		return $actions;
	}

	function process_bulk_action() {
		if('invalidate'===$this->current_action()) {
			wp_die('Items deleted (or they would be if we had items to delete)!');
		}
	}

	function prepare_items() {
		$per_page = 5;

		$columns = $this->get_columns();
		$hidden = array();
		// $sortable = $this->get_sortable_columns();
		$sortable = array();

		$this->_column_headers = array($columns, $hidden, $sortable);

		// $this->process_bulk_action();

		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 * 
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 * 
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/

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
