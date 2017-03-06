	<?php
	$move_matrix = array(
		0=>array(1,2),
		1=>array(2,3),
		2=>array(3,4),
		3=>array(4,5),
		4=>array(5,6),
		5=>array(6,7),
		6=>array(7,8),
		7=>array(8,9),
		8=>array(9,10),
		9=>array(10,11),
		10=>array(11,12),
		11=>array(12,13),
		12=>array(13,14),
		13=>array(14,15),
		14=>array(15),
		15=>array(),
	);

	$paths = 0;
	$initial_state = array(array(0));
	$iterations = iterate($initial_state);
	echo $paths.' total paths';

	function iterate($array){
		global $move_matrix;
		global $paths;
		$next_iteration = array();
		for($i=0;$i<count($array);$i++){
			$thispath = $array[$i];
			$current = $thispath[count($thispath)-1];
			foreach($move_matrix[$current] as $thisnextmove){
				$temp = $thispath;
				$temp[] = $thisnextmove;
				$next_iteration[] = $temp;
				if($thisnextmove==15){
					$paths++;
					echo implode(',',$temp).'<br>';
				}
			}
		}
		if(!empty($next_iteration)){
			$array = iterate($next_iteration);
		}
		return $array;
	}