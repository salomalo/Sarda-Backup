jQuery(document).ready(function()
{
	jQuery(document).on('change', '.wpuef_checkbox_perform_check', wpuef_checkbox_required_check);
	jQuery('.wpuef_checkbox_perform_check').trigger('change');
});
function wpuef_checkbox_required_check(event)
{
	var id = jQuery(event.currentTarget).data('id');
	var at_least_one_checked = false;
	jQuery('.wpuef_checkobox_group_'+id).each(function(index,elem)
	{
		if(elem.checked)
			at_least_one_checked = true;
	});
	if(at_least_one_checked)
		jQuery('.wpuef_checkobox_group_'+id).removeAttr('required');
	else
		jQuery('.wpuef_checkobox_group_'+id).attr('required', 'required');
}