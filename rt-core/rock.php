<?php

class Rock {
	
	public function __construct($args)
	{		
		$this->dir = plugins_url() . '/' . $args['dir'];
		$this->shortcode = $args['shortcode'];
		$this->name = $args['name'];
		$this->item = $args['item'];
		$this->description = $args['description'];
		$this->menu_position = $args['menu_position'];
		$this->post_type = $args['post_type'];
		$this->prefix = $args['prefix'];
		$this->shortcode_dir = $args['shortcode_dir'];
		$this->style_dir = $args['style_dir'];
		$this->script_dir = $args['script_dir'];
		$this->dynamic_style_dir = $args['dynamic_style_dir'];
		
		add_action( 'init', array($this, 'register_plugin') );			
		add_filter('the_posts', array($this, 'load_conditional_scripts') );
		add_shortcode( $this->shortcode, array($this, 'display_shortcode') );
		add_action( 'wp_print_styles', array($this, 'print_dynamic_stylesheet') );
	}

	public function register_plugin() {
		
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
		
		$args = array(
			'labels'        => $labels,
			'description'   => $this->description,
			'public'        => true,
			'menu_position' => $this->menu_position,
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'   => true,
		);
		
		register_post_type( $this->post_type, $args );
	}
	
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
	
	public function display_shortcode( $atts ) {
		if( $this->shortcode_found == true && $atts == 'widget' ) { ?>
			<p>Please use form in body of page.</p>
		<?php }
		else {
			include_once $this->shortcode_dir;
		}
	}
	
	public function print_dynamic_stylesheet() {
		include_once $this->dynamic_style_dir;
	}
	
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
		$gradient .= 'background-color: ' . $top_color . '; ';
		$gradient .= 'background: -webkit-gradient(linear, left top, left bottom, from('.$top_color.'), to('.$bottom_color.') );';
		$gradient .= 'background: -webkit-linear-gradient(top, '.$top_color.', '.$bottom_color.');';
		$gradient .= 'background: -moz-linear-gradient(top, '.$top_color.', '.$bottom_color.');';
		$gradient .= 'background: -ms-linear-gradient(top, '.$top_color.', '.$bottom_color.');';
		$gradient .= 'background: -o-linear-gradient(top, '.$top_color.', '.$bottom_color.');';
		$gradient .= 'background: linear-gradient(top, '.$top_color.', '.$bottom_color.');';
		$gradient .= 'filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='.$top_color.', endColorstr='.$bottom_color.', GradientType=0);';
		return $gradient;
	}

}


?>