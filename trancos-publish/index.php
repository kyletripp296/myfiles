<?php 
/*
Plugin Name: Trancos Publish
Plugin URI: https://www.trancos.com
Description: Streamline the production process from writers to editors to social media team
Version: 1.3
Author: ktripp
*/

/////////////////////////////////////////////////
/// Initial setup

/* prevent direct access */
defined( 'ABSPATH' ) or die('-1');

/* define email addresses for editor and social team */
define('SOCIAL_EMAIL','social@trancos.com');
define('EDITOR_EMAIL','michelle.woodward@trancos.com');

/// END
/////////////////////////////////////////////////
/// Check if we clicked either of our custom submit buttons
/// If so, we will redirect away from the page

if(isset($_POST['sendto_social'])){
	if(ctype_digit($_POST['post_ID']) && !empty($_POST['fbwall']) && ctype_digit($_POST['fbpos'])){
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?post='.$_POST['post_ID'].'&action=edit&sendto_social=try&meta_key=fbwall&meta_value='.trim($_POST['fbwall']).'-'.trim($_POST['fbpos']);
		header('Location: '.$url);exit;
	} else {
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?post='.$_POST['post_ID'].'&action=edit&sendto_social=fail';
		header('Location: '.$url);exit;
	}
} elseif(isset($_POST['sendto_editor'])){
	if(ctype_digit($_POST['post_ID']) && ctype_digit($_POST['article_num'])){
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?post='.$_POST['post_ID'].'&action=edit&sendto_editor=try&meta_key=article-num&meta_value='.trim($_POST['article_num']);
		header('Location: '.$url);exit;
	} else {
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'?post='.$_POST['post_ID'].'&action=edit&sendto_editor=fail';
		header('Location: '.$url);exit;
	}
}

/// END
/////////////////////////////////////////////////
/// Functions that echo HTML (you can modify the html if needed)

/* callback functions, these can echo html into our meta box */
function trancos_admin_callback() {
	trancos_editor_callback();
	trancos_author_callback();
}
function trancos_editor_callback() {
	echo <<<HTML
<select name="fbwall" style="width:100%;">
	<option value='' disabled selected>-- Select FB Wall --</option>
	<option value='fc'>Front Cover</option>
	<option value='nx2'>NX2</option>
	<option value='mp'>Mommypage</option>
	<option value='hp'>Healthypage</option>
	<option value='rtp'>Rewind The Past</option>
	<option value='wsi'>Worth Sharing It</option>
</select>
<input type="text" name="fbpos" placeholder="Position #" style="width:100%;">
<p style="text-align:right">
	<input type="submit" name="sendto_social" class="button button-primary button-large" id="sendto_social" value="Send to Social Media Team" />
</p>
HTML;
}
function trancos_author_callback() {
	echo <<<HTML
<input type="text" name="article_num" placeholder="Article #" style="width:100%;">
<p style="text-align:right">
	<input type="submit" name="sendto_editor" class="button button-primary button-large" id="sendto_editor" value="Send to Editor" />
</p>
HTML;
}

/* admin notices, display success or errors */
function sendto_editor_fail() {
	echo <<<HTML
<div class="error notice">
	<p>Sending email to Editor failed.</p>
	<p>Please ensure that you have given your post a title and entered a value for facebook-copy in the custom fields.</p>
</div>
HTML;
}
function sendto_social_fail() {
	echo <<<HTML
<div class="error notice">
	<p>Sending email to Social Media Team failed.</p>
	<p>Please ensure that the post is Published and that you have selected a FB Wall and Position # for this post.</p>
</div>
HTML;
}
function sendto_editor_success() {
	echo <<<HTML
<div class="updated notice">
	<p>Email sent to Editor.</p>
</div>
HTML;
}
function sendto_social_success() {
	echo <<<HTML
<div class="updated notice">
	<p>Email sent to Social Media Team.</p>
</div>
HTML;
}

/// END
/////////////////////////////////////////////////
/// Database functions
/// These ONLY work for nano sites

