<?php

class Rock {
	
	public function __construct($post_type_args, $meta_args = '')
	{		
		$this->dir = plugins_url() . '/' . $post_type_args['dir'];
		$this->shortcode = $post_type_args['shortcode'];
		$this->name = $post_type_args['name'];
		$this->item = $post_type_args['item'];
		$this->description = $post_type_args['description'];
		$this->menu_position = $post_type_args['menu_position'];
		$this->post_type = $post_type_args['post_type'];
		$this->prefix = $post_type_args['prefix'];
		$this->shortcode_dir = $post_type_args['shortcode_dir'];
		$this->style_dir = $post_type_args['style_dir'];
		$this->script_dir = $post_type_args['script_dir'];
		$this->dynamic_style_dir = $post_type_args['dynamic_style_dir'];
		$this->taxonomies = $post_type_args['taxonomies'];
		$this->custom_taxonomies = $post_type_args['custom_taxonomies'];
		
		$this->meta = $meta_args;
		$this->meta_id = $meta_args['setup']['id'];
		$this->meta_title = $meta_args['setup']['title'];
		
		add_action( 'init', array($this, 'register_post_type') );			
		add_filter('the_posts', array($this, 'load_conditional_scripts') );
		add_shortcode( $this->shortcode, array($this, 'display_shortcode') );
		add_action( 'wp_print_styles', array($this, 'print_dynamic_stylesheet') );
		add_action( 'add_meta_boxes', array($this, 'add_metabox') );
		add_action('save_post', array($this, 'save_metabox'), 1, 2);
		add_action( 'admin_enqueue_scripts', array($this, 'load_meta_scripts') );
		if( $this->custom_taxonomies ) add_action( 'init', array($this, 'custom_taxonomies') );
	}
	
	/* Post Type Registration
	-------------------------------------------------------------------------------- */
	public function register_post_type() {
		
		// Custom labels for admin
		$labels = array(
			'name'               => _x( $this->name, 'post type general name' ),
			'singular_name'      => _x( $this->name, 'post type singular name' ),
			'add_new'            => _x( 'Add New', $this->item ),
			'add_new_item'       => __( 'Add New '.$this->item ),
			'edit_item'          => __( 'Edit '.$this->item ),
			'new_item'           => __( 'New '.$this->item ),
			'all_items'          => __( 'All '.$this->item.'s' ),
			'view_item'          => __( 'View '.$this->item.'s' ),
			'search_items'       => __( 'Search '.$this->item.'s' ),
			'not_found'          => __( 'No '.$this->item.'s Found' ),
			'not_found_in_trash' => __( 'No '.$this->item.'s Found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => $this->name
		);
		
		$post_type_args = array(
			'labels'        => $labels,
			'description'   => $this->description,
			'taxonomies'	=> $this->taxonomies,
			'public'        => true,
			'menu_position' => $this->menu_position,
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'   => true,
		);
		
		register_post_type( $this->post_type, $post_type_args );
	}
	
	/* Load Conditional Scripts
	-------------------------------------------------------------------------------- */
	public function load_conditional_scripts($posts){
		if (empty($posts)) return $posts;	 
		$shortcode_found = false;
		foreach ($posts as $post) {
			if (stripos($post->post_content, '['.$this->shortcode.']') !== false) {
				$shortcode_found = true; 
				break;
			}
		}
		if ($shortcode_found) {
			wp_enqueue_style( $this->prefix.'-style', $this->style_dir );
			wp_enqueue_script( $this->prefix.'-scripts', $this->script_dir, array('jquery') );
			$this->shortcode_found = true;
		}
		return $posts;
	}
	
	/* Display Shortcode
	-------------------------------------------------------------------------------- */
	public function display_shortcode( $atts ) {
		if( $this->shortcode_found == true && $atts == 'widget' ) { ?>
			<p>Please use form in body of page.</p>
		<?php }
		else if( $atts == 'widget' ) include $this->shortcode_dir;
		else {
			ob_start();
			include $this->shortcode_dir;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
	
	/* Print Dynamic Stylesheet
	-------------------------------------------------------------------------------- */
	public function print_dynamic_stylesheet() {
		include_once $this->dynamic_style_dir;
	}
	
	/* Dynamic Stylesheet Help
	-------------------------------------------------------------------------------- */
	public function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);
	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = array($r, $g, $b);
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
	
	public function rgb2hex($rgb) {
	   $hex = "#";
	   $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
	   $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
	   return $hex; // returns the hex value including the number sign (#)
	}
	
	public function lighten($hex, $percent) {
		$rgb = hex2rgb($hex);
		for( $i = 0; $i < 3; $i++ ) $new_color[$i] = ceil( $rgb[$i] + ( (255 - $rgb[$i]) * ($percent / 100) ) );
		$hex = rgb2hex( array($new_color[0], $new_color[1], $new_color[2]) );
		return $hex;	
	}
	
	public function darken($hex, $percent) {
		$rgb = hex2rgb($hex);
		for( $i = 0; $i < 3; $i++ ) $new_color[$i] = ceil( $rgb[$i] - ( (255 - $rgb[$i]) * ($percent / 100) ) );
		$hex = rgb2hex( array($new_color[0], $new_color[1], $new_color[2]) );
		return $hex;	
	}
	
	public function gradient($bottom_color, $top_color = '') {
		if( $top_color == '' ) $top_color = lighten($bottom_color, '40');
		$gradient .= 'background-color: ' . $top_color . ' !important; ';
		$gradient .= 'background: -webkit-gradient(linear, left top, left bottom, from('.$top_color.'), to('.$bottom_color.') ) !important;';
		$gradient .= 'background: -webkit-linear-gradient(top, '.$top_color.', '.$bottom_color.') !important;';
		$gradient .= 'background: -moz-linear-gradient(top, '.$top_color.', '.$bottom_color.') !important;';
		$gradient .= 'background: -ms-linear-gradient(top, '.$top_color.', '.$bottom_color.') !important;';
		$gradient .= 'background: -o-linear-gradient(top, '.$top_color.', '.$bottom_color.') !important;';
		$gradient .= 'background: linear-gradient(top, '.$top_color.', '.$bottom_color.') !important;';
		$gradient .= 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='.$top_color.', endColorstr='.$bottom_color.', GradientType=0) !important;';
		return $gradient;
	}
	
	/* Register Meta Box
	-------------------------------------------------------------------------------- */
	public function add_metabox() {
	    add_meta_box($this->meta_id, $this->meta_title, array($this, 'metabox_content'), $this->post_type, 'normal', 'core');
	}
	
	public function load_meta_scripts() {
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'meta-script', plugins_url().'/rocktree-core/rocktree-core/meta.js', array('jquery', 'farbtastic', 'wp-color-picker') );
	}
	
