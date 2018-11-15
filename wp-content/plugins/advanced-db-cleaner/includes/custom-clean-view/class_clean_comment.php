<?php
/** Used to view Pending comments, Spam Comments and Trash comments */
class ADBC_Clean_Comment extends WP_List_Table {

	private $aDBc_message = "";
	private $aDBc_class_message = "updated";
	private $aDBc_elements_to_display = array();
	private $aDBc_type_to_clean = "";
	private $aDBc_plural_title = "";

    /**
     * Constructor
     */
    function __construct($element_type){
	
		if($element_type == "moderated-comments"){
			$this->aDBc_type_to_clean = "0";
			$aDBc_singular = __('Pending comment', 'advanced-db-cleaner');
			$this->aDBc_plural_title = __('Pending comments', 'advanced-db-cleaner');
		}else if($element_type == "spam-comments"){
			$this->aDBc_type_to_clean = "spam";
			$aDBc_singular = __('Spam comment', 'advanced-db-cleaner');
			$this->aDBc_plural_title = __('Spam comments', 'advanced-db-cleaner');
		}else if($element_type == "trash-comments"){
			$this->aDBc_type_to_clean = "trash";
			$aDBc_singular = __('Trash comment', 'advanced-db-cleaner');
			$this->aDBc_plural_title = __('Trash comments', 'advanced-db-cleaner');		
		}

        parent::__construct(array(
            'singular'  => $aDBc_singular,		//singular name of the listed records
            'plural'    => $this->aDBc_plural_title,	//plural name of the listed records
            'ajax'      => false	//does this table support ajax?
		));

		$this->aDBc_prepare_elements_to_clean();
		$this->aDBc_print_page_content();
    }

