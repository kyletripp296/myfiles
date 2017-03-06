<?php
define('MAXVAL',1000000);
define('NUM_TRIALS',1000);

//keep track of wins
$country_a_wins = 0;
$country_b_wins = 0;
//do N trials
for($n=0;$n<NUM_TRIALS;$n++){
	//country a gets 400 picks
	$country_a = array();
	for($i=0;$i<400;$i++){
		$country_a[] = mt_rand(1,MAXVAL);
	}

	//country b gets 100 picks
	$country_b = array();
	for($i=0;$i<100;$i++){
		$country_b[] = mt_rand(1,MAXVAL);
	}
	
	//only keep highest vals
	rsort($country_a);
	rsort($country_b);
	$country_a_val = array_slice($country_a,0,1);
	$country_b_val = array_slice($country_b,0,1);
	
	//compare, who won?
	if($country_a_val > $country_b_val){
		$country_a_wins++;
	} elseif($country_a_val < $country_b_val){
		$country_b_wins++;
	}
}
echo 'Country A won '.$country_a_wins.' time(s), or '.(($country_a_wins/NUM_TRIALS)*100).'%<br>';
echo 'Country B won '.$country_b_wins.' time(s), or '.(($country_b_wins/NUM_TRIALS)*100).'%<br>';