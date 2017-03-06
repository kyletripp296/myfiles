<?php 

/*

You and four statistician colleagues find a $100 bill on the floor of your department’s faculty lounge. 
None of you have change, so you agree to play a game of chance to divide the money probabilistically. 
The five of you sit around a table. The game is played in turns. Each turn, one of three things can happen, each with an equal probability: 
1. The bill can move one position to the left
2. The bill can move one position to the right
3. the game ends and the person with the bill in front of him or her wins the game

You have tenure and seniority, so the bill starts in front of you. What are the chances you win the money?

Extra credit: What if there were N statisticians in the department?

*/
//how many tests do we want to run?
$num_tests = 100000;
//how many tests did we win the money?
$success_arr = array();

//increase the number of people trying each time
for($i=2;$i<=100;$i++){
	$success = 0;
	//run one test for each time through this loop
	for($j=0;$j<$num_tests;$j++){
		//start with the dollar bill in front of us
		$billpos = 0;
		do{
			//draw a random number, 1-3
			$rand = mt_rand(1,3);
			//if number was 1, shift bill one way
			if($rand==1){
				$billpos++;
				$billpos%=$i;
			//if number was 2, shift bill the other way
			} elseif($rand==2){
				$billpos--;
				$billpos+=$i;
				$billpos%=$i;
			}
		//keep drawing numbers until we get a 3, at that point the game stops
		} while($rand!=3);
		//if the bill is still in front of us, we win
		if($billpos==0){
			$success++;
		}
	}
	$success_arr[$i] = $success;
}
//print results
//echo 'Test ran '.$num_tests.' times, you won '.$success.' times ('.number_format(100*$success/$num_tests,2).'%)<br>';
echo '<pre>'.print_r($success_arr,true).'</pre>';