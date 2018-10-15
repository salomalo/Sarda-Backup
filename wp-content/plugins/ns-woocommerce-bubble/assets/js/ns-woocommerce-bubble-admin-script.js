(function( $ ) {
 
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.ns-color-field-bubble').wpColorPicker();
    });
     
})( jQuery );



jQuery( document ).ready(function( $ ) {
	$('#custom_tab_enabled_two').change(function(){
	  if($('#custom_tab_enabled_two').is(':checked')){
	    $('#badgetwocontainer').css("display", "block");
	  } else {
	    $('#badgetwocontainer').css("display", "none");
	  }
	});
});



