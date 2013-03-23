jQuery(document).ready(function($) {
	// Help text is placed in 'id' attribute. This controls showing and hiding help text as needed.
	$('input, textarea').focus(function() {
		if( $(this).attr('class') != 'rt-mrcf-submit' && $(this).attr('id') == $(this).attr('value') ) {
			$(this).attr('value','');
			$(this).addClass('rt-input-blur');
		}
	});
	$('input, textarea').blur(function() {
		if( $(this).attr('class') != 'rt-mrcf-submit' && $(this).attr('id') != $(this).attr('value') && $(this).attr('value') != '' ) $(this).addClass('rt-input-blur');
		else if( $(this).attr('value') == '' ) {
			$(this).attr( 'value',$(this).attr('id') );
			$(this).removeClass('rt-input-blur');
		}
	});
	$('.rt-mrcf-submit').click(function(){
		$('input, textarea').each(function(){
			if( $(this).attr('id') == $(this).attr('value') ) $(this).attr('value','');
		});
	});
	$('.rt-mrcf').validate();
});