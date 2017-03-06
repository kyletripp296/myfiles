<?php 
/*
https://projecteuler.net/problem=30
Digit fifth powers
Problem 30

Surprisingly there are only three numbers that can be written as the sum of fourth powers of their digits:

1634 = 1^4 + 6^4 + 3^4 + 4^4
8208 = 8^4 + 2^4 + 0^4 + 8^4
9474 = 9^4 + 4^4 + 7^4 + 4^4
As 1 = 1^4 is not a sum it is not included.

The sum of these numbers is 1634 + 8208 + 9474 = 19316.

Find the sum of all the numbers that can be written as the sum of fifth powers of their digits.


prints correct answer 443839
takes ~1 second
values: 4150,4151,54748,92727,93084,194979
*/

//create array of 5th powers
for($i=0;$i<=9;$i++){
	$pow_arr[$i] = pow($i,5);
}

//keep a grand total
$gt = 0;
//loop up to 1 million
for($i=2;$i<1000000;$i++){
	//local total
	$total = 0;
	//split $i into an array of digits
	$arr = str_split($i);
	//use pow arr to lookup values for digits
	foreach($arr as $thisn){
		$total += $pow_arr[$thisn];
	}
	//if total equals itself, add this number to grand total
	if($total==$i){
		$gt += $i;
	}
}

//print answer once done
echo $gt."\r\n";