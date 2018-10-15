<?php 
global $wpuef_option_model, $wpuef_user_model, $wpuef_wpml_helper, $wpuef_woocommerce_is_active;


//$extra_fields = json_decode(stripcslashes($fields_json_string));
?>
<div id="wpuef-checkout-extra-fields">
	<div id="wpuef-file-container" style="display:none"></div> <!--file upload -->
	<?php 
	foreach($extra_fields->fields as $extra_field):
	$read_only = !current_user_can( 'manage_options' ) && isset($extra_field->editable_only_by_admin) && $extra_field->editable_only_by_admin ? true : false;
	$row_size = isset($extra_field->checkout_row_size) && $extra_field->checkout_row_size != "" ? $extra_field->checkout_row_size : 'wide';
	$is_password_override = isset($extra_field->field_to_override) && $extra_field->field_to_override == 'password' ? true : false;
	$password_field_name = 'account_password';	
	$password_class = '';
		
		    //1.
	  if(  (!is_user_logged_in() && (/* (isset($extra_field->visible_only_at_register_page) && $extra_field->visible_only_at_register_page == true) && */ (!isset($extra_field->hide_in_the_register_page) || !$extra_field->hide_in_the_register_page) /* || 
									 (!isset($extra_field->visible_only_at_register_page) || !$extra_field->visible_only_at_register_page) && (!isset($extra_field->hide_in_the_register_page) || !$extra_field->hide_in_the_register_page) && (isset($extra_field->woocommerce_visible_on_checkout) && $extra_field->woocommerce_visible_on_checkout == true) */ 
									 )
			) ||
		  //2.
		  ( is_user_logged_in() && (isset($extra_field->woocommerce_visible_on_checkout) && $extra_field->woocommerce_visible_on_checkout == true) && !$is_password_override &&
			  (!isset($extra_field->visible_only_at_register_page) || !$extra_field->visible_only_at_register_page )
			)
		):
	
		$required = isset($extra_field->required) && $extra_field->required ? true:false;
		$placeholder = !isset($extra_field->field_options->placeholder) ? "": str_replace('"', "", $extra_field->field_options->placeholder);
		if($is_password_override)
		{
			$password_class = 'wpuef_password';	 
			$required = true;
			wp_enqueue_script('wpuef-hide-password', WPUEF_PLUGIN_PATH.'/js/wpuef-password-register-hide.js', array( 'jquery' ));
		}
		?>
	<?php if($extra_field->field_type != "country_and_state"): ?>
	<p class="form-row form-row-<?php echo $row_size ;?> ">
	<?php endif; ?>
		<label class="wpuef_label <?php if($required) echo "wpuef_required";?>"><?php echo $extra_field->label; ?></label>
		<?php 
			$field_value = $wpuef_user_model->get_field( $extra_field->cid, $user_id );
			//wpuef_var_dump($field_value);
			//Types
			if($extra_field->field_type == "dropdown"): ?>
			<select data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" class="wpuef_input_select wpuef_element" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($read_only) echo 'disabled="true"'; if($extra_field->required) echo 'required="required"';?>>
				<?php if($extra_field->field_options->include_blank_option): ?>
				   <option value="" selected="selected"> </option>
				<?php endif; 
					foreach($extra_field->field_options->options as $index => $extra_option): ?>
				  <option value="<?php echo $index; ?>" <?php if((isset($field_value) && $field_value == $index) || (!isset($field_value) && $extra_option->checked && !$extra_field->field_options->include_blank_option)) echo 'selected';?>><?php echo $extra_option->label; ?></option>
				<?php endforeach; ?>
			</select>
		
		<?php elseif($extra_field->field_type == "country_and_state"): 
			  global $wpuef_country_model;
			  $countries = array("" => __('Select one','wp-user-extra-fields'));
			  $country_list = $wpuef_country_model->get_countries(isset($extra_field->coutries_to_show) ? $extra_field->coutries_to_show : null);
			  foreach((array)$country_list as $country_code => $country_name)
						$countries[$country_code] = $country_name;
			  $show_state = !isset($extra_field->show_state) || $extra_field->show_state != 'no' ? true : false;
			  
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
								'class'      =>  $show_state ? array( 'form-row-first' ) :  array( 'form-row-wide' ),
								'input_class' => array('select2-choice', 'wpuef_country_selector', "wpuef_element"),
								//'label'      => __('Select a country','wp-user-extra-fields'),
								'label_class' => array( 'wcmca_form_label' ),
								'custom_attributes' =>  array('data-id'=>$extra_field->cid),
								//placeholder'    => __('Select a country','wp-user-extra-fields'),
								'required' => $required,
								'custom_attributes' =>  $custom_attributes,
								'options'    => $countries,
								'default' => isset($field_value["country"]) ? $field_value["country"] : ""
									)
								); 
		?>
		<div id="wpuef_country_field_container_<?php echo $extra_field->cid; ?>">
		<?php 
		if($show_state && isset($field_value["country"]) && $field_value["country"] != "")
		{
			$wpuef_country_model->render_state_select_html_by_country($field_value["country"], $extra_field->cid, isset($field_value["state"]) ? $field_value["state"] : null, $required);
		}
		?>
		</div>
		<img class="wpuef_preloader_image "id="wpuef_preloader_image_<?php echo $extra_field->cid; ?>" src="<?php echo WPUEF_PLUGIN_PATH.'/img/loader.gif' ?>" ></img>
			
		<?php elseif($extra_field->field_type == "file"): ?>
			<div id="wpuef-file-box-<?php echo $extra_field->cid; ?>"> <!--  //file upload edit -->
			<?php  //wpuef_var_dump($extra_field);
				if(isset($field_value) && isset($field_value["url"])): 
					$required = false; ?> 
					<button class="button button-primary wpuef_view_download_file_button" target="_blank" data-href="<?php echo $field_value["url"]; ?>"><?php _e('Download / View', 'wp-user-extra-fields') ?></button> <br/>
				<?php endif; ?>
				<input class="wpuef_input_file input-text wpuef_element" type="file" value=""  
						data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>"
					   data-id="<?php echo $extra_field->cid; ?>"  
					   <?php if($required) echo 'required="required"';?>
					   <?php if(isset($extra_field->file_types) && $extra_field->file_types) echo 'accept="'.$extra_field->file_types.'"';?> 
					   data-size="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>" 
					   value="<?php if(isset($extra_field->file_size) && $extra_field->file_size) echo $extra_field->file_size*1048576; ?>">
					   </input>
					   <strong><?php if(isset($extra_field->file_size) && $extra_field->file_size) echo __('( Max size: ', 'wp-user-extra-fields').$extra_field->file_size." MB )"; ?></strong>
				<input type="hidden" id="wpuef-filename-<?php echo $extra_field->cid; ?>" name="wpuef_files[<?php echo $extra_field->cid; ?>][file_name]" value=""></input>
			</div>
			
		<?php elseif($extra_field->field_type == "checkboxes"): ?>
			<?php foreach($extra_field->field_options->options as $index => $extra_option): ?>
				<input type="checkbox" data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" <?php if(isset($field_value[$index])) echo 'checked';?> name="wpuef_options[<?php echo $extra_field->cid; ?>][<?php echo $index ?>]" value="<?php echo $index ?>" <?php if($required) echo 'required="required" class="wpuef_element wpuef_checkbox_perform_check wpuef_checkobox_group_'.$extra_field->cid.'" data-id="'.$extra_field->cid.'"';?> <?php if($read_only) echo 'readonly="readonly"';?>><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
			<?php endforeach; ?>
			
			
		<?php elseif($extra_field->field_type == "radio"): ?>
			<?php foreach($extra_field->field_options->options as $index => $extra_option): ?>
				<input data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" type="radio" name="wpuef_options[<?php echo $extra_field->cid; ?>]" value="<?php echo $index; ?>" <?php if($field_value == $index) echo 'checked';?> <?php if($required) echo 'required="required"';?> class="wpuef_element"><span class="wpuef_checkbox_label"><?php echo $extra_option->label; ?></span></input><br/>
			<?php endforeach; ?>

			
		<?php elseif($extra_field->field_type == "date"): 
				if(isset($field_value))
					{
						$date = DateTime::createFromFormat("Y/m/d", $field_value );
						if(is_object($date))
							$field_value = $date->format($wpuef_option_model->get_date_format(true));
					}?>
			 <input data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" class="wpuef_input_date wpuef_element" type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo $placeholder; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?>></input>
		<?php elseif($extra_field->field_type == "time"): ?>
			 <input data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" class="wpuef_input_time wpuef_element" type="text" value="<?php echo $field_value; ?>" placeholder="<?php echo $placeholder; ?>"  name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?>></input>
		
		<?php elseif($extra_field->field_type == "website"): ?>
			<input data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" class="input-text wpuef_input_url wpuef_element" type="url" value="<?php echo $field_value; ?>" placeholder="<?php echo $placeholder; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if($required) echo 'required="required"';?>></input>

		<?php elseif($extra_field->field_type == "paragraph"): ?>
			<textarea  data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" class="wpuef_input_textarea wpuef_element" name="wpuef_options[<?php echo $extra_field->cid; ?>]" placeholder="<?php echo $placeholder; ?>"  <?php if($required) echo 'required="required"';?>><?php echo $field_value; ?></textarea>
		
		<?php elseif($extra_field->field_type == "number"): ?>
			<input class="wpuef_input_number wpuef_element" data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" type="<?php echo $extra_field->field_type; ?>" placeholder="<?php echo $placeholder; ?>"  value="<?php echo $field_value; ?>" name="wpuef_options[<?php echo $extra_field->cid; ?>]" <?php if(isset($extra_field->field_options->min)) echo 'min="'.$extra_field->field_options->min.'"'?>  <?php if(isset($extra_field->field_options->max)) echo 'max="'.$extra_field->field_options->max.'"'?>  <?php if($required) echo 'required="required"';?>/>
		<?php else: ?>
			<input class="input-text wpuef_input_text wpuef_element <?php echo $password_class; ?>" data-required="<?php if(!$required) echo 'no'; else echo 'yes';?>" type="<?php if($is_password_override) echo 'password'; else echo $extra_field->field_type; ?>" placeholder="<?php echo $placeholder; ?>"  value="<?php /* echo $field_value; */ ?>" name="<?php if($is_password_override) echo $password_field_name; else echo 'wpuef_options['.$extra_field->cid.']'; ?>" <?php if($required) echo 'required="required"';?>/>
		<?php endif; 
		//End types
		?>
		
		<?php //Description
			if( isset($extra_field->field_options->description)): ?>
				<span class="description wpuef_description"><?php echo $extra_field->field_options->description; ?></span>
			<?php endif; ?>
<?php if($extra_field->field_type != "country_and_state"): ?>	
	</p>	
<?php endif; ?>			
<?php endif; endforeach; ?>
</div>
<div style="display:block; clear:both; width: 100%; height:1px;"></div>
<script>
var delete_pending_message = ""; //file upload
var delete_popup_warning_message ="";  //file upload
var file_check_popup_browser = "<?php _e("Please upgrade your browser, because your current browser lacks some new features we need!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_size = "<?php _e("Choosen file is too big and will not be uploaded!", 'wp-user-extra-fields'); ?>";  
var file_check_popup_api = "<?php _e("The File APIs are not fully supported in this browser.", 'wp-user-extra-fields'); ?>"; 
jQuery(document).ready(function()
{
	jQuery( ".wpuef_input_date" ).pickadate({formatSubmit: 'yyyy/mm/dd', format: '<?php echo $wpuef_option_model->get_date_format(); ?>',selectMonths: true,  selectYears: true, selectYears: 100, max: [<?php echo date('Y', strtotime('+10 years'))  ?>,11,31] });
	jQuery( ".wpuef_input_time" ).pickatime({formatSubmit: 'HH:i', format: 'HH:i'});
});
</script>