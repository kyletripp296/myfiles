<?php 
/*
https://projecteuler.net/problem=35
Circular primes
Problem 35

The number, 197, is called a circular prime because all rotations of the digits: 197, 971, and 719, are themselves prime.

There are thirteen such primes below 100: 2, 3, 5, 7, 11, 13, 17, 31, 37, 71, 73, 79, and 97.

How many circular primes are there below one million?


Answer:55
takes ~2-3 seconds
*/

//get all primes under 1M
$primes_arr = array();
for($i=2;$i<1000000;$i++){
	if(isPrime($i)){
		$primes_arr[] = $i;
	}
}

//check each prime to see if it is circular
$c_arr = array();
foreach($primes_arr as $thisprime){
	if(circularPrime($thisprime)){
		$c_arr[] = $thisprime;
	}
}
echo count($c_arr)."\r\n";



//say we're given the number 123
//we create an array like (231,312) by constantly shifting the first digit to the end of the string
//then test to see if all the numbers in that array are prime
function circularPrime($n){
	$test_arr = array();
	$temp = $n;
	for($i=0;$i<strlen($n);$i++){
		$temp = substr($temp,1).substr($temp,0,1);
		$test_arr[] = $temp;
	}
	foreach($test_arr as $thisprime){
		if(!isPrime($thisprime)){
			return false;
		}
	}
	return true;
}


//given a number n, test if it is a prime number and return bool
function isPrime($num) {
	if($num == 2){
		return true;
	}
	if($num == 1 || $num % 2 == 0){
		return false;
	}
	for($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
		if($num % $i == 0){
			return false;
		}
	}
	return true;
}