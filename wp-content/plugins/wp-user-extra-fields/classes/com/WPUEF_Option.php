<?php class WPUEF_Option
{
	public function __construct()
	{
		if(is_admin())
		{
			add_action( 'wp_ajax_wpuef_save_fields', array(&$this, 'ajax_save_fields') );
		}
	}
	static function get_option($option_name = null, $unserialize = true)
	{
		$options = get_option( 'wpuef_options'); 
		$options = isset($options) ? $options: null;
		
		$result = null;
		if($option_name)
		{
			if(isset($options[$option_name]))
				$result = $options[$option_name];
			
			if($unserialize && $option_name == 'json_fields_string' && isset($result))
			{
				$result = json_decode(stripcslashes($result));
				//WPML
				if (class_exists('SitePress'))
					foreach($result->fields as $extra_field)
					{
						$extra_field->label = apply_filters( 'wpml_translate_single_string', $extra_field->label, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid, ICL_LANGUAGE_CODE  );
						if(isset($extra_field->field_options->options))
								foreach($extra_field->field_options->options as $index => $extra_option)
								{
									if(isset($extra_option->label))
										$extra_option->label = apply_filters( 'wpml_translate_single_string', $extra_option->label, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid."_sublabel_".$index, ICL_LANGUAGE_CODE  );
										
								}
						if( isset($extra_field->field_options->description))
							$extra_field->field_options->description = apply_filters( 'wpml_translate_single_string', $extra_field->field_options->description, 'wp-user-extra-fields', 'wpuef_'.$extra_field->cid.'_description', ICL_LANGUAGE_CODE  );
					}
			}
		}
		else
			$result = $options;
		
		
		return $result;
	}
	public function save_general_options($options)
	{
		update_option('wpuef_general_options', $options);
	}
	public function get_general_options()
	{
		$options = get_option('wpuef_general_options');
		return $options;
	}
	public function get_woocommerce_checkout_page_positioning()
	{
		$options = get_option('wpuef_general_options');
		$position = isset($options['woocommerce_checkout_page_positioning']) ? $options['woocommerce_checkout_page_positioning'] : 'woocommerce_after_checkout_billing_form';
		return $position;
	}
	public function get_woocommerce_register_page_positioning()
	{
		$options = get_option('wpuef_general_options');
		$position = isset($options['woocommerce_register_page_positioning']) ? $options['woocommerce_register_page_positioning'] : 'register_form';
		return $position;
	}
	public function get_date_format($php_format = false)
	{
		$options = get_option('wpuef_general_options');
		if(!$php_format)
			return isset($options['date_format']) ? $options['date_format'] : "dd/mm/yyyy";
		$format = "d/m/Y";
		if(isset($options['date_format']) )
		{
			switch($options['date_format'])
			{
				case 'dd/mm/yyyy': $format = "d/m/Y"; break;
				case 'mm/dd/yyyy': $format = "m/d/Y"; break;
				case 'dd.mm.yyyy': $format = "d.m.Y"; break;
				case 'mm.dd.yyyy': $format = "m.d.Y"; break;
				case 'dd-mm-yyyy': $format = "d-m-Y"; break;
				case 'mm-dd-yyyy': $format = "m-d-Y"; break;
			}
		}
		return $format;
	}
	public function save_option($option_name, $value)
	{
		global $wpuef_user_model;
		$options = get_option( 'wpuef_options');
		if(isset($value))
		{
			if($option_name == 'json_fields_string')
			{
				$old_fields = json_decode(stripcslashes($options[$option_name]));
				$new_fields = json_decode(stripcslashes($value));
				$deleted_fields = $wpuef_user_model->delete_unused_fields( $old_fields->fields, $new_fields->fields);
				
				//WPML managment
				foreach($new_fields->fields as $new_field)
				{
					//Register new string
					if(class_exists('SitePress'))
					{
						do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid, $new_field->label );
						if(isset($new_field->field_options->options))
							foreach($new_field->field_options->options as $index => $extra_option)
							{
								if(isset($extra_option->label))
									do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid."_sublabel_".$index, $extra_option->label );
								
							}
						if(isset($new_field->field_options->description))
									do_action( 'wpml_register_single_string', 'wp-user-extra-fields', 'wpuef_'.$new_field->cid.'_description', $new_field->field_options->description );
					}
				}
				//Unregister deleted string 
				foreach($deleted_fields as $old_field)
					if (function_exists ( 'icl_unregister_string' ) && class_exists('SitePress'))
					{
						icl_unregister_string ( 'wp-user-extra-fields', 'wpuef_'.$old_field->cid );
						if(isset($old_field->field_options->options))
							foreach($old_field->field_options->options as $index => $extra_option)
							{
								if(isset($extra_option->label))
									icl_unregister_string ( 'wp-user-extra-fields','wpuef_'.$old_field->cid."_sublabel_".$index);
							}
						if(isset($old_field->field_options->description))
								icl_unregister_string ( 'wp-user-extra-fields', 'wpuef_'.$old_field->cid.'_description');
					}
				//End WPML managment
			}
			$options[$option_name] = $value;
		}
		if(is_admin())
			update_option( 'wpuef_options', $options);
	}
	public function ajax_save_fields()
	{
		if(isset($_POST['json_fields_string']))
			$this->save_option('json_fields_string', $_POST['json_fields_string']);
		wp_die();
	}
	
}
?>