<?php 
ini_set('memory_limit','2G');
$arr = range(1,9);
while(strlen($arr[0])<9){
	$newarr = array();
	foreach($arr as $thisarr){
		for($i=1;$i<=9;$i++){
			$newarr[] = $thisarr.$i;
		}
	}
	$arr = $newarr;
}
echo count($newarr);

/* function pc_next_permutation($p) {
	for ($i = count($p) - 1; $p[$i] >= $p[$i+1]; --$i) { }
	if ($i == -1) { return false; }
	for ($j = count($p); $p[$j] <= $p[$i]; --$j) { }
	$tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
	for (++$i, $j = count($p); $i < $j; ++$i, --$j) {
		 $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
	}
	$p = array_filter($p,function($value) {return ($value !== null && $value !== false && $value !== ''); });
	$p = array_values($p);
	return $p;
} */