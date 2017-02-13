<?php 
/**
 * @package Trancos_keywords
 * @version 1.3
 */
/*
Plugin Name: Trancos Keywords
Description: Makes sure the writers have entered the proper meta data before publishing or updating
Author: Kyle Tripp
Version: 1.3
*/

/*
to add a new hook
	1. define label for hook in $hook_arr (model)
	2. define logic for hook, return on good value, throw error on bad value (controller)
	3. define instructions for writers on how to fix specific error (view)
*/

//MODEL
add_action( 'write_post', 'trancos_automated_editor', 10, 3 );
add_action( 'edit_post', 'trancos_automated_editor', 10, 3 );
function trancos_automated_editor(){
	//ignore trashing and untrashing an item, ignore unpublished items
	if(strstr($_GET['action'],'trash') || $_POST['post_status']!='publish'){
		return;
	}
	
	$hook_arr = array('title','content','excerpt','featuredimg','keywords','description','photocredit');
	foreach($hook_arr as $thishook){
		call_editor_hook($thishook);
	}
}


//VIEW
function throw_editor_error( $label ){
	switch($label){
		case 'title':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add a title to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Locate the first input at the top of the page, below 'Edit Post'.</li>
	<li>Enter a catchy title in the input field.</li>
	<ul>
		<li>Ex: 'He Agrees To Pay $250 For His Pet Fish To Live. Now, The Fish Can Keep Swimming For Another Day.'</li>
	</ul>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		break;
		case 'content':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add content to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Locate the big textbox in the middle of your screen</li>
	<li>Enter some high quality content into there.</li>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		break;
		case 'excerpt':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add an excerpt to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Scroll down to the Excerpt textbox.</li>
	<ul>
		<li>If you do not see this, click the 'Screen Options' tab at the top and check the 'Excerpt' box.</li>
	</ul>
	<li>Enter a quick summary of the article in the input field.</li>
	<ul>
		<li>Ex: 'Bob the goldfish escapes death after his owner agree to remove the tumor on his fin through an expensive surgery. Now, Bob can join the ranks of other goldfishes who lived way beyond their prime. Read on for more details!'</li>
	</ul>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		case 'featuredimg':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add a featured image to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Scroll down to the Featured Image textbox on the right sidebar.</li>
	<ul>
		<li>If you do not see this, click the 'Screen Options' tab at the top and check the 'Featured Image' box.</li>
		<li>If you do not see the 'Featured Image' box, consult with tech.</li>
	</ul>
	<li>Click 'Set featured image'</li>
	<ul>
		<li>If the image you want already exists, highlight it</li>
		<li>Otherwise, click the 'Upload Files' tab and click 'Select Files', then find the file from your computer.</li>
		<li>Click the blue button 'Set featured image'</li>
	</ul>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		break;
		case 'keywords':
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
	<li>Click the '--Select--' dropdown</li>
	<ul>
		<li>If the value 'keywords' exists in that list, select it and skip the next step</li>
		<li>If not, click 'Enter New' and type 'keywords' into the 'Name' field</li>
	</ul>
	<li>Enter a comma separated list of keywords in the 'Value' field.</li>
	<ul>
		<li>Ex: 'javascript, programming, objects'</li>
	</ul>
	<li>Click 'Add Custom Field'</li>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		break;
		case 'description':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add a description to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Scroll down to the Custom Fields textbox.</li>
	<ul>
		<li>If you do not see this, click the 'Screen Options' tab at the top and check the 'Custom Fields' box.</li>
	</ul>
	<li>Click the '--Select--' dropdown</li>
	<ul>
		<li>If the value 'description' exists in that list, select it and skip the next step</li>
		<li>If not, click 'Enter New' and type 'description' into the 'Name' field</li>
	</ul>
	<li>Enter a good SEO description of your article in the 'Value' field.</li>
	<ul>
		<li>Ex: 'This convenient cutlery holder is perfect for all types of parties!'</li>
	</ul>
	<li>Click 'Add Custom Field'</li>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		break;
		case 'photocredit':
	$instructions = <<<INST
<h2 style="text-align:center">WAIT!</h2>

<h3 style="text-align:center">You forgot to add a photo credit to your post.</h3>

<p>To fix this, follow these steps:</p>
<ol>
	<li>Return to the post using the link below.</li>
	<li>Scroll down to the Custom Fields textbox.</li>
	<ul>
		<li>If you do not see this, click the 'Screen Options' tab at the top and check the 'Custom Fields' box.</li>
	</ul>
	<li>Click the '--Select--' dropdown</li>
	<ul>
		<li>If the value 'photo-credit' exists in that list, select it and skip the next step</li>
		<li>If not, click 'Enter New' and type 'photo-credit' into the 'Name' field</li>
	</ul>
	<li>Enter text or a link in the 'Value' field.</li>
	<ul>
		<li>Ex: '<a href="https://twitter.com/TM2LeahDawn/media">Photo Copyright © 2016 @TM2LeahDawn/Twitter</a>
'</li>
		<li>Note: If you have entered a photo credit that you believe is valid and are still getting this error, please consult with tech</li>
	</ul>
	<li>Click 'Add Custom Field'</li>
	<li>Resubmit post by clicking either 'Publish' or 'Update'</li>
</ol>

INST;
		default: $instructions = 'Unknown error';
	}
	wp_die($instructions,ucfirst($label).' Error',array('back_link'=>true));
	exit;
}


//CONTROLLER
function call_editor_hook($label){
	switch($label){
		case 'title':
			if(isset($_POST['post_title']) && !empty($_POST['post_title'])){
				//we can continue attempting to write or edit this post
				return;
			}
		break;
		case 'content':
			if(isset($_POST['post_content']) && !empty($_POST['post_content'])){
				//we can continue attempting to write or edit this post
				return;
			}
		break;
		case 'excerpt':
			if(isset($_POST['excerpt']) && !empty($_POST['excerpt'])){
				//we can continue attempting to write or edit this post
				return;
			}
		break;
		case 'featuredimg':
			if(isset($_POST['_thumbnail_id']) && $_POST['_thumbnail_id']!=-1){
				//we can continue attempting to write or edit this post
				return;
			}
		break;
		case 'keywords':
			if(count($_POST['meta'])){
				foreach($_POST['meta'] as $meta_id=>$thismeta){
					//we are looking for $_POST['meta']['keywords'] to exist and it cannot be empty
					if($thismeta['key']=='keywords' && !empty($thismeta['value'])){
						//we can continue attempting to write or edit this post
						return;
					}
				}
			}
		break;
		case 'description':
			if(count($_POST['meta'])){
				foreach($_POST['meta'] as $meta_id=>$thismeta){
					//we are looking for $_POST['meta']['description'] to exist and it cannot be empty
					if($thismeta['key']=='description' && !empty($thismeta['value'])){
						//we can continue attempting to write or edit this post
						return;
					}
				}
			}
		break;
		case 'photocredit':
			$regex = '@(<a ((href|target|rel)="[^"]+"\s?+)+>)?[a-zA-Z0-9-\'?!:\./© ]+(</a>)?@';
			if(count($_POST['meta'])){
				foreach($_POST['meta'] as $meta_id=>$thismeta){
					//we are looking for $_POST['meta']['description'] to exist and it cannot be empty
					if($thismeta['key']=='photo-credit' && preg_match($regex,$thismeta['value'])){
						//we can continue attempting to write or edit this post
						return;
					}
				}
			}
		break;
	}
	throw_editor_error($label);
}