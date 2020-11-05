<?php
//A couple of cute little helper functions to make our code feel DRY. LIKE THE FREAKING ATACAMA
function field( $field_name ) {
	global $context;
	$context['fields'][ $field_name ] = get_field( $field_name );
	return $context;
}

//We are good people. When we render, we dustbin the stuff we store in memory.
function render( $template_name ) {
	global $context;
	Timber::render( 'resources/views/components/' . $template_name . '.twig', $context );
	$context['fields'] = [];
}

//This is the heart and soul of our theme. It renders blocks. It is humble. It is beautiful. It is the Kristin Bell of codebases. Or the late-career Bradd Pitt. Take your pick. It's cool.
function field_switch( $block, $content = '', $is_preview = false, $post_id = 0 ) {
		// var_dump($block['name']);
	global $context;
	switch ( $block['name'] ) {

		case 'acf/cc-graf':
			field( 'btxt' );
			render( 'basic_text' );
			break;

		case 'acf/cc-hero-banner':
			field( 'dimensions' );
			field( 'tagline' );
			$context['fields']['image'] = '';
			if ( $image_id = get_field( 'image' ) ) {
				$context['fields']['image'] = new Timber\Image( $image_id );
			}
			field( 'cards' );
			render( 'hero_banner' );
			break;
	}
}

