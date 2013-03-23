<?php
/*
Plugin Name: rocktree's Plugin Core Development
Plugin URI: n/a
Description: NOT FOR DISTRIBUTION. USED FOR rockree's DEV. 
Version: 0.1
Author: rocktree Design
Author URI: http://rocktreedesign.com
License: 
*/

/* Load Class Files
-------------------------------------------------------------------------------- */ 
require_once(dirname(__FILE__) . '/rt-core/rock.php');
require_once(dirname(__FILE__) . '/rt-core/gift-shop.php');

/* Load Custom Files
-------------------------------------------------------------------------------- */
/* load any other custom files here, such as widgets, APIs, or other server-side files

/* The Settings Values Array
-------------------------------------------------------------------------------- */
// The values array holds all the option values for a settings page. 
// This is an example of every type of option currently available.
$values = array(
	'Radio' => array( // section title (grouping of options --> *gets displayed on page)
		'radio_example' => array( // field title (not displayed on page)
			'name' => 'radio_example', // field name (*NEEDS TO MATCH FIELD TITLE -- **no spaces)
			'label' => 'Radio Example', // field label (what is displayed on the page)
			'type' => 'radio', // field type
			'choices' => array('choice_1', 'choice_2', 'choice_3'), // choices array (each value adds a radio button -- **NO SPACES)
			'choice_labels' => array('Choice 1', 'Choice 2', 'Choice 3'), // choice labels array (gets displayed on page -- corresponds to choices)
			'default' => 'choice_2', // the default style if left blank 
			'before' => '<p>HTML that gets displayed before your options</p>',
			'after' => '&nbsp;&nbsp;<i>HTML that gets displayed after your options</i>'
		),
	),
	'Boolean' => array(
		'boolean_example' => array(
			'name' => 'boolean_example',
			'label' => 'Boolean (Single Checkbox) Example',
			'type' => 'boolean',
			'default' => TRUE,
		),
	),
	'Text' => array(
		'text_example' => array(
			'name' => 'text_example',
			'label' => 'Text Example',
			'type' => 'text',
			'default' => 'Default Values',
		),
	),
	'Color' => array(
		'color_example' => array(
			'name' => 'color_example',
			'label' => 'Color Field Example',
			'type' => 'color',
			'default' => '#123def',
		),
	),
	
);

$gs_args = array(
	'post_type' => 'rt_mrcf',
	'title' => 'Contact Form Settings',
	'menu_title' => 'Settings',
	'menu_slug' => 'rt_mrcf_settings',
	'script_dir' => plugins_url() . '/moon-rock-contact-form/admin/settings.js',
	'style_dir' => plugins_url() . '/moon-rock-contact-form/admin/settings.css',
	
);


$mrcf_gs = new GiftShop($gs_args, $gs_vals);

$args = array(
	'name' => 'Contact Form',
	'prefix' => 'mrcf',
	'dir' => 'moon-rock-contact-form',
	'shortcode' => 'mrcf',
	'shortcode_dir' => dirname(__FILE__) . '/shortcode.php',
	'post_type' => 'rt_mrcf',
	'item' => 'Message',
	'description'   => 'Enables you to build and display a contact form, and saves all entries on your site',
	'menu_position' => 25,
	'script_dir' => plugins_url() . '/moon-rock-contact-form/scripts.js', 
	'style_dir' => plugins_url() . '/moon-rock-contact-form/style.css',
	'dynamic_style_dir' => dirname(__FILE__) . '/custom-style.php',
);

$moon_rock = new Rock($args);


?>