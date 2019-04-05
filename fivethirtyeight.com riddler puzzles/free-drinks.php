<?php
//run this simulation $t times
$t = 100000;
//number of free drinks
$d = 50;
//number of times both cards reached 0
$z = 0;
//all remaining cards, when the other card got declined
$arr = array();

for($i=0;$i<$t;$i++){
    //card #1 and 2 both get an initial balance
    $a = $d;
    $b = $d;
    $continue = true;
    do {
        //pick a card to use
        if(mt_rand(0,1)){
            //if it has a balance, use it
            if($a){
                $a--;
            //if it gets declined, keep the other card
            } else {
                if(!$b){
                    $z++;
                }
                $arr[] = $b;
                $continue = false;
            }
        //card #2
        } else {
            //if it has a balance, use it
            if($b){
                $b--;
            //if it gets declined, keep the other card
            } else {
                if(!$a){
                    $z++;
                }
                $arr[] = $a;
                $continue = false;
            }
        }
    } while($continue);
}

echo 'chance theres still a drink left on the second card: '.($t-$z).'/'.$t.' = '.(($t-$z)/$t).'<br>'; //~92%
echo 'expected drinks left on second card: '.array_sum($arr)/count($arr); //~7

?>
