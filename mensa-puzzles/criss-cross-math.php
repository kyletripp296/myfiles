<?php

/*
Place the digits 1-9 into the empty cells so that the three rows across and three columns down are correct.
All calculations involve only poritive whole numbers, and are read left to right or top to bottom (no pemdas)


[a] + [b] - [c] = 5
 -     /     +
[d] + [e] / [f] = 2
 +     *     /
[g] * [h] + [i] = 6
 =     =     =
 2     8     7


*/
 

$arr = range(1,9);
do {
    $a = $arr[0];
    $b = $arr[1];
    $c = $arr[2];
    $d = $arr[3];
    $e = $arr[4];
    $f = $arr[5];
    $g = $arr[6];
    $h = $arr[7];
    $i = $arr[8];
    if($a+$b-$c==5 && ($d+$e)/$f==2 && $g*$h+$i==6 && $a-$d+$g==2 && ($b/$e)*$h=8 && ($c+$f)/$i==7){
        echo '<pre>'.print_r($arr,true).'</pre>';;exit;
    }
} while($arr = pc_next_permutation($arr,count($arr)-1));

function pc_next_permutation($p, $size) {
    for ($i = $size - 1; isset($p[$i]) && $p[$i] >= $p[$i+1]; --$i) { }
    if ($i == -1) { return false; }
    for ($j = $size; $p[$j] <= $p[$i]; --$j) { }
    $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
    for (++$i, $j = $size; $i < $j; ++$i, --$j) {
         $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
    }
    return $p;
}

//Answer: 8,6,9,7,3,5,1,4,2
?>
