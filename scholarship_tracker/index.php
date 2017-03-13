<?php
/**
 * @package scholarship_tracker
 * @version 1.1
 */
/*
Plugin Name: Scholarship Tracker
Plugin URI: http://wordpress.org/plugins/
Description: Custom Plugin
Author: Kyle Tripp
Version: 1.1
Author URI: http://github.com/kyletripp296
*/

/////////////////////////////////////////////
// MAIN

	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	global $course_id;
	$mysqli = connect_to_db();
	$user_id = $mysqli->real_escape_string(get_user_id());
	$post_id = $mysqli->real_escape_string(get_post_id());
	if(is_course($post_id)){
		$course_id = $post_id;
		if(!$user_id){
			$cid = (ctype_digit($post_id)) ? '?cid='.$course_id : '';
			header('Location: http://www.kyletripp.com/prhacker/register/'.$cid);exit;
		}
	}elseif(is_scholarship($post_id)){
		$scholarship_id = $post_id;
	}
	//activation and deactivation hooks
	register_activation_hook( __FILE__, 'install' );
	register_deactivation_hook( __FILE__, 'cleanup' );
	//admin menu hook
	add_action('admin_menu','generate_admin_menu');
	//filter content and track view
	if(ctype_digit($scholarship_id)){
		add_filter('the_content','add_applynow');
		if(ctype_digit($user_id)){
			add_action('wp_loaded','track_view');
		}
	}
	
	
////////////////////////////////////////////
// FUNCTIONS

//call this the first time plugin gets initialized
function install(){
	create_tables();
}

//call this when plugin gets removed (be careful with this one)
function cleanup(){
	reset_tracker();
	reset_prextra();
	drop_tables();
}

//add a page to the admin menu, giving admins the ability to see stats
function generate_admin_menu(){
	add_options_page( 'Tracker Options', 'Scholarship Tracker', 'manage_options', 'scholarship-tracker', 'get_admin_menu_page' );
}

//This is the HTML that will echo out on the admin menu page
function get_admin_menu_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo get_table('scholarship_tracker');
	echo get_table('prhacker_extra');
}

//we call this on the end of the post to add an apply now button to any scholarship articles
function add_applynow($content){
	global $mysqli;
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	//is user logged in?
	if(ctype_digit($user_id)){
		//check if they have applied already
		$userid_sql = $mysqli->real_escape_string($user_id);
		$scholarshipid_sql = $mysqli->real_escape_string($scholarship_id);
		$sql = "select 1 from scholarship_tracker where user_id='$userid_sql' and scholarship_id='$scholarshipid_sql' and applied='t'";
		$result = $mysqli->query($sql);
		if($result->num_rows){
			$applied = true;
		} else {
			//check if we have extra info
			$sql = "select 1 from prhacker_extra where user_id='$userid_sql'";
			$result = $mysqli->query($sql);
			if($result->num_rows){
				$path = 'apply-now/?sid='.$scholarship_id;
			} else {
				$path = 'get-extra-info/?sid='.$scholarship_id;
			}
		}
	} else {
		$path = 'register/?sid='.$scholarship_id;
	}
	$button = ($applied) ? '<div class="applynow_button" style="padding:10px 20px;background-color:#00abab;"><p style="margin:0;font-size:14px;line-height:18px;color:#fff;">APPLIED!</p></div>' : '<div class="applynow_button" style="padding:10px 20px;background-color:green;"><p style="margin:0;font-size:14px;line-height:18px;color:#fff;">APPLY NOW</p></div>';
	$content .= '<div class="applynow" style="position:relative;display:block;">
		<div class="applynow_wrap" style="position:relative;display:inline-block;margin:15px 0;">
			<a href="http://www.kyletripp.com/prhacker/'.$path.'">'.$button.'</a>
		</div>
	</div>';
	return $content;
	
}

//gimme a table name and ill return it formatted for you
function get_table($tablename){
	global $mysqli;
	$tablename_sql = $mysqli->real_escape_string($tablename);
	$sql = "select * from $tablename_sql";
	$result = $mysqli->query($sql);
	if($result->num_rows){
		$key_arr = array();
		while($row = $result->fetch_assoc()){
			$tb .= '<tr>';
			foreach($row as $key=>$value){
				if(!in_array($key,$key_arr)){
					$key_arr[] = $key;
					$th .= '<td>'.$key.'</td>';
				}
				$tb .= '<td>'.$row[$key].'</td>';
			}
			$tb .= '</tr>';
		}
		echo '<table><thead><tr>'.$th.'</tr></thead><tbody>'.$tb.'</tbody></table>';
	} else {
		echo 'No rows in '.$tablename.'<br>';
	}
}

//pass it a user_id and a scholarship_id
//if this is a new row, we insert it and start them with 1 view, otherwise we add one to the view count
function track_view(){
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	$sql = "insert into scholarship_tracker (user_id,scholarship_id,views) values ('$user_id','$scholarship_id','1') on duplicate key update views=views+1";
	$mysqli->query($sql);
}

//the owner of the scholarship will choose who they accept from the wp admin area, we send congratulations email
function grant_scholarship(){
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	$sql = "update scholarship_tacker set granted='t',granted_date=NOW() where user_id='$user_id' and scholarship_id='$scholarship_id'";
	$mysqli->query($sql);
}

//user responds to their congratulations email and accepts the offer, we mark that date
function accept_scholarship(){
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	$sql = "update scholarship_tacker set accepted='t',accepted_date=NOW() where user_id='$user_id' and scholarship_id='$scholarship_id'";
	$mysqli->query($sql);
}

