<?php 
class WPUEF_Wpml
{
	function __construct()
	{
	}
	
	public function get_current_language()
	{
		if(!class_exists('SitePress'))
			return get_locale();
		
		return ICL_LANGUAGE_CODE."_".strtoupper(ICL_LANGUAGE_CODE);
	}
}
?>
