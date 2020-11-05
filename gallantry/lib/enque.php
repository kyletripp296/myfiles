<?php

//Enqueue The Assets
function my_assets() {
	// wp_enqueue_style( 'vendor-style', get_template_directory_uri() . '/public/css/vendor.css' );
	wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/public/css/site.css' );
	wp_enqueue_script( 'theme-script', get_template_directory_uri() . '/public/js/site.js' );
}
add_action( 'wp_enqueue_scripts', 'my_assets' );



 ?>
