<?php 
/*
Riddler Expected Max Dice Sum
Kyle Tripp, August 2021
https://fivethirtyeight.com/features/are-you-clever-enough/
*/

$num_dice_vals = range(2,10);
$num_trials = 10000;
foreach($num_dice_vals as $num_dice){
    $scores = [];
    for($i=0;$i<$num_trials;$i++){
        $scores[] = runTrial($num_dice);
    }
    echo 'Average score for '.$num_dice.' dice over '.$num_trials.' trials is '.(array_sum($scores)/count($scores))."\n";
}

/*
Average score for 2 dice over 10000 trials is 8.2057
Average score for 3 dice over 10000 trials is 13.2653
Average score for 4 dice over 10000 trials is 18.2745
Average score for 5 dice over 10000 trials is 23.2809
Average score for 6 dice over 10000 trials is 28.2872
Average score for 7 dice over 10000 trials is 33.2831
Average score for 8 dice over 10000 trials is 38.2674
Average score for 9 dice over 10000 trials is 43.2951
Average score for 10 dice over 10000 trials is 48.311

summary
for n dice, expected score is about 8.2 + (5*(n-2))
*/


//simulate one play of a game with n dice
function runTrial($num_dice){
    // setup stuff
    $score = 0;
    $to_roll = $num_dice;
    $freeze = false;
    // lets play
    do {
        // roll a certain amount of dice
        $dice = rollDice($to_roll);
        //echo 'rolled: ['.implode(',',$dice).']'."\n";
        // look through our rolled dice and freeze any 4's, 5's or 6's
        foreach($dice as $key=>$val){
            if($val>=4){
                //echo 'freezing '.$val."\n";
                $score += $val;
                unset($dice[$key]);
                $freeze = true;
            }
        }
        //if we werent able to freeze any 4's, 5's or 6's
        if(!$freeze){
            // we sort the dice in descending order
            rsort($dice);
            // and take the first one, the highest one
            //echo 'freezing '.$dice[0]."\n";
            $score += $dice[0];
            unset($dice[0]);
        }
        //update for next loop
        $to_roll = count($dice);
        $freeze = false;
    //continue until we've frozen all dice
    } while($to_roll);
    //echo 'score: '.$score."\n";
    //return an integer score, the sum of our frozen dice
    return $score;
}

// return an array of n dice, values 1-6
function rollDice($n){
    $arr = [];
    for($i=0;$i<$n;$i++){
        $arr[] = mt_rand(1,6);
    }
    return $arr;
}
