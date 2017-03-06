<?php 

$tests_ran = 0;
$test_success = 0;
for($i=0;$i<10000;$i++){
	$test_arr = array();
	while(count($test_arr)<3){
		do{
			$rand = mt_rand(0,359);
		} while(in_array($rand,$test_arr));
		$test_arr[] = $rand;
	}
	for($x=0;$x<180;$x++){
		if($test_arr[0]>=$x && $test_arr[0]<$x+180 && $test_arr[1]>=$x && $test_arr[1]<$x+180 && $test_arr[2]>=$x && $test_arr[2]<$x+180){
			$test_success++;
			break;
		}
	}
	$tests_ran++;
}

echo ($test_success/$tests_ran)*100;