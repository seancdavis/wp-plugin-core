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
	
	// control for post-type list where user can choose a post from this post type
	if( $('.rt-post-type-list').length > 0 ) {
		var ptBkgColor = '#DFDFDF';
		$('.rt-post-type-list').each(function(){
			var listID = $(this).attr('id');
			var id = $('#list-' + listID).attr('value');
			$('#' + id).css('background', ptBkgColor);
		});
		$('.rt-post-type-list li').click(function(){
			var listID = $(this).parent().attr('id');
			var id = $(this).attr('id');
			$('#list-' + listID).attr('value',id)
			$('.rt-post-type-list li').css('background','none');
			$(this).css('background', ptBkgColor);
		});
	}
	
});