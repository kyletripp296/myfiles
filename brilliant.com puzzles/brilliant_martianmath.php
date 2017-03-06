<?php 
$string = '325*73*9*52*+++';
$arr = str_split($string);
do{
	echo implode(' ',$arr).'<br>';
	$arr = evaluate($arr);
}while(count($arr)>1);
echo $arr[0];//prints 212


function evaluate($arr){
	$i=0;
	while($i<count($arr)-2){
		if($arr[$i+2]=='+'){
			$arr[$i] += $arr[$i+1];
			break;
		} elseif($arr[$i+2]=='-'){
			$arr[$i] -= $arr[$i+1];
			break;
		} elseif($arr[$i+2]=='*'){
			$arr[$i] *= $arr[$i+1];
			break;
		} elseif($arr[$i+2]=='/'){
			$arr[$i] /= $arr[$i+1];
			break;
		}
		$i++;
	}
	unset($arr[$i+1]);
	unset($arr[$i+2]);
	$arr = array_values($arr);
	return $arr;
}