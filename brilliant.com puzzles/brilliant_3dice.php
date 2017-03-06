<?php
$array = array();
for($i=1;$i<=6;$i++){
	for($j=1;$j<=6;$j++){
		for($k=1;$k<=6;$k++){
			$array[] = $i+$j+$k;
		}
	}
}
echo sd($array);
//prints "2.9649110736106"

function sd_square($x, $mean) { return pow($x - $mean,2); }
function sd($array) { return sqrt(array_sum(array_map("sd_square", $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) ); }
