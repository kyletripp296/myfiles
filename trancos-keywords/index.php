<?php 
/**
 * @package Trancos_keywords
 * @version 1.1
 */
/*
Plugin Name: Trancos Keywords
Description: Prevent posts from saving/publishing without keywords set
Author: Kyle Tripp
Version: 1.2
*/
add_action( 'write_post', 'trancos_keywords_hook', 10, 3 );
add_action( 'edit_post', 'trancos_keywords_hook', 10, 3 );
//this function checks for the existence of meta keywords when we attempt to write or edit a post
function trancos_keywords_hook( $post_id ) {
	//ignore trashing and untrashing an item
	if(strstr($_GET['action'],'trash')){
		return;
	}
	//custom wordpress fields are submitted as $_POST['meta']
	if(count($_POST['meta'])){
		foreach($_POST['meta'] as $meta_id=>$thismeta){
			//we are looking for $_POST['meta']['keywords'] to exist and it cannot be empty
			if($thismeta['key']=='keywords' && !empty($thismeta['value'])){
				//we can continue attempting to write or edit this post
				return;
			}
		}
	}
	//if we get here, we should stop trying to write/edit and throw an error
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add keywords to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Scroll down to the Custom Fields textbox.</li>
	<ul>
		<li>If you do not see this, click the 'Screen Options' tab at the top and check the 'Custom Fields' box.</li>
	</ul>
	<li>Click the '--Select--' dropdown.</li>
	<ul>
		<li>If the value 'keywords' exists in that list, select it and skip the next step.</li>
		<li>If not, click 'Enter New' and type 'keywords' into the 'Name' field.</li>
	</ul>
	<li>Enter a comma separated list of keywords in the 'Value' field.</li>
	<ul>
		<li>Ex: 'javascript, programming, objects'</li>
	</ul>
	<li>Click 'Add Custom Field'.</li>
	<li>Resubmit post by clicking either 'Publish' or 'Update' in the top right.</li>
</ol>

INST;
	wp_die($instructions,'Keywords Error',array('back_link'=>true));
	exit;
}