//user responds to their congratulations email and rejects the offer, we mark that date
function reject_scholarship(){
	global $mysqli;
	global $user_id;
	global $scholarship_id;
	$sql = "update scholarship_tacker set accepted='d',accepted_date=NOW() where user_id='$user_id' and scholarship_id='$scholarship_id'";
	$mysqli->query($sql);
}

//we will return post_id only if we are on a single article and that article is in the scholarship category
function get_post_id(){
	global $mysqli;
	//we expect something like '/prhacker/scholarship-3/' for $_SERVER['REQUEST_URI']
	$request_arr = explode('/',$_SERVER['REQUEST_URI']);
	$slug = $request_arr[2];
	$slug_sql = $mysqli->real_escape_string($slug);
	$sql = "select ID from wp_posts where post_name='$slug_sql' and post_type='post' and post_status='publish'";
	$result = $mysqli->query($sql);
	if($result->num_rows){
		$row = $result->fetch_assoc();
		return $row['ID'];
	}
	return false;
}

//return true if post_id is in scholarships array
function is_scholarship($post_id){
	$scholarships_arr = get_scholarships_array();
	return (in_array($post_id,$scholarships_arr)) ? true : false;
}

//return true if post_id is in courses array
function is_course($post_id){
	$courses_arr = get_courses_array();
	return (in_array($post_id,$courses_arr) || preg_match('@category/virtual-courses@i',$_SERVER['REQUEST_URI'])) ? true : false;
}

//we can memcache this later to save time
function get_scholarships_array(){
	global $mysqli;
	$scholarships_arr = array();
	$sql = "select tr.object_id from wp_term_taxonomy tt left join wp_terms t on t.term_id=tt.term_id left join wp_term_relationships tr on tr.term_taxonomy_id=tt.term_taxonomy_id where tt.taxonomy='category' and t.slug='scholarships'";
	$result = $mysqli->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			$scholarships_arr[] = $row['object_id'];
		}
	}
	return $scholarships_arr;
}

//we can memcache this later to save time
function get_courses_array(){
	global $mysqli;
	$courses_arr = array();
	$sql = "select tr.object_id from wp_term_taxonomy tt left join wp_terms t on t.term_id=tt.term_id left join wp_term_relationships tr on tr.term_taxonomy_id=tt.term_taxonomy_id where tt.taxonomy='category' and t.slug='virtual-courses'";
	$result = $mysqli->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			$courses_arr[] = $row['object_id'];
		}
	}
	return $courses_arr;
}

//uses the wordpress login cookie to return a user_id
function get_user_id(){
	global $mysqli;
	foreach($_COOKIE as $thiscookie=>$value){
		if(preg_match('@^wordpress_logged_in_.+@',$thiscookie)){
			list($username,$discard) = explode('|',$value,2);
		}
	}
	if(!isset($username) || empty($username)){
		return false;
	} else {
		$username_sql = $mysqli->real_escape_string($username);
		$sql = "select ID from wp_users where user_login='$username_sql' limit 1";
		$result = $mysqli->query($sql);
		if($result->num_rows){
			$row = $result->fetch_assoc();
			return $row['ID'];
		}
	}
}

//returns a mysqli connection or throws an error and dies
function connect_to_db(){
	$path = ABSPATH.'wp-config.php';
	require_once $path;
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if($mysqli->connect_errno){
		echo "failed: ".$mysqli->connect_errno.'\n';exit;
	}
	return $mysqli;
}

//attempts to create tables, skips over any tables that already exist
function create_tables(){
	global $mysqli;
	//define tables
	$tables_arr = array(
		'scholarship_tracker'=>"create table scholarship_tracker(user_id int unsigned NOT NULL, scholarship_id int unsigned NOT NULL, views int unsigned DEFAULT 0, applied enum('t','f') DEFAULT 'f', applied_date datetime DEFAULT NULL, application_text varchar(2000) DEFAULT NULL, granted enum('t','f') DEFAULT 'f', granted_date datetime DEFAULT NULL, accepted enum('t','f','d') DEFAULT 'f', accept_date datetime DEFAULT NULL, PRIMARY KEY ('user_id','scholarship_id'))",
		'prhacker_extra'=>"create table prhacker_extra(user_id int unsigned PRIMARY KEY, grad_year int unsigned DEFAULT NULL, address varchar(256) DEFAULT NULL, college varchar(256) DEFAULT NULL)",
	);
	
	//list all existing tables
	$existing_arr = array();
	$sql = "show tables";
	$result = $mysqli->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			$existing_arr[] = $row['Tables_in_'.DB_NAME];
		}
	}
	$tablenames_arr = array_keys($tables_arr);
	foreach($existing_arr as $id=>$tablename){
		if(!in_array($tablename,$tablenames_arr)){
			unset($existing_arr[$id]);
		}
	}
	$existing_arr = array_values($existing_arr);
	
	//if table does not exist, create it
	$regex = '@^create table[^;]+$@i';
	foreach($tables_arr as $tablename=>$create_sql){
		if(!in_array($tablename,$existing_arr) && preg_match($regex,$create_sql)){
			$mysqli->query($create_sql);
		}
	}
}

//removes tables scholarship_tracker and prhacker_extra, use with care
function drop_tables(){
	global $mysqli;
	$sql = 'drop table scholarship_tracker';
	$result = $mysqli->query($sql);
	$sql = 'drop table prhacker_extra';
	$result = $mysqli->query($sql);
}

//removes all rows from scholarship_tracker, use with care
function reset_tracker(){
	global $mysqli;
	$sql = 'delete from scholarship_tracker';
	$result = $mysqli->query($sql);
}

//removes all rows from prhacker_extra, use with care
function reset_prextra(){
	global $mysqli;
	$sql = 'delete from prhacker_extra';
	$result = $mysqli->query($sql);
}