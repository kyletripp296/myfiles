<?php 
$s=0;
for($a=0;$a<10000;$a++){
	$h=0;
	$t=0;
	for($i=0;$i<100;$i++){
		$rand = mt_rand(0,99);
		if(!$rand){
			$t++;
		} else {
			$h++;
		}
	}
	if($h==99){
		$s++;
	}
}
echo '~'.floor($s/100).'%';