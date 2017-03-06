<?php
# https://fivethirtyeight.com/features/how-long-will-you-be-stuck-playing-this-bar-game/
set_time_limit(0);
//keep an array of how many tries it takes for each test
$results = array();
//run a certain number of tests
for($i=0;$i<1000;$i++){
	//pick a negative value for player1, positive value for player2
	$p1win = -1*mt_rand(1,100);
	$p2win = mt_rand(1,100);
	$p1win = -25;
	$p2win = 25;
	//reset the coin position
	$coin=0;
	//reset number of flips
	$flips = 0;
	//flip until someone wins
	while($coin!=$p1win && $coin!=$p2win){
		//flip coin
		$heads = mt_rand(0,1);
		//move coin
		if($heads){$coin++;}else{$coin--;}
		//count this flip
		$flips++;
	}
	//create temp array with this tests info
	$temp = array('flips'=>$flips,'p1win'=>$p1win,'p2win'=>$p2win,'coin'=>$coin,'expected'=>abs($p1win*$p2win));
	//save temp array into results
	$results[] = $temp;
}
//print results
echo '<pre>'.print_r($results,true).'</pre>';

foreach($results as $thisresult){
	$totalflips += $thisresult['flips'];
	$totalexp += $thisresult['expected'];
}
echo 'average flips: '.($totalflips/count($results)).'<br>';
echo 'average expected: '.($totalexp/count($results)).'<br>';

/*
X		Y		exp
-1		1		1
-2		1		2
-2		2		4
-3		1		3
-3		2		6
-3		3		9
-4		1		4
-4		2		8
-4		3		12
-4		4		16
-5		1		5
-5		2		10
-5		3		15
-5		4		20
-5		5		25
X		Y		abs(X*Y)
*/