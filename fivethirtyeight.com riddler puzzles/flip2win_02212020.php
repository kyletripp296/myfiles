<?php 
$scores = array();
$wins = 0;
for($t=0;$t<100000;$t++){
    $score = 0;
    for($f=0;$f<100;$f++){
        if($score>=0){
            $score += flip_coin(1);
        } else {
            $score += flip_coin(2);
        }
    }
    $scores[] = $score;
    if($score>0){
        $wins++;
    }
}

echo 'Average score: '.(array_sum($scores)/count($scores)).'<br>';
echo 'Games won: '.$wins.'<br>';
echo 'Win percent: '.($wins/count($scores)).'<br>';

function flip_coin($n){
    return (mt_rand(0,1)) ? $n : -$n;
}

/*
Rules:
Must flip 100 coins
Coins have values of 1 and 2, and are fair
Heads adds to score, tails subtracts from score

Goal:
After 100 flips, we want a score greater than 0

Strategy:
If score is negative, we'll flip the higher value coin to attempt to get out of the hole
If score is 0 or positive, we'll flip the lower value coin to minimize losses

Findings:
With the strategy above, we are winning about 64% of the games we play
*/
?>
