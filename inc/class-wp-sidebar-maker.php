<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Wp sidebar maker main class
*/


class Wp_Sidebar_Maker{


	public function __construct(){

		$this->post_type = 'wp_sidebar_maker';
		$this->column = 'shortcode';
		$this->shortcode = 'wp_sidebar_maker';
		add_action('init', array($this, 'create_post_type'));
		add_filter( 'gettext', array($this,'change_excerpt_text'), 10, 2 );
		add_action('widgets_init', array($this,'create_sidebars'));
		add_filter('manage_'.$this->post_type.'_posts_columns',array($this,'create_column'));
		add_filter('manage_'.$this->post_type.'_posts_custom_column',array($this,'show_column_data'), 10, 2 );
		add_shortcode($this->shortcode,array($this,'get_shortcode'));
	}


	/**
	 * For plugin activation
	*/
	public function activate_plugin(){
		// $this->make_widgets();
		flush_rewrite_rules(); 
	}

	/**
	 * For plugin deactivation
	*/
	public function deactivate_plugin(){
		unregister_post_type($this->post_type);
		flush_rewrite_rules(); 
	}

	/**
	 * For register the widget post type
	*/
	public function create_post_type(){
		$supports = array(
			'title', // post title
			'author', // post author
			'excerpt', // post excerpt
			'revisions', // post revisions
		);
		$labels = array(
			'name' => _x('Sidebars', 'plural'),
			'singular_name' => _x('Sidebar', 'singular'),
			'menu_name' => _x('Sidebars', 'admin menu'),
			'name_admin_bar' => _x('Sidebars', 'admin bar'),
			'add_new' => _x('Add New Sidebar', 'add new'),
			'add_new_item' => __('Add New Sidebar'),
			'new_item' => __('New Sidebar'),
			'edit_item' => __('Edit Sidebar'),
			'view_item' => __('View Sidebar'),
			'all_items' => __('All Sidebars'),
			'search_items' => __('Search Sidebar'),
			'not_found' => __('No sidebar found.'),
		);
		$args = array(
			'supports' => $supports,
			'labels' => $labels,
			'public' => true,
			'query_var' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-format-aside',
		);
		register_post_type($this->post_type, $args);
	}

	/**
	 * @param $translation, $original
	 * For change post excerpt text
	 * @return $translation
	*/
	public function change_excerpt_text($translation, $original){
		if (get_post_type() == $this->post_type) {
	        if ('Excerpt' == $original) {
	            return 'Add Sidebar Description';
	        } else {
	            $pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
	            if ($pos !== false) {
	                return  'Add the custom sidebar discription here';
	            }
	        }
	    }
	    return $translation;
	}


	/**
	 * For create the sidebars
	*/
	public function create_sidebars(){
		global $post;
		$args = array(
		    'post_type'  => $this->post_type,
		    'orderby'    => 'id',
		    'order'      => 'ASC',
		    'post_status' => 'publish',
		    'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );
		if($query->have_posts()):
		    while($query->have_posts()) : $query->the_post();
		    	$title = get_the_title($post->ID);
		    	$id = $post->post_name.'-'.$post->ID;
		    	$description = get_the_excerpt($post->ID);
		    	$this->register_sidebar($title,$id,$description);
			endwhile;
		endif;
		wp_reset_postdata(); 
	}

	/**
	 * @param $title,$id,$description
	 * For register the widgets
	*/

	public function register_sidebar($title,$id,$description){
		register_sidebar( array(
	        'name' => $title,
	        'id' => $id,
	        'description' => $description,
	        'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
	    ));
	}


	/**
	 * For create the admin table column
	*/
	public function create_column($columns){
		return array_merge($columns, [$this->column => ucfirst($this->column)]);
	}

	/**
	 * For show the admin table column data
	*/
	public function show_column_data($column_key, $post_id){
		if ($column_key == $this->column) {
			echo '<input type="text" value="['.$this->shortcode.' id='.$post_id.']" disabled>';
		}
	}

	/**
	 * For sidebar shortcode
	 * @return sidebar
	*/
	 public function get_shortcode($attr){
	 	$html = '';	
	 	if(isset($attr['id'])){
	 		$data = get_post($attr['id']);
	 		$sidebar_id = $data->post_name.'-'.$data->ID;
	 		if($data->post_type != $this->post_type) return;
	 		if(is_active_sidebar($sidebar_id)){
		 		// Displays the Sidebar.
				ob_start();
				dynamic_sidebar( $sidebar_id );
				$sidebar_out = ob_get_clean();
				$html .= '<div class="wp_sidebar_maker_wrapper" id="wp_sidebar_maker_'.$data->ID.'" data-id="'.$data->ID.'">';
		 		$html .= $sidebar_out;
		 		$html .= '</div>';
				return $html;
	 		}else{
	 			$html .= 'Sidebar not found';
	 		}
	 	}
	 	return $html;
	 }
}