function db4s1_connect($slave_or_master = ''){
	$config_nano = (stristr($_SERVER['SERVER_NAME'],'stg2')) ? '/home/ktripp/public_html/shared_lib/config_nano.php' : '/var/www/microsites/shared_lib/config_nano.php';
	$micro2_db_slave = (stristr($_SERVER['SERVER_NAME'],'stg2')) ? '/home/ktripp/public_html/dbs/mysqli_db4s1' : '/var/www/dbs/mysqli_db4s1';
	$micro2_db_master = (stristr($_SERVER['SERVER_NAME'],'stg2')) ? '/home/ktripp/public_html/dbs/mysqli_db4m' : '/var/www/dbs/mysqli_db4m';
	define('MICRO2_DB_SLAVE',$micro2_db_slave);
	define('MICRO2_DB_MASTER',$micro2_db_master);
	(@require_once $config_nano) or die("could not find file $config_nano");
	$mysqli_db4m = connect_master();
	return ($slave_or_master == 'master') ? $mysqli_db4m : $mysqli_db4s1;
}

/* given a postid for the nano db, grab the main category */
function get_siteid_from_postid($postid){
	global $mysqli_db4s1;
	if(empty($mysqli_db4s1)){
		$mysqli_db4s1 = db4s1_connect();
	}
	$postid_sql = $mysqli_db4s1->real_escape_string($postid);
	$sql = "select t.slug from wp_terms t left join wp_term_taxonomy tt on t.term_id=tt.term_id left join wp_term_relationships tr on tr.term_taxonomy_id=tt.term_taxonomy_id where tt.taxonomy='category' and tt.parent='0' and t.slug!='uncategorized' and t.slug!='videos' and tr.object_id='$postid_sql' limit 1";
	$result = nano_query($sql,'nano_db','slave');
	if($result->num_rows){
		$row = $result->fetch_assoc();
		return $row['slug'];
	}
	return false;
}

/* for a slug like 'wsi', we return the description of that category, example:'worthsharingit.com' */
function get_nano_url($slug){
	global $mysqli_db4s1;
	if(empty($mysqli_db4s1)){
		$mysqli_db4s1 = db4s1_connect();
	}
	$slug_sql = $mysqli_db4s1->real_escape_string($slug);
	$sql = "select t.slug,tt.description from wp_term_taxonomy tt left join wp_terms t on t.term_id=tt.term_id where tt.taxonomy='category' and t.slug='$slug_sql' limit 1";
	$result = nano_query($sql,'nano_db','slave');
	if($result->num_rows){
		$row = $result->fetch_assoc();
		return 'http://www.'.strtolower($row['description']);
	}
	return false;
}

/// END
/////////////////////////////////////////////////
/// Wordpress functions
/// These MUST be hooked in using add_action

/* check the users privelege level; add meta box for authors and editors */
function trancos_publisher_metabox() {
	$current_user = wp_get_current_user();
	$user_info = get_userdata($current_user->ID);
	if(in_array('administrator',$user_info->roles)){
		add_meta_box( 'trancos_meta','Trancos Publish', 'trancos_admin_callback', 'post' );
	} elseif(in_array('editor',$user_info->roles)){
		add_meta_box( 'trancos_meta','Trancos Publish', 'trancos_editor_callback', 'post' );
	} elseif(in_array('author',$user_info->roles)){
		add_meta_box( 'trancos_meta','Trancos Publish', 'trancos_author_callback', 'post' );
	}
}

/// END
/////////////////////////////////////////////////
/// Wordpress actions
/// Here we can hook in certain functions to wordpress to be ran

/* when wordpress is going through and adding its meta boxes, we want to say "add this one too" */
add_action( 'add_meta_boxes', 'trancos_publisher_metabox' );

//try
if($_GET['sendto_editor']=='try'){
	add_action( 'wp_loaded', 'send_email_to_editor' );
} elseif($_GET['sendto_social']=='try'){
	add_action( 'wp_loaded', 'send_email_to_social' );
//success
} elseif($_GET['sendto_editor']=='success'){
	add_action( 'admin_notices', 'sendto_editor_success' );
} elseif($_GET['sendto_social']=='success'){
	add_action( 'admin_notices', 'sendto_social_success' );
//fail
} elseif($_GET['sendto_editor']=='fail'){
	add_action( 'admin_notices', 'sendto_editor_fail' );
} elseif($_GET['sendto_social']=='fail'){
	add_action( 'admin_notices', 'sendto_social_fail' );
}

