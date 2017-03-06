<?php
/*
https://projecteuler.net/problem=33
Digit cancelling fractions
Problem 33

The fraction 49/98 is a curious fraction, as an inexperienced mathematician in attempting to simplify it may incorrectly believe that 49/98 = 4/8, which is correct, is obtained by cancelling the 9s.

We shall consider fractions like, 30/50 = 3/5, to be trivial examples.

There are exactly four non-trivial examples of this type of fraction, less than one in value, and containing two digits in the numerator and denominator.

If the product of these four fractions is given in its lowest common terms, find the value of the denominator.
*/
$fraction_arr = array();
for($i=10;$i<=98;$i++){
	for($j=$i+1;$j<99;$j++){
		if(is_curious((string)$i,(string)$j)){
			$fraction_arr[] = array($i,$j);
		}
	}
}
$f = multiply_fractions($fraction_arr);
echo $f[1]."\r\n";

function is_curious($num,$den){
	//check for a in either the form ab/ca or ba/ac
	if($num[0]==$den[1]){
		$b = $num[1];
		$c = $den[0];
	}elseif($num[1]==$den[0]){
		$b = $num[0];
		$c = $den[1];
	}
	//check that ab/ca=b/c or ba/ac=b/c
	if(isset($b) && !empty($c) && ($num/$den)===($b/$c)){
		return true;
	}
	return false;
}

function multiply_fractions($arr){
	if(!count($arr) || !count($arr[0])){return false;}
	$num = $arr[0][0];
	$den = $arr[0][1];
	for($i=1;$i<count($arr);$i++){
		$num*=$arr[$i][0];
		$den*=$arr[$i][1];
		list($num,$den) = reduce($num,$den);
	}
	return array($num,$den);
}

function reduce($num,$den){
	for($i=$den;$i>=2;$i--){
		if($num%$i==0&&$den%$i==0){
			$num/=$i;
			$den/=$i;
		}
	}
	return array($num,$den);
}