<?php 

/*
mobile_cookie.php

Purpose:
We set a cookie to determine if a user is on their desktop computer or a mobile phone
Tablets, such as the ipad and galaxy tab, are classified as desktop devices
We can tailor user experience differently based on this cookie

Setting the 'is_mobile' cookie:
The cookie will set itself the first time a user comes to the page
We can force this cookie either on or off using $_GET['mobile']
	Force mobile: http://www.mysite.com?mobile=1
	Force desktop: http://www.mysite.com?mobile=0

Using the 'is_mobile' cookie:
It is simple to use this cookie in php or javascript code
	PHP - echo ($_COOKIE['is_mobile']) ? 'mobile version' : 'desktop version';
	JS - if(document.cookie.indexOf('is_mobile=1')!=-1){ alert('mobile version'); } else { alert('desktop version'); }

*/

if(!isset($_COOKIE['is_mobile'])||(isset($_GET['mobile']))){
	set_mobile_cookie();
}
function set_mobile_cookie(){
	$value = (is_phone_device()||(isset($_GET['mobile'])&&$_GET['mobile']==1)) ? 1 : 0;
	setcookie('is_mobile', $value, time() + 24*60*60, '/');
	$_COOKIE['is_mobile'] = $value;
}

function is_phone_device($user_agent = null) {
	$user_agent = $user_agent ?: $_SERVER['HTTP_USER_AGENT'];
	if( !is_desktop($user_agent) ){
		$phone_devices = array(
			'Android',
			'blackberry|\bBB10\b|rim tablet os',
			'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
			'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
			'Windows CE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window Mobile|Windows Phone [0-9.]+|WCE;',
			'Windows Phone 10.0|Windows Phone 8.1|Windows Phone 8.0|Windows Phone OS|XBLWP7|ZuneWP7',
			'\biPhone.*Mobile|\biPod',
			'MeeGo',
			'Maemo',
			'J2ME/|\bMIDP\b|\bCLDC\b',
			'webOS|hpwOS',
			'\bBada\b',
			'BREW',
			'\bCrMo\b|CriOS|Android.*Chrome/[.0-9]* (Mobile)?',
			'\bDolfin\b',
			'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
			'Skyfire',
			'IEMobile|MSIEMobile',
			'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile',
			'bolt',
			'teashark',
			'Blazer',
			'Tizen',
			'UC.*Browser|UCWEB',
			'DiigoBrowser',
			'Puffin',
			'\bMercury\b',
			'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger'
		);

		foreach( $phone_devices as $_regex ){
			if( preg_match('@'. $_regex .'@i', $user_agent) ){
				return true;
			}
		}
	}
	return false;
}

function is_desktop($user_agent = null) {
	$user_agent = $user_agent ?: $_SERVER['HTTP_USER_AGENT'];
	$os_array = array(
		'Windows NT',
		'[^l][^i][^k][^e]\s+Mac OS X',
		'Ubuntu|\(X11; Linux',
		'\biPad',
		'SCH-I605',
		'SM-N900A',
		'SGH-T889',
		'SPH-L900',
		'SM-N900V',
		'GT-P5210',
		'GT-P5113',
		'GT-P3113',
		'SM-900T',
		'SM-T210R',
		'Kindle|Silk.*Accelerated|Android.*\b(KFOT|KFTT|KFJWI|KFJWA|KFOTE|KFSOWI|KFTHWI|KFTHWA|KFAPWI|KFAPWA|WFJWAE)\b',
	);

	foreach( $os_array as $_regex ){
		if( preg_match('/'. $_regex .'/is', $user_agent) ){
			return true;
		}
	}
	return false;
}