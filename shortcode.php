<?php
global $mrcf_gs;

extract( shortcode_atts( array(
			'some_att' => '',
), $atts ) );
$email = $_POST['rt_mrcf_email'];
$topic = $_POST['rt_mrcf_topic'];
$msg = $_POST['rt_mrcf_msg'];

// Create post object
if( $msg != '' ) :
	$new_msg = array(
	  'post_title'    => $topic . ' (' . $email . ')',
	  'post_content'  => $msg,
	  'post_status'   => 'publish',
	  'post_author'   => $email,
	  'post_type' => 'rt_mrcf'
	);
	
	$error_msg = 'Error submitting form.';
	
	// Insert the post into the database
	wp_insert_post( $new_msg, $error_msg ); ?>
	<div class="rt-success"><span>&#x2713;</span>Message submitted successfully. We will be in touch soon.</div>
	<?php 
	if( $mrcf_gs->option('email_notification') == true ) {
		$to = $mrcf_gs->option('email');
		$subject = get_bloginfo('name') . ": " . $topic;
		$from = $email;
		$headers = "From:" . $from;
		mail($to,$subject,$msg,$headers); 
	}
	
endif; ?>

<form class="rt-mrcf" name="rt_mrcf" method="post">
	<label class="rt-mrcf-email">Your Email Address*</label>&nbsp;<input id="Enter your email address." name="rt_mrcf_email" class="rt-mrcf-input required email" type="text" value="Enter your email address." size="30">
	<label class="rt-mrcf-topic">Topic*</label>&nbsp;<input id="What is your message about?" name="rt_mrcf_topic" class="required rt-mrcf-input" type="text" value="What is your message about?" size="30">
	<label class="rt-mrcf-msg">Message*</label>&nbsp;<textarea id="Have a question or request? Enter it here..." class="required rt-mrcf-input" type="text" name="rt_mrcf_msg">Have a question or request? Enter it here...</textarea>
	<label class="rt-mrcf-required">*Required fields</label>
	<input type="submit" value="<?php echo $mrcf_gs->option('button_text'); ?>" class="rt-mrcf-submit">
</form>