<?php

$numtests = 1000000;
$numwins = 0;
for($i=0;$i<$numtests;$i++){
	$heads=0;
	$tails=0;
	for($j=0;$j<10;$j++){
		if(mt_rand(0,1)){
			$heads++;
		} else {
			$tails++;
		}
	}
	if($heads==$tails){$numwins++;}
}

echo 'Number of tests: '.$numtests.'<br>'; //prints '10000'
echo 'Number of wins: '.$numwins.'<br>'; //prints somewhere around 2400-2500
echo 'Win %: '.($numwins/$numtests)*100; //prints somewhere around 24-25%