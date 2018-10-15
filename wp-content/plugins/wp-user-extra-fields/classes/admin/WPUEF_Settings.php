<?php 
class WPUEF_Settings
{
	public function __construct()
	{
	}
	public function render_page()
	{
		 global $wpuef_option_model, $wpuef_woocommerce_is_active;
		
		if(isset($_POST['wpuef_general_options']))
			$wpuef_option_model->save_general_options($_POST['wpuef_general_options']); 
		
		$options = $wpuef_option_model->get_general_options();
		$woocommerce_checkout_page_positioning = $wpuef_option_model->get_woocommerce_checkout_page_positioning();
		$woocommerce_register_page_positioning = $wpuef_option_model->get_woocommerce_register_page_positioning();
		$date_format = $wpuef_option_model->get_date_format();//isset($options['date_format']) ? $options['date_format'] : "dd/mm/yyyy";
		
		/* wp_enqueue_style( 'wpuef-common', WPUEF_PLUGIN_PATH.'/css/wpuef_common.css'); */
		wp_enqueue_style( 'wpuef-admin', WPUEF_PLUGIN_PATH.'/css/wpuef-settings.css');		
		//$order_statuses = wc_get_order_statuses();
		
		?>
		
		<div class="wrap white-box">
				
			<?php screen_icon("options-general"); ?>
			<h2><?php _e('General options', 'wp-user-extra-fields');?></h2>
			<form action="" method="post" > <!--options.php -->
			<?php settings_fields('wpuef_general_options_group'); ?> 
				
				<h3><?php _e('Date format', 'wp-user-extra-fields');?></h3>
				<label><?php _e('Select the date format used to display dates in input fields', 'wp-user-extra-fields'); ?></label>
				<select name="wpuef_general_options[date_format]">
					<option value="dd/mm/yyyy" <?php if($date_format == "dd/mm/yyyy") echo 'selected="selected"';?>><?php _e('dd/mm/yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm/dd/yyyy" <?php if($date_format == "mm/dd/yyyy") echo 'selected="selected"';?>><?php _e('mm/dd/yyyy', 'wp-user-extra-fields');?></option>
					<option value="dd.mm.yyyy" <?php if($date_format == "dd.mm.yyyy") echo 'selected="selected"';?>><?php _e('dd.mm.yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm.dd.yyyy" <?php if($date_format == "mm.dd.yyyy") echo 'selected="selected"';?>><?php _e('mm.dd.yyyy', 'wp-user-extra-fields');?></option>
					<option value="dd-mm-yyyy" <?php if($date_format == "dd-mm-yyyy") echo 'selected="selected"';?>><?php _e('dd-mm-yyyy', 'wp-user-extra-fields');?></option>
					<option value="mm-dd-yyyy" <?php if($date_format == "mm-dd-yyyy") echo 'selected="selected"';?>><?php _e('mm-dd-yyyy', 'wp-user-extra-fields');?></option>
				</select>
				
				<?php if($wpuef_woocommerce_is_active): ?>
				<h3><?php _e('WooCommerce registration page field positioning', 'wp-user-extra-fields');?></h3>
				<label><?php _e('Select the extra fields form positioning in WooCommerce registration page', 'wp-user-extra-fields'); ?></label>
				<select name="wpuef_general_options[woocommerce_register_page_positioning]">
					<option value="register_form" <?php if($woocommerce_register_page_positioning == "register_form") echo 'selected="selected"';?>><?php _e('After', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_register_form_start" <?php if($woocommerce_register_page_positioning == "woocommerce_register_form_start") echo 'selected="selected"';?>><?php _e('Before', 'wp-user-extra-fields');?></option>
				</select>
				<?php endif; ?>
				
				<?php if($wpuef_woocommerce_is_active): ?>
				<h3><?php _e('WooCommerce checkout page field positioning', 'wp-user-extra-fields');?></h3>
				<label><?php _e('Select the extra fields form in checkout page', 'wp-user-extra-fields'); ?></label>
				<select name="wpuef_general_options[woocommerce_checkout_page_positioning]">
					<option value="woocommerce_before_checkout_billing_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_checkout_billing_form") echo 'selected="selected"';?>><?php _e('Before billing form', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_after_checkout_billing_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_checkout_billing_form") echo 'selected="selected"';?>><?php _e('After billing form', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_before_checkout_shipping_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_checkout_shipping_form") echo 'selected="selected"';?>><?php _e('Before shipping form (Note: shipping form must be visible)', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_after_checkout_shipping_form" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_checkout_shipping_form") echo 'selected="selected"';?>><?php _e('After shipping form (Note: shipping form must be visible)', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_before_order_notes" <?php if($woocommerce_checkout_page_positioning == "woocommerce_before_order_notes") echo 'selected="selected"';?>><?php _e('Before order notes', 'wp-user-extra-fields');?></option>
					<option value="woocommerce_after_order_notes" <?php if($woocommerce_checkout_page_positioning == "woocommerce_after_order_notes") echo 'selected="selected"';?>><?php _e('After order notes', 'wp-user-extra-fields');?></option>
				</select>
				<?php endif; ?>
				
				<p class="submit">
					<input  name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'wp-user-extra-fields'); ?>" />
				</p>
			</fom>
		</div>
		<?php
	}
}
?>