<?php 
class WPUEF_UsersTable
{
	public function __construct()
	{
		add_filter( 'manage_users_columns', array(&$this,'add_column') );
		add_filter( 'manage_customers_columns', array(&$this,'add_column') );
		add_filter( 'manage_users_custom_column', array(&$this,'insert_colum_data'), 10, 3 );
		add_filter( 'manage_customers_custom_column', array(&$this,'insert_colum_data'), 10, 3 );

	}
	function add_column( $columns ) 
	{
		global $wpuef_option_model;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if( isset($extra_field->add_field_to_user_table_colum) && ($extra_field->add_field_to_user_table_colum))
				{
					$columns['wpuef_'.$extra_field->cid] = $extra_field->label;
				}
	
		return $columns;
	}
	function insert_colum_data( $val, $column_name, $user_id ) {
		//$user = get_userdata( $user_id );
		global $wpuef_option_model,$wpuef_shortcodes;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		
		if($extra_fields)
			foreach($extra_fields->fields as $extra_field)
				if($column_name == 'wpuef_'.$extra_field->cid)
					return $wpuef_shortcodes->wpuef_show_field_value(array('user_id' => $user_id, 'field_id' =>$extra_field->cid));
						
		return "";
	}
}
?>