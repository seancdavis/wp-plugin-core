<?php

class GiftShop {
	
	public function __construct($args, $vals) {	
		// args
		$this->post_type = $args['post_type'];
		$this->title = $args['title'];
		$this->menu_title = $args['menu_title'];
		$this->menu_slug = $args['menu_slug'];
		
		// vals
		$this->options = $vals;

		if( $_GET['page'] == $this->menu_slug )
			add_action( 'admin_enqueue_scripts', array($this, 'load_admin_scripts') );
		add_action('admin_menu', array($this, 'register_page') );
		add_action('admin_init', array($this, 'admin_init') );
	}
	
	public function load_admin_scripts() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'settings-script', plugins_url().'/rocktree-core/rocktree-core/settings.js', array('jquery', 'farbtastic', 'wp-color-picker') );
		wp_enqueue_style( 'settings-style', plugins_url().'/rocktree-core/rocktree-core/settings.css' );
	}
	
	/* Page Registration
	-------------------------------------------------------------------------------- */
	public function register_page() {
		add_submenu_page('edit.php?post_type='.$this->post_type, $this->title, $this->menu_title, 'manage_options', $this->menu_slug, array($this, 'display_page') );
	}
	
	/* Sections/Fields Registration
	-------------------------------------------------------------------------------- */
	public function admin_init(){	
		register_setting( $this->post_type, $this->post_type, $this->validate_options() );
		foreach ($this->options as $sections => $section) {
			add_settings_section($sections, '', $this->settings_section(), $sections);
			foreach($section as $field) {
				add_settings_field(
					$field['name'], 
					$field['label'], 
					array($this, 'settings_field'), 
					$sections, 
					$sections,
					array($field['name'], $field['before'], $field['after'], $field['type']) 
				);
			}
		}	
	}
	
	/* Section Display
	-------------------------------------------------------------------------------- */
	public function settings_section() {
		// don't need additional content at this point, but THIS NEEDS TO STAY
	}
	
	/* Field Display
	-------------------------------------------------------------------------------- */
	public function settings_field( $args ) {
		echo $args[1]; // before content
		$option_name = $args[0];
		$current_option = $this->option($option_name);
		$label = $this->option_labels($option_name);
		
		global $post;
		
		switch($args[3]) { // $field['type'] from values array
		
			case 'boolean' : ?>
			<input id="boo-<?php echo $option_name; ?>" value=true type="checkbox" name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" <?php if( $current_option == true ) echo 'checked="checked"';?> >
			<?php break;
			
			case 'text' : ?>
			<input id="txt-<?php echo $option_name; ?>" type="text" name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" value="<?php echo $current_option; ?>" >
			<?php break;
			
			case 'radio' :
			$options = $this->option_choices($option_name);
			for( $i = 0; $i < count($options); $i++ ) {
				?><input id="rdio-<?php echo $options[$i]; ?>" type="radio" name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" value="<?php echo $options[$i]; ?>" <?php if($current_option == $options[$i]) echo 'checked="checked"'; ?>>
				<label for="rdio-<?php echo $options[$i]; ?>"><?php if( is_array($label) ) echo $label[$i]; else echo $options[$i]; ?></label><br><?php
			}
			break;
			
			case 'color' : ?>
			<input id="color-<?php echo $option_name; ?>" class="rt-color" name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" size="40" type="text" value="<?php echo $current_option; ?>">
			<?php break;
			
			case 'post_type_single_option' : ?>
			<input id="list-<?php echo $option_name; ?>" type="text" hidden name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" value="<?php echo $current_option; ?>" >
			<ul class="rt-post-type-list" id="<?php echo $option_name; ?>"><?php
				$loop = new WP_Query( array ( 'post_type' => $this->post_type, 'posts_per_page' => '100' ) );
				while ( $loop->have_posts() ) : $loop->the_post(); ?>
					<li id="<?php echo $post->ID; ?>"><?php the_title(); ?></li>
				<?php endwhile; ?>
			</ul>
			<?php break;
			
			/* MULTIPLE SELECT WILL GO HERE --
			case 'checkbox' : ?>
			<input id="checkbox-<?php echo $option_name; ?>" value=true type="checkbox" name="<?php echo $this->post_type; ?>[<?php echo $option_name; ?>]" <?php if( $current_option == true ) echo 'checked="checked"';?> >
			<label for="checkbox-<?php echo $option_name; ?>" id="label-<?php echo $option_name; ?>"><?php echo $label; ?></label>
			<?php break;
			 * */
			
		}
		echo $args[2]; // after content
	}
	
	/* Page Display
	-------------------------------------------------------------------------------- */
	public function display_page() { ?>
		<div>    
	        <h1><?php echo $this->title; ?></h1>
	        <form class="rt-settings-form" action="options.php" method="post">
	        	<?php if ($_GET['settings-updated']==true) _e( '<div id="message" class="updated"><p>Settings updated.</p></div>' ); ?>
	        	<hr class="rt-settings-line">
	        	<input hidden type="text" id="nav_control" name="<?php echo $this->post_type;?>[nav_control]" value="<?php if( $_GET['settings-updated'] == true ) echo $this->option('nav_control'); ?>" />
	        	<?php
				$tab_control = 1;
				settings_fields($this->post_type);
				foreach ($this->options as $key => $value) {
					$name = str_replace('_', ' ', $key); ?>			
					<a class="rt-settings-tab <?php if( $tab_control == 1 ) echo 'rt-settings-tab-selected'; ?>" id="tab_<?php echo $key; ?>"><?php echo $name; ?></a><?php
					$tab_control++; 
				}
				$tab_control = 1; ?>
				<hr class="rt-settings-line">
				<?php foreach ($this->options as $key => $value) {    			    			
	    			$name = str_replace('_', ' ', $key ); ?>					
					<div class="rt-settings-section <?php if( $tab_control == 1 ) echo 'rt-settings-section-selected'; ?>" id="<?php echo $key; ?>">
						<h2><?php echo $name; ?></h2>
						<?php do_settings_sections( $key ); ?>
					</div><?php 
					$tab_control++;
				} ?>
				<hr class="rt-settings-line">
				<?php submit_button('Save All Settings'); ?>

	        </form>
		
	    </div><?php
	}

	/* Get Values from The Values Array
	-------------------------------------------------------------------------------- */
	public function option( $option_name ) {
		$current_options = get_option($this->post_type);
		if( $option_name == 'nav_control' ) { // first if statement is a catch to help control navigation of settings page
			if( $current_options[$option_name] == '' ) '';		
			else return $current_options[$option_name];
		}
		else {
			foreach ($this->options as $sections) {
				foreach($sections as $section) {
					if( $section['name'] == $option_name ) $default = $section['default'];
				}
			}
			if( $current_options[$option_name] == '' ) return $default;
			else return $current_options[$option_name];
		}
	}
	
	/* Get choices -- for multiple select or radio options
	-------------------------------------------------------------------------------- */
	public function option_choices( $option_name ) {
		foreach ($this->options as $sections) {
			foreach($sections as $section) {
				if( $section['name'] == $option_name ) $choices = $section['choices'];
			}
		}
		return $choices;
	}
	
	/* Get labels -- for multiple select or radio options
	-------------------------------------------------------------------------------- */
	public function option_labels( $option_name ) {
		foreach ($this->options as $sections) {
			foreach($sections as $section) {
				if( $section['name'] == $option_name && $section['choice_labels'] == '' ) $choices = $section['label']; 
				else if( $section['name'] == $option_name ) $choices = $section['choice_labels'];
			}
		}
		return $choices;
	}
	
	/* Validate options -- need this function; it doesn't sanitize anything right now
	-------------------------------------------------------------------------------- */
	public function validate_options($input) {	
		return $input;
	}
	
	
}

?>