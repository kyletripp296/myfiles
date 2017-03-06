<?php 
set_time_limit(0);
$tests_ran = 0;
$success = 0;
for($x=0;$x<=1;$x+=.0001){
	for($y=0;$y<=1;$y+=.0001){
		if($y>0 && round($x/$y)%2==1){
			$success++;
		}
		$tests_ran++;
	}
}

$prob = $success/$tests_ran;
$answer = ceil(10000*$prob);
echo 'Tests ran: '.$tests_ran.', Success: '.$success.'<br>';
echo 'Probability: '.$prob.'<br>';
echo 'Answer: '.$answer; //got correct answer 5354