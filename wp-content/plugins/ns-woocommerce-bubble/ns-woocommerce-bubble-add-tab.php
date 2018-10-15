<?php
function ns_custom_tab_options_tab() {
    echo '<li class="custom_tab"><a href="#custom_tab_data">Custom Badge</a></li>';
}
add_action('woocommerce_product_write_panel_tabs', 'ns_custom_tab_options_tab'); 


//Add field options
function custom_tab_options() {
	global $post;
	
	$custom_tab_options = array(
		'title' => get_post_meta($post->ID, 'custom_tab_title', true),
		'content' => get_post_meta($post->ID, 'custom_tab_content', true),
		'type' => get_post_meta($post->ID, 'custom_tab_type_ns', true),
		'font_size' => get_post_meta($post->ID, 'custom_tab_font_size_ns', true),
		'color_text' => get_post_meta($post->ID, 'custom_tab_color_text', true),
		'color_bubble' => get_post_meta($post->ID, 'custom_tab_color', true),
		'enable_two' => get_post_meta($post->ID, 'custom_tab_enabled_two', true),
		'title2' => get_post_meta($post->ID, 'custom_tab_title2', true),
		'font_size2' => get_post_meta($post->ID, 'custom_tab_font_size_ns2', true),
		'type2' => get_post_meta($post->ID, 'custom_tab_type_ns2', true),
		'color_text2' => get_post_meta($post->ID, 'custom_tab_color_text2', true),
		'color_bubble2' => get_post_meta($post->ID, 'custom_tab_color2', true),
		'custom_tab_top' => get_post_meta($post->ID, 'custom_tab_top', true),
		'custom_tab_left' => get_post_meta($post->ID, 'custom_tab_left', true),		
		'custom_tab_top2' => get_post_meta($post->ID, 'custom_tab_top2', true),
		'custom_tab_left2' => get_post_meta($post->ID, 'custom_tab_left2', true)		
	);
	
?>
	<div id="custom_tab_data" class="panel woocommerce_options_panel">
		<div class="options_group">
			<p class="form-field">
				<?php woocommerce_wp_checkbox( array( 'id' => 'custom_tab_enabled', 'label' => __('Enable Custom Badge?', 'woothemes'), 'description' => __('', 'woothemes') ) ); ?>
			</p>
		</div>
		
		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Text:', 'woothemes'); ?></label>
				<input type="text" size="5" name="custom_tab_title" value="<?php echo @$custom_tab_options['title']; ?>" placeholder="<?php _e('Enter your custom badge text', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Font Size:', 'woothemes'); ?></label>
				<input type="number" size="5" min="8" name="custom_tab_font_size_ns" value="<?php echo @$custom_tab_options['font_size']; ?>" placeholder="<?php _e('Enter your font size', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Top (px):', 'woothemes'); ?></label>
				<input type="number" size="5" name="custom_tab_top" value="<?php echo @$custom_tab_options['custom_tab_top']; ?>" placeholder="<?php _e('Enter Top distance in px', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Left (px):', 'woothemes'); ?></label>
				<input type="number" size="5" name="custom_tab_left" value="<?php echo @$custom_tab_options['custom_tab_left']; ?>" placeholder="<?php _e('Enter Left distance in px', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<?php 
					$filed = array(
                        'id' => 'custom_tab_type_ns',
                        'label' => __('Style:', 'woocommerce'),
                        'description' => __('Choose a value.', 'woocommerce'),
                        'value'         => @$custom_tab_options['type'],
                        'options' => array(
			                            '' => __('...choose', 'woocommerce'),
			                            'roundedNS' => __('Rounded', 'woocommerce'),
			                            'roundedSmallNS' => __('Rounded Small', 'woocommerce'),
			                            'roundedBigNS' => __('Rounded Big', 'woocommerce'),
			                            'squareNS' => __('Square', 'woocommerce'),
			                            'squareSmallNS' => __('Square Small', 'woocommerce'),
			                            'squareBigNS' => __('Square Big', 'woocommerce'),
			                            'squareRadiusNS' => __('Square Radius', 'woocommerce'),
			                            'squareRadiusSmallNS' => __('Square Radius Small', 'woocommerce'),
			                            'squareRadiusBigNS' => __('Square Radius Big', 'woocommerce'),
			                            'squareRadiusRectNS' => __('Rectangle', 'woocommerce'),
			                            'squareRadiusRectSmallNS' => __('Rectangle Small', 'woocommerce'),
			                            'squareRadiusRectBigNS' => __('Rectangle Big', 'woocommerce')
                        			 )                       
                    );
				?>
				<?php woocommerce_wp_select($filed); ?>
			</p>
        </div>	

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Text color:', 'woothemes'); ?></label>
				<input type="text" size="6" class="ns-color-field-bubble" name="custom_tab_color_text" value="<?php echo @$custom_tab_options['color_text']; ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Badge color:', 'woothemes'); ?></label>
				<input type="text" size="6" class="ns-color-field-bubble" name="custom_tab_color" value="<?php echo @$custom_tab_options['color_bubble']; ?>" />
			</p>
        </div>




		<div class="options_group">
			<p class="form-field">
				<?php woocommerce_wp_checkbox( array( 'id' => 'custom_tab_enabled_two', 'label' => __('Enable Second Badge?', 'woothemes'), 'description' => __('', 'woothemes') ) ); ?>
			</p>
		</div>

<?php
if(isset($custom_tab_options['enable_two']) && $custom_tab_options['enable_two'] == 'yes'){
	$secondbubbleclass = '';
}else{
	$secondbubbleclass = 'secondbubbhide';
}


?>
		<!-- start div secondbubbhide -->
		<div class="<?php echo $secondbubbleclass; ?>" id="badgetwocontainer">

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Text:', 'woothemes'); ?></label>
				<input type="text" size="5" name="custom_tab_title2" value="<?php echo @$custom_tab_options['title2']; ?>" placeholder="<?php _e('Enter your custom badge text', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Font Size:', 'woothemes'); ?></label>
				<input type="number" size="5" min="8" name="custom_tab_font_size_ns2" value="<?php echo @$custom_tab_options['font_size2']; ?>" placeholder="<?php _e('Enter your font size', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Top (px):', 'woothemes'); ?></label>
				<input type="number" size="5" name="custom_tab_top2" value="<?php echo @$custom_tab_options['custom_tab_top2']; ?>" placeholder="<?php _e('Enter Top distance in px', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Left (px):', 'woothemes'); ?></label>
				<input type="number" size="5" name="custom_tab_left2" value="<?php echo @$custom_tab_options['custom_tab_left2']; ?>" placeholder="<?php _e('Enter Left distance in px', 'woothemes'); ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<?php 
					$filed2 = array(
                        'id' => 'custom_tab_type_ns2',
                        'label' => __('Style:', 'woocommerce'),
                        'description' => __('Choose a value.', 'woocommerce'),
                        'value'         => @$custom_tab_options['type2'],
                        'options' => array(
			                            '' => __('...choose', 'woocommerce'),
			                            'roundedNS' => __('Rounded', 'woocommerce'),
			                            'roundedSmallNS' => __('Rounded Small', 'woocommerce'),
			                            'roundedBigNS' => __('Rounded Big', 'woocommerce'),
			                            'squareNS' => __('Square', 'woocommerce'),
			                            'squareSmallNS' => __('Square Small', 'woocommerce'),
			                            'squareBigNS' => __('Square Big', 'woocommerce'),
			                            'squareRadiusNS' => __('Square Radius', 'woocommerce'),
			                            'squareRadiusSmallNS' => __('Square Radius Small', 'woocommerce'),
			                            'squareRadiusBigNS' => __('Square Radius Big', 'woocommerce'),
			                            'squareRadiusRectNS' => __('Rectangle', 'woocommerce'),
			                            'squareRadiusRectSmallNS' => __('Rectangle Small', 'woocommerce'),
			                            'squareRadiusRectBigNS' => __('Rectangle Big', 'woocommerce')
                        			 )                       
                    );
				?>
				<?php woocommerce_wp_select($filed2); ?>
			</p>
        </div>	

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Text color:', 'woothemes'); ?></label>
				<input type="text" size="6" class="ns-color-field-bubble" name="custom_tab_color_text2" value="<?php echo @$custom_tab_options['color_text2']; ?>" />
			</p>
        </div>

		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Badge color:', 'woothemes'); ?></label>
				<input type="text" size="6" class="ns-color-field-bubble" name="custom_tab_color2" value="<?php echo @$custom_tab_options['color_bubble2']; ?>" />
			</p>
        </div>

    	</div>
    	<!-- end div secondbubbhide -->

	</div>
<?php
}
add_action('woocommerce_product_write_panels', 'custom_tab_options');