/// END
/////////////////////////////////////////////////
/// Email functions
/// These get added when $_GET variable sendto_editor or sendto_social equals 'try'
/// These get ran once wordpress finishes loading
/// Will redirect user to either 'success' or 'fail'

function send_email_to_social(){
	//get required info
	$postid = trim($_GET['post']);
	$meta_key = trim($_GET['meta_key']);
	$meta_value = trim($_GET['meta_value']);
	
	//update post meta
	update_post_meta($postid,$meta_key,$meta_value);
	
	//more required info
	$title = get_the_title($postid);
	$postmeta = get_post_meta($postid);
	$fbcopy = get_fbinfo($postmeta,'facebook-copy');
	$fbwall = get_fbinfo($postmeta,'fbwall');
	$slug = get_post_field('post_name',get_post($postid));
	$image = wp_get_attachment_url(get_post_thumbnail_id($postid));
	
	//prepare urls
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$redirect_fail = $url.'?post='.$postid.'&action=edit&sendto_social=fail';
	$redirect_success = $url.'?post='.$postid.'&action=edit&sendto_social=success';
	
	//make sure vars not empty
	if(empty($postid) || empty($title) || empty($fbcopy) || empty($fbwall) || empty($slug) || empty($image)){
		header('Location: '.$redirect_fail);exit;
	}
	
	//use site_url to build permalink
	$site_url = site_url();
	if(stristr($site_url,'mommypage') || stristr($site_url,'mp_wp') || stristr($site_url,'healthypage') || stristr($site_url,'hp_wp') ){
		$permalink = $site_url.get_the_date('/m/Y/',$postid).$slug;
	} elseif(stristr($site_url,'glamourpage') || stristr($site_url,'gp_wp')){
		$permalink = $site_url.$slug;
	} elseif(stristr($site_url,'nx2') || stristr($site_url,'nx2_wp')){
		$permalink = $site_url.'/a/nx2/'.$slug.'-'.$postid;
	} elseif(stristr($site_url,'nano.trancospages') || stristr($site_url,'nano_wp')){
		$siteid = get_siteid_from_postid($postid);
		$permalink = get_nano_url($siteid).'/'.$slug;
	}
	
	//second check for empty vars
	if(empty($permalink)){
		header('Location: '.$redirect_fail);exit;
	}
	
	//send email
	$user_email = get_user_email();
	$to = SOCIAL_EMAIL;
	list($wall_id,$wall_num) = explode('-',$fbwall,2);
	
	$img_path = explode('/',$image);
	$img_path = array_slice($img_path,-3,3);
	$uploads = wp_upload_dir();
	$attachment = $uploads['basedir'].'/'.implode('/',$img_path);
	$content = file_get_contents($attachment);
	$content = chunk_split(base64_encode($content));
	$uid = md5(uniqid(time()));
	$filename = basename($attachment);

	// header
	$headers = "From: $user_email\r\n";
	$headers .= "Reply-To: $user_email\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	
	// subject
	$subject = strtoupper($wall_id).' '.date('l').' #'.$wall_num.': '.ucwords($title);
	$subject = html_entity_decode($subject,ENT_QUOTES,'UTF-8');
	$subject = str_replace('’',"'",$subject);
	$subject = str_replace('‘',"'",$subject);
	$subject = str_replace('–',"-",$subject);
	$subject = str_replace('—',"--",$subject);
	$subject = str_replace('“','"',$subject);
	$subject = str_replace('”','"',$subject);
	$subject = str_replace('…','...',$subject);
	
	// message & attachment
	$message = "--".$uid."\r\n";
	$message .= "Content-type:text/plain; charset=iso-8859-1\r\n";
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$message .= "FB Copy:\r\n$fbcopy\r\n\r\n\r\n$permalink\r\n\r\n\r\nimg:\r\n$image\r\n\r\n";
	$message .= "--".$uid."\r\n";
	$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
	$message .= "Content-Transfer-Encoding: base64\r\n";
	$message .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	$message .= $content."\r\n\r\n";
	$message .= "--".$uid."--";
	
	
	
	mail($to,$subject,$message,$headers);
	
	//redirect editor to this post with a success message and exit
	header('Location: '.$redirect_success);exit;
}

