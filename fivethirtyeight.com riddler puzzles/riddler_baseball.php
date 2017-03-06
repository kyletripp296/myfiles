<?php
# https://fivethirtyeight.com/features/can-you-solve-the-puzzle-of-the-baseball-division-champs/
    
define('NUM_TEAMS',5);
//array of highest wins for each season
$season_wins = array();
//run sim X amount of times
for($i=0;$i<10000;$i++){
	$teams = array();
	//simulate 86 games for 5 teams with 50/50 probability for each
	for($j=0;$j<NUM_TEAMS;$j++){
		$wins = 0;
		//non-division games
		for($k=0;$k<86;$k++){
			if(mt_rand(0,1)){$wins++;}
		}
		$teams[] = $wins;
	}
	//each team plays each other team in the division 19 times
	for($n=0;$n<19;$n++){
		for($j=0;$j<NUM_TEAMS-1;$j++){
			for($k=$j+1;$k<NUM_TEAMS;$k++){
				if(mt_rand(0,1)){
					$teams[$j]++;
				} else {
					$teams[$k]++;
				}
			}
		}
	}
	//reverse sort so highest win total for the year is in [0]
	rsort($teams);
	$season_wins[] = $teams[0];
}
echo array_sum($season_wins)/count($season_wins);
exit;
/* 
//if we wanted to, we could show more statistics about the data
echo 'Mean: '.mmmr($season_wins,'mean').'<br>';
echo 'Median: '.mmmr($season_wins,'median').'<br>';
echo 'Mode: '.mmmr($season_wins,'mode').'<br>';
echo 'Range: '.mmmr($season_wins,'range').'<br>';

function mmmr($array, $output = 'mean'){ 
	if(!is_array($array)){ 
		return FALSE; 
	}else{ 
		switch($output){ 
			case 'mean': 
				$count = count($array); 
				$sum = array_sum($array); 
				$total = $sum / $count; 
			break; 
			case 'median': 
				rsort($array); 
				$middle = round(count($array) / 2); 
				$total = $array[$middle-1]; 
			break; 
			case 'mode': 
				$v = array_count_values($array); 
				arsort($v); 
				foreach($v as $k => $v){$total = $k; break;} 
			break; 
			case 'range': 
				sort($array); 
				$sml = $array[0]; 
				rsort($array); 
				$lrg = $array[0]; 
				$total = $lrg - $sml; 
			break; 
		} 
		return $total; 
	} 
}  
*/