	public function metabox_content() {
		global $post;
		// Noncename needed to verify where the data originated
		?><input type="hidden" name="<?php echo $this->post_type; ?>" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" /><?php
		foreach ($this->meta as $key => $value) {
			if( $key != 'setup' ) {				
				$meta_value = get_post_meta($post->ID, $key, true);
				if( $value['title'] != '' ) : ?><h4 class="feature-sub-title"><?php echo $value['title']; ?></h4><?php endif;
				echo $value['before'];
				switch( $value['type'] ) {
					case 'text' : ?> 
					<label for="<?php echo $key; ?>"><?php echo $value['label']; ?></label>
					<input type="text" name="<?php echo $key; ?>" value="<?php echo $meta_value; ?>">
					<?php break;
					
					case 'media' : ?>
					<label for="<?php echo $key; ?>"><?php echo $value['label']; ?></label>
					<input type="text" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $meta_value; ?>">
					<a class="rt-meta-button button" name="<?php echo $key; ?>_button" id="<?php echo $key; ?>_button">Add Media</a>
					<?php break;
					
					case 'boolean' : ?>
					<label for="<?php echo $key; ?>"><?php echo $value['label']; ?></label>
					<input type="checkbox" name="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php if($meta_value == true) : ?>checked='checked'<?php endif; ?>>
					<?php break;
				}
				echo $value['after']; ?>
				<br><br><?php
			}
		}	
	}
	
	public function save_metabox($post_id, $post) {
		// verify this came from the our screen and with proper authorization,
	    // because save_post can be triggered at other times
		if ( !wp_verify_nonce( $_POST[$this->post_type], plugin_basename(__FILE__) )) {
			return $post->ID;
		}
	    // Is the user allowed to edit the post or page?
	    if ( !current_user_can( 'edit_post', $post->ID ) ) return $post->ID;

		foreach ($this->meta as $key => $value) {
			if( $key != 'setup' ) {	
				if( $value['type'] == 'text' ) $meta_values[$key] = sanitize_text_field( $_POST[$key] );
				//else if( $_POST[$name] == '' ) $meta_values[$name] = ''; 
				else $meta_values[$key] = $_POST[$key];	
			}
		}
		
		/* update or add meta values
		------------------------------------------------------------------------- */
	    foreach ($meta_values as $key => $value) { // Cycle through the $feature_meta
	        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
	        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
	        if(get_post_meta($post->ID, $key, FALSE)) update_post_meta($post->ID, $key, $value); // If the custom field already has a value
			else add_post_meta($post->ID, $key, $value); // If the custom field doesn't have a value
	        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	    }

	}

	public function custom_taxonomies() {
		$tax = $this->custom_taxonomies;
		foreach( $tax as $key => $value ) {
			if( $value['hierarchical'] == true ) $hi = true; else $hi = false;
			$single = $value['singular'];
			$slug = strtolower( str_replace(' ', '-', $key) );
			
			$labels = array(
			    'name'                => _x( $key, 'taxonomy general name' ),
			    'singular_name'       => _x( $key, 'taxonomy singular name' ),
			    'search_items'        => __( 'Search ' . $key ),
			    'all_items'           => __( 'All '.$key ),
			    'parent_item'         => __( 'Parent '.$single ),
			    'parent_item_colon'   => __( 'Parent '.$single.':' ),
			    'edit_item'           => __( 'Edit '.$single ), 
			    'update_item'         => __( 'Update '.$single ),
			    'add_new_item'        => __( 'Add New '.$single ),
			    'new_item_name'       => __( 'New '.$single.' Name' ),
			    'menu_name'           => __( $key )
			  ); 	
			
			  $args = array(
			    'hierarchical'        => $hi,
			    'labels'              => $labels,
			    'show_ui'             => true,
			    'show_admin_column'   => true,
			    'query_var'           => true,
			    'rewrite'             => array( 'slug' => $slug )
			  );
			register_taxonomy( $slug, $this->post_type, $args );
		}
	}

}


?>