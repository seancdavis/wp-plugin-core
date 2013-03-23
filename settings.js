jQuery(document).ready(function($) {
	$('.rt-color').wpColorPicker(); // controls the color pickers
	// current highlighted nav
	var currentMenuItem = $('#nav_control').attr('value');
	if( currentMenuItem != '' ) {
		$('.rt-settings-section').hide();
		$('#' + currentMenuItem).show();
		$('.rt-settings-tab').removeClass('rt-settings-tab-selected');
		$('#tab_' + currentMenuItem).addClass('rt-settings-tab-selected');
	}
	// click control	
	$('.rt-settings-tab').click(function(){
		var id = $(this).attr('id').substr(4);
		$('.rt-settings-tab').removeClass('rt-settings-tab-selected');
		$(this).addClass('rt-settings-tab-selected');
		$('.rt-settings-section').hide();
		$('#' + id).show();
		$('#nav_control').attr('value', id);
	});	
	$('#tab_').hide();
});