function send_email_to_editor(){
	//get required info
	$postid = trim($_GET['post']);
	$meta_key = trim($_GET['meta_key']);
	$meta_value = trim($_GET['meta_value']);
	
	//update post meta
	update_post_meta($postid,$meta_key,$meta_value);
	
	//more required info
	$title = get_the_title($postid);
	$postmeta = get_post_meta($postid);
	$fbcopy = get_fbinfo($postmeta,'facebook-copy');
	$article_num = get_fbinfo($postmeta,'article-num');
	$image = wp_get_attachment_url(get_post_thumbnail_id($postid));
	
	//prepare_urls
	$edit_url = admin_url('post.php?post='.$postid.'&action=edit');
	$redirect_fail = admin_url('post.php?post='.$postid.'&action=edit&sendto_editor=fail');
	$redirect_success = admin_url('edit.php?sendto_editor=success');
	
	//make sure vars not empty
	if(empty($postid) || empty($title) || empty($fbcopy) || empty($article_num) || empty($image)){
		header('Location: '.$redirect_fail);exit;
	}
	
	$site_url = site_url();
	if(stristr($site_url,'mommypage') || stristr($site_url,'mp_wp')){
		$siteid = 'MP';
	} elseif(stristr($site_url,'glamourpage') || stristr($site_url,'gp_wp')){
		$siteid = 'GP';
	} elseif(stristr($site_url,'healthypage') || stristr($site_url,'hp_wp')){
		$siteid = 'HP';
	} elseif(stristr($site_url,'nx2') || stristr($site_url,'nx2_wp')){
		$siteid = 'NX2';
	} elseif(stristr($site_url,'nano.trancospages') || stristr($site_url,'nano_wp')){
		$siteid = get_siteid_from_postid($postid);
	}
	
	//second check for empty vars
	if(empty($siteid)){
		header('Location: '.$redirect_fail);exit;
	}
	
	//send email
	$user_email = get_user_email();
	$to = EDITOR_EMAIL;
	
	$img_path = explode('/',$image);
	$img_path = array_slice($img_path,-3,3);
	$uploads = wp_upload_dir();
	$attachment = $uploads['basedir'].'/'.implode('/',$img_path);
	$content = file_get_contents($attachment);
	$content = chunk_split(base64_encode($content));
	$uid = md5(uniqid(time()));
	$filename = basename($attachment);

	// header
	$headers = "From: $user_email\r\n";
	$headers .= "Reply-To: $user_email\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	
	// subject
	$subject = strtoupper($siteid).' '.date('l').' #'.$article_num.': '.ucwords($title);
	$subject = html_entity_decode($subject,ENT_QUOTES,'UTF-8');
	$subject = str_replace('’',"'",$subject);
	$subject = str_replace('‘',"'",$subject);
	$subject = str_replace('–',"-",$subject);
	$subject = str_replace('—',"--",$subject);
	$subject = str_replace('“','"',$subject);
	$subject = str_replace('”','"',$subject);
	$subject = str_replace('…','...',$subject);

	// message & attachment
	$message = "--".$uid."\r\n";
	$message .= "Content-type:text/plain; charset=iso-8859-1\r\n";
	$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$message .= "FB Copy:\r\n$fbcopy\r\n\r\n\r\n$edit_url\r\n\r\n";
	$message .= "--".$uid."\r\n";
	$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
	$message .= "Content-Transfer-Encoding: base64\r\n";
	$message .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	$message .= $content."\r\n\r\n";
	$message .= "--".$uid."--";
	
	mail($to,$subject,$message,$headers);
	
	//redirect authors to 'all posts' and exit
	header('Location: '.$redirect_success);exit;
}

/// END
/////////////////////
/// Other functions

/* look through post meta for $meta_key, return value if found */
function get_fbinfo($arr,$meta_key){
	if(count($arr)){
		foreach($arr as $thismeta=>$value){
			if($thismeta==$meta_key){
				return $value[0];
			}
		}
	}
	return false;
}

/* looks at logged in wordpress user, returns their email */
function get_user_email(){
	$current_user = wp_get_current_user();
	$user_info = get_userdata($current_user->ID);
	return $current_user->user_email;
}