<?php

/*
Visual
  O O O
 O O O O
O * O * O
 O O O O
  O O O

Numerical
		[03]	[08]	[13]
	[01]	[06]	[11]	[16]
[00]	[04]	[09]	[14]	[18]
	[02]	[07]	[12]	[17]
		[05]	[10]	[15]
*/
$move_matrix = array(
	0=>array(1,2),
	1=>array(3,4),
	2=>array(4,5),
	3=>array(6),
	4=>array(6,7),
	5=>array(7),
	6=>array(8,9),
	7=>array(9,10),
	8=>array(11),
	9=>array(11,12),
	10=>array(12),
	11=>array(13,14),
	12=>array(14,15),
	13=>array(16),
	14=>array(16,17),
	15=>array(17),
	16=>array(18),
	17=>array(18),
	18=>array()
);

$initial_state = array(array(0));
$iterations = iterate($initial_state);
$paths=0;
foreach($iterations as $thispath){
	$stars = 0;
	if(in_array(4,$thispath)){$stars++;}
	if(in_array(14,$thispath)){$stars++;}
	if($stars!=2){$paths++;}
}
echo $paths;//prints 30

function iterate($array){
	global $move_matrix;
	$next_iteration = array();
	for($i=0;$i<count($array);$i++){
		$thispath = $array[$i];
		$current = $thispath[count($thispath)-1];
		foreach($move_matrix[$current] as $thisnextmove){
			$temp = $thispath;
			$temp[] = $thisnextmove;
			$next_iteration[] = $temp;
		}
	}
	if(!empty($next_iteration)){
		$array = iterate($next_iteration);
	}
	return $array;
}