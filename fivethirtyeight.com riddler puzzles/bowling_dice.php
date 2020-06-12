<?php
$a = 0;
$b = 0;
foreach(range(1,6) as $r){
    if($r==1){
        $bowl = array(1,3,4,6);
    } elseif($r==5){
        $bowl = array(1,2,5,6);
    } else {
        $bowl = array(2,3,4,5);
    }
    foreach($bowl as $c){
        $v = $r+$c;
        $b++;
        if(in_array($v,array(7,11))){
            $a++;
        }
    }
}
echo 'answer: ' . $a . '/' . $b . ' ' . number_format(($a/$b)*100,2).'%<br>';


//Extra credit
$trials = 10000;
$score = 0;
for($i=0;$i<$trials;$i++){
    $r = mt_rand(1,6);
    if($r==1){
        $bowl = array(1,3,4,6);
    } elseif($r==5){
        $bowl = array(1,2,5,6);
    } else {
        $bowl = array(2,3,4,5);
    }
    $r += $bowl[rand(0,3)];
    if(in_array($r,array(2,3,12))){
        $score--;
    } elseif(in_array($r,array(7,11))){
        $score++;
    }
}
echo 'extra credit: ~'.$score/$trials.' (standard die is ~0.111)';
?>
