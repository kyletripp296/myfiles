<?php 
/**
 * @package Trancos_keywords
 * @version 1.1
 */
/*
Plugin Name: Trancos Keywords
Description: Prevent posts from saving/publishing without keywords set
Author: Kyle Tripp
Version: 1.1
*/
add_action( 'write_post', 'trancos_keywords_hook', 10, 3 );
add_action( 'edit_post', 'trancos_keywords_hook', 10, 3 );
//this function checks for the existence of meta keywords when we attempt to write or edit a post
function trancos_keywords_hook( $post_id ) {
	//ignore trashing and untrashing an item
	if(strstr($_GET['action'],'trash')){
		return;
	}
	if(count($_POST['meta'])){
		foreach($_POST['meta'] as $meta_id=>$thismeta){
			if($thismeta['key']=='keywords' && !empty($thismeta['value'])){
				//we can continue attempting to write or edit this post
				return;
			}
		}
	}
	//if we get here, we should stop trying to write/edit and throw an error
	wp_die('Please enter some keywords into the "custom fields" box','Keywords Error',array('back_link'=>true));
	exit;
}