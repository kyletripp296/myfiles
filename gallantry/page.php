<?php
get_header();
$context = $GLOBALS['context'];
$context = array($context);

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

    the_content();

	}
}

get_footer();
 ?>
