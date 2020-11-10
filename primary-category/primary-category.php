<?php 
/**
 * Plugin Name: Primary Category
 * Plugin URI: https://kyletripp.com
 * Description: Add the ability to set a primary category for each post and filter the frontend based on selection
 * Version: 1.0
 * Author: Kyle Tripp
 * Author URI: https://kyletripp.com
 * License: GPL2
 */

Namespace KTPC;

define('PRIMARY_META_KEY','ktpc_primary');

add_action('plugins_loaded', function(){
	$KTPC = new KT_Primary_Category();
});

class KT_Primary_Category {
	public function __construct(){
		/* actions */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_styles') );
		add_action( 'save_post', array( $this, 'save_primary_category') );
		add_action( 'pre_get_posts', array( $this, 'apply_primary_category') );
	}

	/**
	 * Enqueue scripts and styles on the admin side
	 */
	public function admin_scripts_styles(){
		$js_uri 	= plugin_dir_url(__FILE__) . 'js/pc-admin.js';
		$js_dir 	= dirname(__FILE__) . '/js/pc-admin.js';
		wp_register_script('pc-admin-js', $js_uri, null, filemtime($js_dir), true);
		wp_enqueue_script('pc-admin-js');

		global $post;
		$post_id = (isset($post->ID)) ? $post->ID : null;
		$meta = ($post_id) ? get_post_meta($post_id, PRIMARY_META_KEY, true) : false;
		$primary = ($meta) ? $meta : 1; 
		wp_localize_script( 'pc-admin-js', 'ktpc', 
			array(
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
				'postID' => $post_id,
				'primary' => $primary,
				'primary_text' => __('Primary'),
				'make_text' => __('Make Primary'),
			)
		);

		$css_uri 	= plugin_dir_url(__FILE__) . 'css/pc-admin-style.css';
		$css_dir 	= dirname(__FILE__) . '/css/pc-admin-style.css';
		wp_register_style('pc-admin-css', $css_uri, null, filemtime($css_dir));
		wp_enqueue_style('pc-admin-css');
	}

	public function save_primary_category($post_id){
    	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if( !current_user_can( 'edit_post' ) ) return;
	    $primary = (isset($_POST['primary_category'])) ? $_POST['primary_category'] : 0;
        update_post_meta( $post_id, PRIMARY_META_KEY, $primary );
	}

	public function apply_primary_category($query){
		if(!is_admin() && $query->is_main_query() && $query->is_category){
			$o = get_queried_object();
			$category_id = $o->term_id;
			$query->set('meta_query',array(
				array(
					'key' => PRIMARY_META_KEY,
					'value' => $category_id,
					'compare' => '='
				)
			));
		}
	}
}