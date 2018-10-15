<?php 
function wpuef_get_field($field_id, $user_id = null)
{
	global $wpuef_user_model, $wpuef_option_model;
	if(!isset($user_id))
		$user_id = get_current_user_id();
	if(!isset($user_id) || !isset($field_id))
		return null;
	
	$field_value = $wpuef_user_model->get_field( $field_id, $user_id);
	$extra_fields = $wpuef_option_model->get_option('json_fields_string');
	$result = array();

	foreach($extra_fields->fields as $extra_field)
	{
		if($extra_field->cid == $field_id)
		{
			$result = $extra_field;			
			$result->value = $field_value;
		}
	}
	return $result;
}
function wpuef_set_field($field_id, $value, $user_id = null)
{
	global $wpuef_user_model, $wpuef_option_model;
	if(!isset($user_id))
		$user_id = get_current_user_id();
	if(!isset($user_id) || !isset($field_id))
		return null;
	
	$wpuef_user_model->save_field($field_id, $value, $user_id);
}

function wpuef_get_woo_version_number() 
{
        // If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	
	// If the plugin version number is set, return it 
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return $plugin_folder[$plugin_file]['Version'];

	} else {
	// Otherwise return null
		return NULL;
	}
}
function wpuef_url_exists($url) 
{
    $headers = @get_headers($url);
	if(strpos($headers[0],'200')===false) return false;
	
	return true;
}
function wpuef_get_file_version( $file ) 
{

		// Avoid notices if file does not exist
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version   = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] )
			$version = _cleanup_header_comment( $match[1] );

		return $version ;
	}
?>