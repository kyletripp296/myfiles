<?php 
/**
 * Plugin Name: CC Focus Point
 * Plugin URI: https://computercourage.com
 * Description: Add the ability to set focus points on images and display focused images on the front end
 * Version: 2.0
 * Author: Computer Courage (webstaff@computercourage.com)
 * Author URI: https://computercouarge.com
 * License: GPL2
 */

Namespace CCFP;

define('CCFP_META_KEY','focus_point');

add_action('plugins_loaded', function(){
	$ccfp = new CC_Focus_Point();
});

class CC_Focus_Point {
	public function __construct(){
		/* Filters */
		add_filter( 'manage_media_columns', array( $this, 'columns' ) );
		add_filter( 'manage_upload_sortable_columns', array( $this, 'sortable_column' ) );
		add_filter('wp_get_attachment_image_attributes', array($this, 'add_coord_attributes'), 20, 3);
		add_filter( 'attachment_fields_to_edit', array($this, 'focus_point_attachment_fields'), 10, 2 );
		/* Actions */
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
		/* Ajax */
		add_action('wp_ajax_get_coords', array($this, 'get_default_coords'));
		add_action('wp_ajax_nopriv_get_coords', array($this, 'get_default_coords'));
		add_action('wp_ajax_save_coords', array($this, 'save_coords'));
		add_action('wp_ajax_nopriv_save_coords', array($this, 'save_coords'));
	}

	/**
	 * Enqueue scripts and styles on the admin side
	 */
	public function admin_scripts_styles(){
		$js_uri 	= plugin_dir_url(__FILE__) . 'js/ccfp-admin-dist.js';
		$js_dir 	= dirname(__FILE__) . '/js/ccfp-admin-dist.js';
		wp_register_script('ccfp-admin-js', $js_uri, null, filemtime($js_dir), true);
		wp_enqueue_script('ccfp-admin-js');

		wp_localize_script( 'ccfp-admin-js', 'ajaxURL', admin_url( 'admin-ajax.php' ) );

		$css_uri 	= plugin_dir_url(__FILE__) . 'css/ccfp-admin-style.css';
		$css_dir 	= dirname(__FILE__) . '/css/ccfp-admin-style.css';
		wp_register_style('ccfp-admin-css', $css_uri, null, filemtime($css_dir));
		wp_enqueue_style('ccfp-admin-css');
	}

	/**
	 * Enqueue scripts and styles on the front end
	 */
	public function enqueue_scripts_styles(){
		$js_uri 	= plugin_dir_url(__FILE__) . 'js/ccfp-dist.js';
		$js_dir 	= dirname(__FILE__) . '/js/ccfp-dist.js';
		wp_register_script('ccfp-js', $js_uri, null, filemtime($js_dir), true);
		wp_enqueue_script('ccfp-js');

		wp_localize_script( 'ccfp-js', 'ajaxURL', admin_url( 'admin-ajax.php' ) );

		$css_uri 	= plugin_dir_url(__FILE__) . 'css/ccfp-style.css';
		$css_dir 	= dirname(__FILE__) . '/css/ccfp-style.css';
		wp_register_style('ccfp-css', $css_uri, null, filemtime($css_dir));
		wp_enqueue_style('ccfp-css');
	}

	/**
	 * Get coordinates for an attachment id, use post meta if set otherwise use 50,50
	 *
	 * @return array  json encoded x and y values
	 */
	public function get_default_coords(){
		$post_id = trim($_POST['post_id']);
		$post_meta = get_post_meta($post_id,CCFP_META_KEY,true);
		$x = 50;
		$y = 50;
		if(isset($post_meta) && strpos($post_meta,',')!==false){
			list($x,$y) = explode(',',$post_meta,2);
		}
		echo json_encode(array('x'=>$x,'y'=>$y));
		wp_die();
	}

	/**
	 * Save coordinates for an attachment id
	 *
	 * @return array  json encoded success value
	 */
	public function save_coords(){
		$post_id = trim($_POST['post_id']);
		$x = trim($_POST['x']);
		$y = trim($_POST['y']);
		$success = update_post_meta($post_id,CCFP_META_KEY,$x.','.$y);
		echo json_encode(array('success'=>$success));
		wp_die();
	}

	/**
	 * Get coordinates for an attachment id, use post meta if set otherwise use 50%, 50%
	 *
	 * @param array 	$attr 			Attributes to add to <img> tag
	 * @param array 	$attachment 	Information about attached image like ID
	 * @param string 	$size 			Image size name like 'full' or 'large' 	
	 *
	 * @return array  updated attributes array
	 */
	public function add_coord_attributes($attr, $attachment, $size){
		$post_meta = get_post_meta($attachment->ID,CCFP_META_KEY,true);
		if(isset($post_meta) && strpos($post_meta,',')!==false){
			list($x,$y) = explode(',',$post_meta,2);
			$attr['class'] .= ' focused-image';
			$attr['style'] = sprintf('object-position: %s %s;', $x.'%', $y.'%');
		}
		return $attr;
	}

	/**
	 * Adds a button to the wp media sidebar, clicking it triggers the set focus point js feature
	 *
	 * @param array 	$form_fields 	Array of fields to add to the sidebar
	 * @param array 	$post 			Information about the current attachment being edited
	 *
	 * @return array  updated array of fields to add to the sidebar
	 */
	public function focus_point_attachment_fields( $form_fields, $post ) {
		$form_fields['cc_focus_point'] = array(
			'label' => 'Focus Point',
			'input' => 'html',
			'html' => sprintf('<a href="#" class="button-secondary set-focus-point" data-id="%s">%s</a>', $post->ID, __('Set focus point','ccfp')),
			'helps' => __('To set a focus point on this image, click the link and select the subject to focus on','ccfp'),
		);
		return $form_fields;
	}
}