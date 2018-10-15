jQuery(document).ready(function()
{
	if(!wpuef_is_logged)
		//jQuery('#wpuef-checkout-extra-fields').hide();
		wpuef_create_account_checkbox_check(null);
	
	jQuery(document).on('change', '#createaccount', wpuef_create_account_checkbox_check);
});

function wpuef_create_account_checkbox_check(event)
{
	var is_checked = jQuery('#createaccount').prop('checked');
	//console.log(jQuery(event.currentTarget).prop('checked'));
	//console.log(is_checked);
	
	if(is_checked)
	{
		jQuery('#wpuef-checkout-extra-fields').show();
		jQuery('.wpuef_element').each(function(index,elem)
		{
			if(jQuery(elem).data('required') == 'yes')
				jQuery(elem).prop('required','required');
		});
	}
	else
	{
		jQuery('#wpuef-checkout-extra-fields').hide(800);
		jQuery('.wpuef_element').each(function(index,elem)
		{
			if(jQuery(elem).data('required') == 'yes')
			{
				//console.log(jQuery(elem));
				jQuery(elem).removeAttr('required');
			}
		});
	}
}