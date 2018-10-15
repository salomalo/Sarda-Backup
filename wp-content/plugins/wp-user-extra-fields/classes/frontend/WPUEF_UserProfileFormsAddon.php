<?php class WPUEF_UserProfileFormsAddon
{
	var $buddy_press_do_not_render_edit_fields_table = false;
	var $register_form_rendered = 0;
	public function __construct()
	{
		global $wpuef_option_model,$wpuef_woocommerce_is_active;
		$theme_version = wpuef_get_file_version( get_template_directory() . '/woocommerce/myaccount/my-account.php' );
		try{
			$wc_version = wpuef_get_woo_version_number();
		}catch(Exception $e){}
		
		//Registration
		if(isset($wpuef_option_model) && isset($wpuef_woocommerce_is_active) && $wpuef_woocommerce_is_active)  //WooCommerce
			add_action($wpuef_option_model->get_woocommerce_register_page_positioning() , array( &$this, 'render_register_form_with_extra_fields'));
		add_action('register_form',array( &$this, 'render_register_form_with_extra_fields'));
		
		add_action('register_post',array( &$this,'check_fields',10,3));
		add_action('user_register', array( &$this,'register_extra_fields'));
		add_action( 'delete_user', array( &$this,'delete_fields_on_user_delete') );
		
		add_action ( 'show_user_profile', array( &$this,'render_edit_table_with_extra_fields'));
		add_action ( 'edit_user_profile', array( &$this,'render_edit_table_with_extra_fields') );
		add_action ( 'user_new_form', array( &$this,'render_add_table_with_extra_fields') ); //Admin add new user
		add_action ( 'personal_options_update',  array( &$this,'update_extra_fields')); //user is viewing it's own profile
		add_action ( 'edit_user_profile_update', array( &$this,'update_extra_fields')); //user/admin is viewing other profile
		
		//Buddy press
		add_action('bp_before_registration_submit_buttons', array( &$this, 'render_register_form_with_extra_fields'));
		add_action('bp_complete_signup', array( &$this, 'register_extra_fields'));
		//add_action('bp_signup_validate', array( &$this, 'buddypress_check_validation'));
		add_action('bp_profile_field_item', array( &$this, 'buddypress_show_profile_extra_fields'));
					//bp_custom_profile_edit_fields
		add_action('bp_after_profile_field_content', array( &$this, 'buddypress_edit_profile_extra_fields'),999); 
		add_action('xprofile_updated_profile', array( &$this, 'buddypress_update_extra_fields'));
		
		//WooCommerce
		add_action( 'woocommerce_created_customer', array( &$this,'register_extra_fields') );
		add_action( 'woocommerce_edit_account_form', array( &$this,'woocommerce_edit_account_details') );
		add_action( 'woocommerce_save_account_details', array( &$this,'update_extra_fields') );
		add_action( 'woocommerce_customer_save_address', array( &$this,'update_extra_fields') );
		//add_action( 'woocommerce_before_my_account', array( &$this,'woocommerce_edit_and_save_from_account_details_in_my_account_page') );
		if(!isset($wc_version) || version_compare($wc_version , 2.6, '<') || version_compare($theme_version , 2.6, '<') )
			add_action( 'woocommerce_after_my_account', array( &$this,'woocommerce_edit_and_save_from_account_details_in_my_account_page') );		
		add_action( 'woocommerce_after_edit_address_form_billing', array( &$this,'woocommerce_edit_billing_address_details') );
		add_action( 'woocommerce_after_edit_address_form_shipping', array( &$this,'woocommerce_edit_shipping_address_details') );
		//Wc 2.6: Noo more needed => woocommerce_edit_account_details is the only hook used
		/* if(isset($wc_version) && version_compare($wc_version , 2.6, '>=') )
			add_action( 'woocommerce_account_content', array( &$this,'woocommerce_edit_and_save_from_account_details_in_my_account_page'),99 ); */
		
		//Order detail page (admin)
		//add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this,'woocommerce_after_shipping_address_meta_box') );
		//add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this,'woocommerce_custom_checkout_field_display_admin_order_meta'), 10, 1 );		
		add_action( 'woocommerce_admin_order_data_after_order_details', array( &$this,'woocommerce_after_shipping_address_meta_box') ); //Order details
		
		//Checkout
		add_action( 'woocommerce_process_shop_order_meta', array( &$this, 'woocommerce_on_save_order_details_admin_page' ), 5, 2 );//save order	
		if(isset($wpuef_option_model))
			add_action( $wpuef_option_model->get_woocommerce_checkout_page_positioning()/* 'woocommerce_after_checkout_billing_form' */, array( &$this, 'woocommerce_add_checkout_extra_user_fields' ), 10, 1 );
		//add_action('woocommerce_checkout_process', array( &$this, 'woocommerce_validate_fields' )); //validation
		add_action('woocommerce_checkout_update_order_meta', array( &$this, 'woocommerce_save_checkout_extra_user_fields' )); //update user info
			
		//Emails
		add_action('woocommerce_email_customer_details', array( &$this, 'woocommerce_include_extra_fields_in_emails' ), 12, 3);
		
		//Reset password
		add_action('woocommerce_resetpassword_form', array(&$this, 'woocommerce_reset_password'));
	}
	//WooCommerce
	function woocommerce_reset_password()
	{
		global $wpuef_field_model;
		if($wpuef_field_model->exists_password_override_field())
			wp_dequeue_script('wc-password-strength-meter');
	}
	function woocommerce_validate_fields() //data validation
	{
		/*  if ( ! $_POST['my_field_name'] )
			wc_add_notice( __( 'Please enter something into this new shiny field.' ), 'error' ); */
		
	}
	function woocommerce_custom_checkout_field_display_admin_order_meta($order)
	{
		//echo '<p><strong>'.__('My Field').':</strong> ' . get_post_meta( $order->id, 'My Field', true ) . '</p>';
	}
	function woocommerce_save_checkout_extra_user_fields( $order_id ) 
	{
		$order = new WC_Order($order_id);
		$user_id = $order->customer_user;
		if(isset($user_id) && $user_id > 0)
			$this->woocommerce_updata_extra_fields_checkout($user_id);	
	}
	function woocommerce_add_checkout_extra_user_fields($checkout)
	{
		global $wpuef_htmlHelper;
		//wpuef_var_dump($checkout);
		$user_id = get_current_user_id();		
		$wpuef_htmlHelper->woocommerce_render_checkout_form_extra_fields($user_id);
	}
	public function woocommerce_edit_billing_address_details()
	{
		$user_id = get_current_user_id();
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($user_id,false,1);
	}
	public function woocommerce_edit_shipping_address_details()
	{
		$user_id = get_current_user_id();
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($user_id,false,2);
	}
	public function woocommerce_edit_account_details()
	{
		$user_id = get_current_user_id();
		/* $user = get_userdata( $user_id );
		if ( !$user )
			return; */
		
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($user_id);
	}
	public function woocommerce_edit_and_save_from_account_details_in_my_account_page()
	{
		global $wpuef_htmlHelper, $wp;
		$user_id = get_current_user_id();
		if ( !$user_id  )
			return;
		
		//save post
		$this->update_extra_fields($user_id);
		
		//form
		if ( did_action( 'woocommerce_account_content' ) ) 
		{
				//wpuef_var_dump("2.6");
				foreach ( $wp->query_vars as $key => $value ) 
				{
					if($key == 'edit-account')
						$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($user_id, true);
					/* if ( has_action( 'woocommerce_account_' . $key . '_endpoint' ) ) 
					{
						wcmca_var_dump('woocommerce_account_' . $key . '_endpoint' );
					} */
	 			} 
		}
		else if(did_action('woocommerce_after_my_account')) //WC < 2.6
		{
			//wpuef_var_dump("2.5");
			$wpuef_htmlHelper->woocommerce_render_edit_form_extra_fields($user_id, true);
		}
	}
	public function woocommerce_on_save_order_details_admin_page( $post_id, $post )
	{
		$order = new WC_Order($post_id);
		$user_id = $order->customer_user;
		if(isset($user_id) && $user_id > 0)
			$this->update_extra_fields($user_id);
	}
	public function woocommerce_after_shipping_address_meta_box( $order )
	{
		global $wpuef_htmlHelper;
		$user_id = $order->customer_user;
		if(isset($user_id) && $user_id > 0)
			$wpuef_htmlHelper->woocommerce_render_order_edit_form_extra_fields($user_id);
	}
	//End WooCommerce
	
	//BuddyPress
	public function buddypress_show_profile_extra_fields()
	{
		if($this->buddy_press_do_not_render_edit_fields_table)
			return;
		
		$this->buddy_press_do_not_render_edit_fields_table = true;
		
		global $wpuef_htmlHelper;
		$user_id = bp_displayed_user_id();
		if(isset($user_id) && $user_id > 0)
			$wpuef_htmlHelper->buddypress_profile_extra_field_values_table($user_id);
	}
	public function buddypress_edit_profile_extra_fields( )
	{
		if($this->buddy_press_do_not_render_edit_fields_table)
			return;
		
		$this->buddy_press_do_not_render_edit_fields_table = true;
		global $wpuef_htmlHelper;
		$user_id = bp_displayed_user_id();
		if(isset($user_id) && $user_id > 0)
			$wpuef_htmlHelper->render_edit_table_with_extra_fields($user_id, true);
	}
	public function  buddypress_update_extra_fields($user_id, $posted_field_ids, $errors, $old_values, $new_values)
	{
		//wpuef_var_dump($_POST);
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		global $wpuef_user_model;
		if(isset($_POST['wpuef_options']))
			$wpuef_user_model->save_fields($_POST['wpuef_options'], $user_id );
	}
	//End BuddyPress
	
	public function delete_fields_on_user_delete($user_id)
	{
		global $wpuef_file_model;
		$wpuef_file_model->delete_all_user_files($user_id);
	}
	public function render_add_table_with_extra_fields( )
	{
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->render_add_table_with_extra_fields();
	}
	public function render_edit_table_with_extra_fields( $user )
	{
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->render_edit_table_with_extra_fields($user->ID);
	}
	
	public function  render_register_form_with_extra_fields()
	{
		global $wpuef_htmlHelper;
		//if(did_action('register_form') > 1) //Forma already rendered, usefull for woocommerce
		if(++$this->register_form_rendered > 1) //Forma already rendered, usefull for woocommerce
			return;
		$wpuef_htmlHelper->render_register_form_extra_fields();
	}
	public function  woocoommerce_render_register_form_with_extra_fields()
	{
		global $wpuef_htmlHelper;
		$wpuef_htmlHelper->render_register_form_extra_fields();
	}

	public function  check_fields ( $login, $email, $errors )
	{
		
	}
	public function woocommerce_include_extra_fields_in_emails( $order, $sent_to_admin = false, $plain_text = false)
	{
		global $wpuef_htmlHelper;
		$user_id = $order->customer_user;
		if(isset($user_id) && $user_id > 0)
			$wpuef_htmlHelper->woocommerce_render_fields_on_emails($user_id);
	}
	public function woocommerce_updata_extra_fields_checkout($user_id)
	{
		if ( !current_user_can( 'edit_user', $user_id ) || !isset($_POST['wpuef_options']))
			return false;
		
		global $wpuef_user_model,$wpuef_option_model;
		$posted_fields = $_POST['wpuef_options'];
		$extra_fields = $wpuef_option_model->get_option('json_fields_string');
		$customer_fields_to_save = array();
		$order_fields_to_save = array();
		//Customer fields
		foreach($extra_fields->fields as $extra_field)
		{
			if( isset($extra_field->woocommerce_visible_on_checkout) && $extra_field->woocommerce_visible_on_checkout == true )
				 if(isset($posted_fields[$extra_field->cid]))
					 $customer_fields_to_save[$extra_field->cid] = $posted_fields[$extra_field->cid];
				 
			/* if( isset($extra_field->woocommerce_save_on_checkout_as_order_field) && $extra_field->woocommerce_save_on_checkout_as_order_field == true )
				 if(isset($posted_fields[$extra_field->cid]))
					 $order_fields_to_save[$extra_field->cid] = $order_fields_to_save[$extra_field->cid]; */
			
			/* if(isset($_POST['wpuef_options']['files']))
				 $posted_fields['files'] = $_POST['wpuef_options']['files'];
			   if(isset($_POST['wpuef_options']['files_to_delete'])) 
				   $posted_fields['files_to_delete'] = $_POST['wpuef_options']['files_to_delete'];
				   */
				
		}
		//Save Customer extra fields
		/* if(!empty($customer_fields_to_save))
			$wpuef_user_model->save_fields($customer_fields_to_save, $user_id );  */
		
		if(isset($posted_fields))
			$wpuef_user_model->save_fields($posted_fields, $user_id );
		
		//Order extra fields fields
	}
	public function  update_extra_fields($user_id)
	{
		//wpuef_var_dump($_POST);
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		global $wpuef_user_model;
		if(isset($_POST['wpuef_options']))
			$wpuef_user_model->save_fields($_POST['wpuef_options'], $user_id );
	}
	public function  register_extra_fields ( $user_id )
	{
		global $wpuef_user_model;
		if(isset($_POST['wpuef_options']))
			$wpuef_user_model->save_fields($_POST['wpuef_options'], $user_id );
	}
}?>