// Save Data
function process_product_meta_custom_tab( $post_id ) {
	update_post_meta( $post_id, 'custom_tab_enabled', ( isset($_POST['custom_tab_enabled']) && $_POST['custom_tab_enabled'] ) ? 'yes' : 'no' );
	update_post_meta( $post_id, 'custom_tab_title', $_POST['custom_tab_title']);
	update_post_meta( $post_id, 'custom_tab_type_ns', $_POST['custom_tab_type_ns']);
	update_post_meta( $post_id, 'custom_tab_font_size_ns', $_POST['custom_tab_font_size_ns']);
	update_post_meta( $post_id, 'custom_tab_color_text', $_POST['custom_tab_color_text']);
	update_post_meta( $post_id, 'custom_tab_color', $_POST['custom_tab_color']);
	update_post_meta( $post_id, 'custom_tab_top', $_POST['custom_tab_top']);
	update_post_meta( $post_id, 'custom_tab_left', $_POST['custom_tab_left']);	

	update_post_meta( $post_id, 'custom_tab_enabled_two', ( isset($_POST['custom_tab_enabled_two']) && $_POST['custom_tab_enabled_two'] ) ? 'yes' : 'no' );	
	update_post_meta( $post_id, 'custom_tab_title2', $_POST['custom_tab_title2']);
	update_post_meta( $post_id, 'custom_tab_font_size_ns2', $_POST['custom_tab_font_size_ns2']);
	update_post_meta( $post_id, 'custom_tab_type_ns2', $_POST['custom_tab_type_ns2']);
	update_post_meta( $post_id, 'custom_tab_color_text2', $_POST['custom_tab_color_text2']);
	update_post_meta( $post_id, 'custom_tab_color2', $_POST['custom_tab_color2']);
	update_post_meta( $post_id, 'custom_tab_top2', $_POST['custom_tab_top2']);
	update_post_meta( $post_id, 'custom_tab_left2', $_POST['custom_tab_left2']);

	// add dynamic style
	require_once( BUBBLE_NS_PLUGIN_DIR.'/ns-bubble-dynamic-style.php');
}
add_action('woocommerce_process_product_meta', 'process_product_meta_custom_tab');
?>