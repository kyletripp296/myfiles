<?php
$GLOBALS['timberContext'] = Timber::context();
$context = $GLOBALS['timberContext'];
$context['header'] = new Timber\Menu( 'header-menu' );
$GLOBALS['context'] = $context;
$linkText = get_field( 'lets_talk_button_text', 'options' );
$linkUrl = get_field( 'lets_talk_button_link', 'options' );
$context['headercta'] = array(
	'text' => $linkText,
	'url' => $linkUrl,
);
Timber::render( '/resources/views/globals/header.twig', $context );



