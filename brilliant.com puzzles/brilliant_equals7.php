<?php 
//given the equation 1_2_3_4_5_6=7
//we can replace the _ with either + or -
//how many sets of operators make this true?

//create an array of all possible sets of operators
$operators = array('+','-');
while(strlen($operators[0])<5){
	$temp = array();
	foreach($operators as $thisop){
		$temp[] = $thisop.'+';
		$temp[] = $thisop.'-';
	}
	$operators = $temp;
}
//there are 32 (2^5) sets of operators to try at this point
// '+++++' is one, '++++-' is another, etc.

//try each
$hits = 0;
foreach($operators as $key=>$val){
	//create a string like '1+2+3+4+5+6'
	$op = '1'.$val[0].'2'.$val[1].'3'.$val[2].'4'.$val[3].'5'.$val[4].'6';
	//if it evaluates to 7
	if(eval("return ($op);")==7){
		//print it out and count it
		echo $op.'<br>';
		$hits++;
	}
}
//once we've tried them all, how many hit?
echo 'Total: '.$hits;

/*
prints:
1+2-3-4+5+6
1-2+3+4-5+6
Total: 2
*/