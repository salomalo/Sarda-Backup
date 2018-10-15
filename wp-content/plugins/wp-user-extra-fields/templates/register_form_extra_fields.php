<?php
global $wpuef_option_model, $wpuef_wpml_helper, $wpuef_woocommerce_is_active;

//Defaul registration page
if(strpos($_SERVER['REQUEST_URI'],"wp-login.php?action=register") !== false): ?>
<style>
.wpuef_label
{
	display:block;
	clear:both;
	margin-top: 10px;
}
</style>

<?php endif; ?>
<div id="wpuef-file-container" style="display:none"></div> <!--file upload -->
<?php foreach($extra_fields->fields as $extra_field):
	
	$read_only = !current_user_can( 'manage_options' ) && isset($extra_field->editable_only_by_admin) && $extra_field->editable_only_by_admin ? true : false;
	$placeholder = !isset($extra_field->field_options->placeholder) ? "": str_replace('"', "", $extra_field->field_options->placeholder);
	$row_size = isset($extra_field->checkout_row_size) && $extra_field->checkout_row_size != "" ? $extra_field->checkout_row_size : 'wide';
	$is_password_override = isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password' ? true : false;;
	$password_field_name = 'password';
	$password_class = '';
	
	if(!$wpuef_woocommerce_is_active && $extra_field->field_type == 'country_and_state')
			continue;
		
	if( (!isset($extra_field->hide_in_the_register_page) || !$extra_field->hide_in_the_register_page) &&				
		(!isset($extra_field->invisible) || !$extra_field->invisible) &&  
		!$read_only /* && 
		( !isset($extra_field->woocommerce_checkout_only_editable) || !$extra_field->woocommerce_checkout_only_editable) */
	  ):
		$required = isset($extra_field->required) && $extra_field->required ? true:false;

	//wpuef_var_dump($extra_field); //field_to_override	
	if($is_password_override)
	{
		$password_class = 'wpuef_password';	
		$required = true;
		wp_enqueue_script('wpuef-hide-password', WPUEF_PLUGIN_PATH.'/js/wpuef-password-register-hide.js', array( 'jquery' ));
	}
	
	?>
	<?php if($extra_field->field_type != "country_and_state"): ?>
	<p class="form-row form-row-<?php echo $row_size ;?> wpuef_field_row">
	<?php endif; ?>
	<label class="wpuef_label <?php if($required) echo "wpuef_required";?> "><?php echo $extra_field->label; ?></label>
	<?php 
		
		//Types
		if($extra_field->field_type == "dropdown"): ?>
		<select class="wpuef_input_select" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($read_only) echo 'disabled="true"'; if($extra_field->required) echo 'required="required"';?>>
			<?php if($extra_field->field_options->include_blank_option): ?>
			   <option value="" selected="selected"> </option>
			<?php endif; 
				foreach($extra_field->field_options->options as $index => $extra_option): ?>
			  <option value="<?php echo $index; ?>" <?php if((isset($field_value) && $field_value == $index) || (!isset($field_value) && $extra_option->checked && !$extra_field->field_options->include_blank_option)) echo 'selected';?>><?php echo $extra_option->label; ?></option>
			<?php endforeach; ?>
		</select>
		
		<?php elseif($extra_field->field_type == "country_and_state"): 
				  global $wpuef_country_model;
				  $show_state = !isset($extra_field->show_state) || $extra_field->show_state != 'no' ? true : false;
				  $countries = array("" => __('Select one','wp-user-extra-fields'));
				  $country_list = $wpuef_country_model->get_countries(isset($extra_field->coutries_to_show) ? $extra_field->coutries_to_show : null);
				  foreach((array)$country_list as $country_code => $country_name)
						$countries[$country_code] = $country_name;
					 
				  //js
				  if($show_state)
				  {
					  wp_enqueue_script('wpuef-country-state-manager', WPUEF_PLUGIN_PATH."/js/wpuef-country-state-manager.js", array('jquery'));
					  $js_vars = array(
							'ajax_url' => admin_url('admin-ajax.php'),
						);
						wp_localize_script( 'wpuef-country-state-manager', 'wpuef', $js_vars );
				  }
				  
				    
				  $custom_attributes = array('data-id'=>$extra_field->cid, 'data-required'=> $required ? 'true' : 'false');
				  if($required)
					   $custom_attributes['required'] = 'required'; 
				  woocommerce_form_field('wpuef_options['. $extra_field->cid.'][country]', array(
									'type'       => 'select',
									'class'      => $show_state ? array( 'form-row-first' ) :  array( 'form-row-wide' ),
									'input_class' => array('select2-choice', 'wpuef_country_selector'),
									//'label'      => __('Select a country','wp-user-extra-fields'),
									'label_class' => array( 'wcmca_form_label' ),
									'required' => $required,
									'custom_attributes' =>  $custom_attributes,
									//placeholder'    => __('Select a country','wp-user-extra-fields'),
									'options'    => $countries
										)
									); 
		?>
		<div id="wpuef_country_field_container_<?php echo $extra_field->cid; ?>"></div>
		<img class="wpuef_preloader_image"id="wpuef_preloader_image_<?php echo $extra_field->cid; ?>" src="<?php echo WPUEF_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>
        
	<?php elseif($extra_field->field_type == "file"): ?>
		<input class="wpuef_field wpuef_input_file input-text" type="file" value=""  
			   data-id="<?php echo $extra_field->cid; ?>"  
			   <?php if($required) echo 'required="required"';?> 
			   <?php if(isset($extra_field->file_types) && $extra_field->file_types) echo 'accept="'.$extra_field->file_types.'"';?> 
			   data-size="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>" 
			   value="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>">
			   </input>
			   <strong><?php if(isset($extra_field->file_size) && $extra_field->file_size) echo __('( Max size: ', 'wp-user-extra-fields').$extra_field->file_size." MB )"; ?></strong>
		<input type="hidden" id="wpuef-filename-<?php echo $extra_field->cid; ?>" name="wpuef_files[<?php echo $extra_field->cid; ?>][file_name]" value=""></input>
		
	<?php elseif($extra_field->field_type == "checkboxes"): ?>
		<?php foreach($extra_field->field_options->options as $index => $extra_option): ?>
			<input type="checkbox" class="wpuef_field <?php if($required) echo "wpuef_checkbox_perform_check wpuef_checkobox_group_".$extra_field->cid ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>][<?php echo $index ?>]" value="<?php echo $index ?>" <?php if($extra_option->checked) echo 'checked';?>  <?php if($required) echo 'required="required" data-id="'.$extra_field->cid.'"';?> ><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
		<?php endforeach; ?>
		
		
	<?php elseif($extra_field->field_type == "radio"): ?>
		<?php foreach($extra_field->field_options->options as $index => $extra_option): ?>
			<input type="radio" class="wpuef_field " name="wpuef_options[<?php echo $extra_field->cid; ?>]" value="<?php echo $index; ?>" <?php if($extra_option->checked) echo 'checked';?>  <?php if($required) echo 'required="required"';?> ><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
		<?php endforeach; ?>

		
	<?php elseif($extra_field->field_type == "date"): ?>
		 <input class="wpuef_field wpuef_input_date" type="text" placeholder="<?php echo $placeholder; ?>" value="" name="wpuef_options[<?php echo $extra_field->cid; ?>]"  <?php if($required) echo 'required="required"';?> ></input>
	<?php elseif($extra_field->field_type == "time"): ?>
		 <input class="wpuef_field wpuef_input_time " type="text" placeholder="<?php echo $placeholder; ?>" value="" name="wpuef_options[<?php echo $extra_field->cid; ?>]"  <?php if($required) echo 'required="required"';?> ></input>
	
				
	<?php elseif($extra_field->field_type == "website"): ?>
		<input class="wpuef_field wpuef_input_url input-text" type="url" placeholder="<?php echo $placeholder; ?>" value="" name="wpuef_options[<?php echo $extra_field->cid; ?>]"  <?php if($required) echo 'required="required"';?>  ></input>

	<?php elseif($extra_field->field_type == "paragraph"): ?>
		<textarea  class="wpuef_field wpuef_input_textarea" name="wpuef_options[<?php echo $extra_field->cid; ?>]"  placeholder="<?php echo $placeholder; ?>" <?php if($required) echo 'required="required"';?> ></textarea>
	
	<?php elseif($extra_field->field_type == "number"): ?>
		<input class="wpuef_field wpuef_input_number input-text" type="<?php echo $extra_field->field_type; ?>" placeholder="<?php echo $placeholder; ?>" value="" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if(isset($extra_field->field_options->min)) echo 'min="'.$extra_field->field_options->min.'"'?>  <?php if(isset($extra_field->field_options->max)) echo 'max="'.$extra_field->field_options->max.'"'?>  <?php if($required) echo 'required="required"';?> />
	<?php else: 
		// Text type?>
		<input class="wpuef_field wpuef_input_text input-text <?php echo $password_class; ?>" type="<?php if($is_password_override) echo 'password'; else echo $extra_field->field_type; ?>" placeholder="<?php echo $placeholder; ?>" value="" name="<?php if($is_password_override) echo $password_field_name; else echo 'wpuef_options['.$extra_field->cid.']'; ?>"  <?php if($required) echo 'required="required"';?> />
	<?php endif; 
	//End types
	?>
	<?php //Description
		if( isset($extra_field->field_options->description)): ?>
			<span class="wpuef_description"><?php echo $extra_field->field_options->description; ?></span>
		<?php endif; ?>
<?php if($extra_field->field_type != "country_and_state"): ?>	
	</p>	
<?php endif; ?>
<?php endif; endforeach; ?>
<div style="display:block; clear:both; width: 100%; height:1px;"></div>
<script>
var delete_pending_message = ""; //file upload
var delete_popup_warning_message ="";  //file upload
var file_check_popup_browser = "<?php _e("Please upgrade your browser, because your current browser lacks some new features we need!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_size = "<?php _e("Choosen file is too big and will not be uploaded!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_api = "<?php _e("The File APIs are not fully supported in this browser.", 'wp-user-extra-fields'); ?>";  
var wpuef_required_fields_error = "<?php _e("Required fields cannot be left empty.", 'wp-user-extra-fields'); ?>";  
jQuery(document).ready(function()
{
	jQuery( ".wpuef_input_date" ).pickadate({formatSubmit: 'yyyy/mm/dd', format: '<?php echo $wpuef_option_model->get_date_format(); ?>',selectMonths: true,  selectYears: true, selectYears: 100, max: [<?php echo date('Y', strtotime('+10 years'))  ?>,11,31] } );
	jQuery( ".wpuef_input_time" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i'});
});
</script>