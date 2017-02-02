<?php
/*
https://projecteuler.net/problem=20
Factorial digit sum
Problem 20

n! means n × (n − 1) × ... × 3 × 2 × 1

For example, 10! = 10 × 9 × ... × 3 × 2 × 1 = 3628800,
and the sum of the digits in the number 10! is 3 + 6 + 2 + 8 + 8 + 0 + 0 = 27.

Find the sum of the digits in the number 100!
*/

echo array_sum(str_split(number_format(factorial(100),0,'','')))."\r\n";

function factorial($number) {
	if ($number < 2) {
		return 1;
	} else {
		return ($number * factorial($number-1));
	}
}