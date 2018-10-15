jQuery(document).ready(function()
{
	wpuef_hide_password();
});
function wpuef_hide_password()
{
	var selectors = Array('#reg_password', '#account_password_field', '#password_1');
	for(var i = 0; i < selectors.length; i++)
	{
		if(jQuery(selectors[i]).length)
		{
			//console.log(jQuery(selectors[i]));
			//repositioning
			jQuery('.wpuef_password').parent().insertBefore(jQuery(selectors[i]).parent());
			//remove old password field
			jQuery(selectors[i]).parent().hide().remove(); 
		} 
	}
	/* jQuery('#reg_password').parent().hide().remove(); 
	jQuery('#account_password_field').parent().hide().remove(); 
	if(jQuery('#password_1'))
	{
		jQuery('.wpuef_password').parent().insertBefore(jQuery('#password_1').parent());
		jQuery('#password_1').parent().hide().remove(); 
	} */
	
}
wpuef_hide_password();