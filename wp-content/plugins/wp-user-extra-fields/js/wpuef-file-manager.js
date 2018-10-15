jQuery(document).ready(function()
{
	jQuery('.wpuef_delete_file_button').on("click",wpuef_delete_file);
	jQuery('.wpuef_view_download_file_button').on("click",wpuef_view_download_file);
	if (window.File && window.FileReader && window.FileList && window.Blob) 
	{
		jQuery('.wpuef_input_file').on('change' ,wpuef_encode_file);
	} 
	else {
		alert(file_check_popup_api);
	}
		
	// jQuery('#place_order').live('click', function(event) 
	
});

function wpuef_view_download_file(evt)
{
	evt.stopPropagation();
	evt.preventDefault();
	var href =  jQuery(evt.currentTarget).data('href');
	var win = window.open(href, '_blank');
	//win.focus();
	return false;
}
function wpuef_delete_file(evt)
{
	evt.stopPropagation();
	evt.preventDefault(); 
	var id =  jQuery(evt.currentTarget).data('id');
	if(confirm(delete_popup_warning_message))
	{
		jQuery('#wpuef-file-box-'+id).html(delete_pending_message);
		jQuery('#wpuef-file-box-'+id).append('<input type="hidden" name="wpuef_options[files_to_delete]['+id+']" value="'+id+'" id="wpuef-encoded-file_'+id+'" />');
	}
	return false;
}
function wpuef_check_file_size(files, max_size)
{
	if(max_size == "")
		return true;
	
	if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		if(files != undefined)
		{
			var fsize =files[0].size;
			var ftype = files[0].type;
			var fname = files[0].name;
			if(fsize > max_size)
			{
				var size = fsize/1048576; // 1MB in bytes
				size = size.toFixed(2);
				alert(file_check_popup_size);
				return false;
			}
		}
	}
	else{
		alert(file_check_popup_browser);
		return false;
	}
	return true;
}
function wpuef_encode_file(evt) 
{
    var files = evt.target.files;
    var file = files[0];
	var id =  jQuery(evt.currentTarget).data('id');
	var max_size =  jQuery(evt.currentTarget).data('size');
	var sub_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? "-"+jQuery(evt.currentTarget).data("extra-field-custom-form-id") : ""; //used in the extra field custom form in case of multiple forms
	var file_id = jQuery(evt.currentTarget).attr("data-extra-field-custom-form-id") ? jQuery(evt.currentTarget).data("file-id") : id;
	
	//clear old one
	if(jQuery('#wpuef-encoded-file_'+id).length)
		jQuery('#wpuef-encoded-file_'+id).remove();
	
	if(wpuef_check_file_size(files, max_size))
	{
		jQuery('#wpuef-filename-'+id).val(file.name);
		if (files && file) 
		{
			var reader = new FileReader();

			reader.onload = function(readerEvt) 
			{
				var binaryString = readerEvt.target.result;
				//document.getElementById("base64textarea").value = btoa(binaryString);
			   if(!jQuery('#wpuef-encoded-file_'+id).length)
			   {
					jQuery('#wpuef-file-container'+sub_id).append('<input type="hidden" name="wpuef_options[files]['+file_id+']" id="wpuef-encoded-file_'+id+'" />');
			   }
				
				jQuery('#wpuef-encoded-file_'+id).val(btoa(binaryString));			
		   };
			reader.readAsBinaryString(file);
		}
	}
};