<?php 

//8 exercises
//each time can choose .3 or .5
//add this value to Difficulty score
//100% chance to add .3 to Execution score
//50% chance to add .5 to Execution score
//Difficulty score + Execution score >= 13

$strings_arr = array('C','E');
while(strlen($strings_arr[0])<8){
	$temp = array();
	foreach($strings_arr as $thisstr){
		$temp[] = $thisstr.'C';
		$temp[] = $thisstr.'E';
	}
	$strings_arr = $temp;
}
//echo '<pre>'.print_r($strings_arr,true).'</pre>';

$results_arr = array();
foreach($strings_arr as $key=>$thisstr){
	$difficulty = 0;
	$execution_max = 10;
	$execution_min = 10;
	$thisstr_arr = str_split($thisstr);
	foreach($thisstr_arr as $thisletter){
		if($thisletter=='C'){
			$difficulty += 0.3;
		} else {
			$difficulty += 0.5;
			$execution_min -= 0.25;
		}
	}
	//echo $thisstr.'=>'.$difficulty.','.$execution.','.$chance.'<br>';
	if($difficulty+$execution_max>=13){
		$results_arr[] = array('string'=>$thisstr,'diff'=>$difficulty,'combined_min'=>$difficulty+$execution_min,'combined_max'=>$difficulty+$execution_max);
	}
}

//usort($results_arr,function($a,$b){return (float)$a['combined']-(float)$b['combined'];});

echo '<pre>'.print_r($results_arr,true).'</pre>';