	/** Prepare elements to display */
	function aDBc_prepare_elements_to_clean(){
		global $wpdb;
		// Process bulk action if any before preparing elements to clean
		$this->process_bulk_action();
		// Get all elements to clean (Pending comments, Spam Comments or Trash comments)
		if(function_exists('is_multisite') && is_multisite()){
			$blogs_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blogs_ids as $blog_id){
				switch_to_blog($blog_id);
				$aDBc_all_elements = $wpdb->get_results("SELECT comment_ID, comment_author, comment_content, comment_date FROM $wpdb->comments WHERE comment_approved = '" . $this->aDBc_type_to_clean . "'");
				foreach($aDBc_all_elements as $element){
					array_push($this->aDBc_elements_to_display, array(
						'comment_id' 		=> $element->comment_ID,
						'comment_author' 	=> esc_html($element->comment_author),
						'comment_content' 	=> esc_html($element->comment_content),
						'comment_date'		=> $element->comment_date,
						'site_id'			=> $blog_id
						)
					);
				}
				restore_current_blog();
			}
		}else{
			$aDBc_all_elements = $wpdb->get_results("SELECT comment_ID, comment_author, comment_content, comment_date FROM $wpdb->comments WHERE comment_approved = '" . $this->aDBc_type_to_clean . "'");
			foreach($aDBc_all_elements as $element){
				array_push($this->aDBc_elements_to_display, array(
					'comment_id' 		=> $element->comment_ID,
					'comment_author' 	=> esc_html($element->comment_author),
					'comment_content' 	=> esc_html($element->comment_content),
					'comment_date'		=> $element->comment_date,
					'site_id'			=> "1"
					)
				);
			}
		}
		// Call WP prepare_items function
		$this->prepare_items();
	}

	/** WP: Get columns */
	function get_columns(){
		$columns = array(
			'cb'       			=> '<input type="checkbox" />',
			'comment_id' 		=> __('ID','advanced-db-cleaner'),
			'comment_author' 	=> __('Author','advanced-db-cleaner'),
			'comment_content'   => __('Content','advanced-db-cleaner'),
			'comment_date'   	=> __('Date','advanced-db-cleaner'),
			'site_id'   		=> __('Site id','advanced-db-cleaner')
		);
		return $columns;
	}

	/** WP: Column default */
	function column_default($item, $column_name){
		switch($column_name){
			case 'comment_id':
			case 'comment_author':
			case 'comment_content':
			case 'comment_date':
			case 'site_id':
				return $item[$column_name];
			default:
			  return print_r($item, true) ; //Show the whole array for troubleshooting purposes
		}
	}

	/** WP: Get columns that should be hidden */
    function get_hidden_columns(){
		// If MU, nothing to hide, else hide Side ID column
		if(function_exists('is_multisite') && is_multisite()){
			return array();
		}else{
			return array('site_id');
		}
    }

	/** WP: Prepare items to display */
	function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = array();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$per_page = 50;
		$current_page = $this->get_pagenum();
		// Prepare sequence of elements to display
		$display_data = array_slice($this->aDBc_elements_to_display,(($current_page-1) * $per_page), $per_page);
		$this->set_pagination_args( array(
			'total_items' => count($this->aDBc_elements_to_display),
			'per_page'    => $per_page
		));
		$this->items = $display_data;
	}

	/** WP: Column cb for check box */
	function column_cb($item) {
		return sprintf('<input type="checkbox" name="aDBc_elements_to_clean[]" value="%s" />', $item['site_id']."|".$item['comment_id']);
	}

	/** WP: Get bulk actions */
	function get_bulk_actions() {
		$actions = array(
			'clean'    => __('Clean','advanced-db-cleaner')
		);
		return $actions;
	}

	/** WP: Message to display when no items found */
	function no_items() {
		_e('No comments found!','advanced-db-cleaner');
	}

	/** WP: Process bulk actions */
    public function process_bulk_action() {
        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])){
            $nonce  = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-' . $this->_args['plural'];
            if (!wp_verify_nonce( $nonce, $action))
                wp_die('Security check failed!');
        }
        $action = $this->current_action();
        if($action == 'clean'){
			// If the user wants to clean the elements he/she selected
			if(isset($_POST['aDBc_elements_to_clean'])){
				global $wpdb;
				if(function_exists('is_multisite') && is_multisite()){
					// Prepare elements to delete
					$elements_to_delete = array();
					foreach($_POST['aDBc_elements_to_clean'] as $element){
						$element_info = explode("|", $element);
						if(empty($elements_to_delete[$element_info[0]])){
							$elements_to_delete[$element_info[0]] = array();
						}
						array_push($elements_to_delete[$element_info[0]], $element_info[1]);
					}
					// Delete elements
					foreach($elements_to_delete as $site_id => $elements_ids){
						switch_to_blog($site_id);
						foreach($elements_ids as $id_comment) {
							$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_ID = $id_comment and comment_approved = '" . $this->aDBc_type_to_clean . "'");
						}
						restore_current_blog();
					}
				}else{
					foreach($_POST['aDBc_elements_to_clean'] as $element) {
						$element_info = explode("|", $element);
						$wpdb->query("DELETE FROM $wpdb->comments WHERE comment_ID = " . $element_info[1] . " and comment_approved = '" . $this->aDBc_type_to_clean . "'");
					}
				}
				// Update the message to show to the user
				$this->aDBc_message = __('Selected comments successfully cleaned!', 'advanced-db-cleaner');
			}
        }
    }

	/** Print the page content */
	function aDBc_print_page_content(){
		// Print a message if any
		if($this->aDBc_message != ""){
			echo '<div id="aDBc_message" class="' . $this->aDBc_class_message . ' notice is-dismissible"><p>' . $this->aDBc_message . '</p></div>';
		}
		?>
		<div class="aDBc-content-max-width">
			<div class="aDBc-float-left">
				<a style="text-decoration: none" href="?page=advanced_db_cleaner&aDBc_tab=general">
					<img src="<?php echo ADBC_PLUGIN_DIR_PATH . '/images/go_back.png'?>"/>
				</a>
			</div>
			<div class="aDBc-float-right aDBc-custom-clean-text">
				<?php echo __('Custom cleaning : ','advanced-db-cleaner') . '<b>' . $this->aDBc_plural_title . '</b>' . " (" . count($this->aDBc_elements_to_display) . ")"; ?>
			</div>
			<div>
				<form id="aDBc_form" action="" method="post">
					<?php
					// Print the elements to clean
					$this->display();
					?>
				</form>
			</div>
		</div>
		<div id="aDBc_dialog1" title="<?php _e('Cleaning...','advanced-db-cleaner'); ?>" class="aDBc-jquery-dialog">
			<p class="aDBc-box-warning">
				<?php 
				if($this->aDBc_type_to_clean == "0"){
					_e('You are about to clean some of your pending comments. This operation is irreversible. Don\'t forget to make a backup first.','advanced-db-cleaner');
				}else if($this->aDBc_type_to_clean == "spam"){
					_e('You are about to clean some of your spam comments. This operation is irreversible. Don\'t forget to make a backup first.','advanced-db-cleaner');
				}else if($this->aDBc_type_to_clean == "trash"){
					_e('You are about to clean some of your trash comments. This operation is irreversible. Don\'t forget to make a backup first.','advanced-db-cleaner');
				}
				?>
			</p>
			<p>
				<?php _e('Are you sure to continue?','advanced-db-cleaner'); ?>
			</p>
		</div>		
		<div id="aDBc_dialog2" title="<?php _e('Action required','advanced-db-cleaner'); ?>" class="aDBc-jquery-dialog">
			<p class="aDBc-box-info">
				<?php _e('Please select an action!','advanced-db-cleaner'); ?>
			</p>
		</div>		
	<?php
	}
}
?>