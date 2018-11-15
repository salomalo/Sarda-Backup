<?php class WPUEF_HtmlHelper
{
	var $extra_fields_custom_from_rendered_at_least_one_time = false;
	public function __construct()
	{
		
	}
	//BuddyPress 
	public function buddypress_profile_extra_field_values_table($user_id = null)
	{
		global $wpuef_option_model, $wpuef_shortcodes;
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		$user_id = !isset($user_id) ? get_current_user_id() : $user_id;
		include WPUEF_PLUGIN_ABS_PATH.'/templates/buddypress_profile_extra_field_values_table.php';
	}
	//WP, BuddyPress & WooCommerce Register form
	public function render_register_form_extra_fields()
	{
		global $wpuef_option_model, $wpuef_wpml_helper;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-registration-fields-check', WPUEF_PLUGIN_PATH.'/js/wpuef-registration-fields-check.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));

		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');
	
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/register_form_extra_fields.php';
		}
	}
	public function render_register_form_extra_fields_wccm()
	{
		global $wpuef_option_model, $wpuef_wpml_helper;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));

		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');
		
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_register_form_extra_fields_wccm.php';
		}
	}
	public function woocommerce_render_extra_fields_wccm($user_id = null)
	{
		//$edit_page_type: 0 my account, 1 billing address edit, 2 shipping address edit
		global $wpuef_option_model, $wpuef_user_model;
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_extra_fields_wccm.php'; 
		}
		
	}
	//WP Add new user
	function render_add_table_with_extra_fields()
	{
		$this->render_edit_table_with_extra_fields();
	}
	//WP Edit my account page (admin & frontend and WCCM Edit user)
	function render_edit_table_with_extra_fields($user_id = null, $is_buddypress = false)
	{
		global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));
		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');

		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/edit_table_with_extra_fields.php';
		}
	}
	//Custom extra fields form rendered by shortcode
	function render_extra_fields_custom_form($field_ids, $user_id = null)
	{
		global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 
		wp_enqueue_style('wpuef-extra-fields-custom-form', WPUEF_PLUGIN_PATH.'/css/wpuef-extra-fields-custom-form.css'); 
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css');
		
		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-frontend-extra-fields-custom-form', WPUEF_PLUGIN_PATH.'/js/wpuef-frontend-extra-fields-custom-form.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-frontend-extra-fields-custom-form-ui', WPUEF_PLUGIN_PATH.'/js/wpuef-frontend-extra-fields-custom-form-ui.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));

		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');


		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		$fields_ids = !is_array($field_ids) ? array($field_ids) : $field_ids;
		
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/extra_fields_custom_form.php';
		}
	}
	//WooCommerce Edit my account page - shipping address or billing address edit page (frontend)
	function woocommerce_render_edit_form_extra_fields($user_id = null, $render_form = false, $edit_page_type = 0)
	{
		//$edit_page_type: 0 my account, 1 billing address edit, 2 shipping address edit
		
		//wpuef_var_dump( $edit_page_type);
		global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));

		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');

		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_edit_form_extra_fields.php';
		}
		
	}
	//WooCommerce Checkout page
	function woocommerce_render_checkout_form_extra_fields($user_id = null)
	{
		global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper, $wpuef_woocommerce_is_active;
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-common-validation', WPUEF_PLUGIN_PATH.'/js/wpuef-common-validation.js', array( 'jquery' ));

		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');

		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_checkout_form_extra_fields.php';
		}
		
	}
	//WooCommerce Order details meta box addon (backend)
	function woocommerce_render_order_edit_form_extra_fields($user_id = null)
	{
		global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper;
		
		wp_enqueue_style('wpuef-datepicker-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.css');   
		wp_enqueue_style('wpuef-datepicker-date-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.date.css');   
		wp_enqueue_style('wpuef-datepicker-time-classic', WPUEF_PLUGIN_PATH.'/css/datepicker/classic.time.css');  
		wp_enqueue_style('wpuef-common-styles', WPUEF_PLUGIN_PATH.'/css/wpuef-common-html-styles.css'); 
		wp_enqueue_style('wpuef-order-edit', WPUEF_PLUGIN_PATH.'/css/wpuef-admin-order-details.css'); 

		wp_enqueue_script('wpuef-ui-picker', WPUEF_PLUGIN_PATH.'/js/picker.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-datepicker', WPUEF_PLUGIN_PATH.'/js/picker.date.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-ui-timepicker', WPUEF_PLUGIN_PATH.'/js/picker.time.js', array( 'jquery' ));
		wp_enqueue_script('wpuef-file-manager', WPUEF_PLUGIN_PATH.'/js/wpuef-file-manager.js', array( 'jquery' ));
		//datepicker localization get_locale() 
		if(wpuef_url_exists(WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js'))
			wp_enqueue_script('wpuef-timepicker-localization', WPUEF_PLUGIN_PATH.'/js/time/translations/'.$wpuef_wpml_helper->get_current_language().'.js');

		//$fields_json_string = $wpuef_option_model->get_option('json_fields_string');
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_order_edit_form_extra_fields.php';
		}
		
	}
	//Woocommerce email
	function woocommerce_render_fields_on_emails($user_id)
	{
		global $wpuef_option_model, $wpuef_user_model;
		//$fields_json_string = $wpuef_option_model->get_option('json_fields_string');
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		if($extra_fields)
		{
			include WPUEF_PLUGIN_ABS_PATH.'/templates/woocommerce_email.php';
		} 
	}
}
?>