<?php 

//we have 2 boxes (a and b)
//we have 10 balls (0,1,2,3,4,5,6,7,8,9)

//how many ways can we put the balls into the boxes?
//each box must have at least 1 ball

//so to start we can put between 1-9 balls in box a
//then we can put between 1 and however many are left in box b
//we can describe the number of balls in a and b as f(a,b)
//f(1,1) is like saying (10 choose 1) + (9 choose 1)
//f(1,2) is like saying (10 choose 1) + (9 choose 2)
//f(2,1) is like saying (10 choose 2) + (8 choose 1)
//etc.
//we should be able to verify f(1,1)=19
//so we need to run f() for every a 1-9 and every b 1-9, if a+b>10 we can skip

$sum = 0;
for($a=1;$a<=9;$a++){
	for($b=1;$b<=9;$b++){
		if($a+$b<=10){
			$sum += f_of_ab($a,$b);
		}
	}
}
echo 'Total: '.$sum;

function f_of_ab($a,$b){
	$sum = n_choose_r(10,$a)+n_choose_r(10-$a,$b);
	echo 'f('.$a.','.$b.')='.$sum.'<br>';
	return $sum;
}

function n_choose_r($n,$r){
	return factorial($n)/(factorial($r)*factorial($n - $r));
}

function factorial($n) { 
	if ($n < 2) {
		return 1;
	} else {
		return ($n * factorial($n-1));
	}
}

/*
prints:
f(1,1)=19
f(1,2)=46
f(1,3)=94
f(1,4)=136
f(1,5)=136
f(1,6)=94
f(1,7)=46
f(1,8)=19
f(1,9)=11
f(2,1)=53
f(2,2)=73
f(2,3)=101
f(2,4)=115
f(2,5)=101
f(2,6)=73
f(2,7)=53
f(2,8)=46
f(3,1)=127
f(3,2)=141
f(3,3)=155
f(3,4)=155
f(3,5)=141
f(3,6)=127
f(3,7)=121
f(4,1)=216
f(4,2)=225
f(4,3)=230
f(4,4)=225
f(4,5)=216
f(4,6)=211
f(5,1)=257
f(5,2)=262
f(5,3)=262
f(5,4)=257
f(5,5)=253
f(6,1)=214
f(6,2)=216
f(6,3)=214
f(6,4)=211
f(7,1)=123
f(7,2)=123
f(7,3)=121
f(8,1)=47
f(8,2)=46
f(9,1)=11
Total: 6123
*/
//this looks nice, but its wrong
//the correct answer was 511, or (2^9)-1