<?php
global $perms;
$perms = array();

function permute($str,$i,$n) {
    global $perms;
    if ($i == $n){
        $perms[] = $str;
    } else {
        for ($j = $i; $j < $n; $j++) {
            swap($str,$i,$j);
            permute($str, $i+1, $n);
            swap($str,$i,$j);
        }
    }
}

function swap(&$str,$i,$j) {
    $temp = $str[$i];
    $str[$i] = $str[$j];
    $str[$j] = $temp;
}   

for($n=2;$n<=10;$n++){
    $str = implode('',range(0,$n-1));
    permute($str,0,strlen($str));
    $count = 0;
    foreach($perms as $p){
        $arr = str_split($p);
        foreach($arr as $key=>$value){
            if($key==$value){
                $count++;
                break;
            }
        }
    }

    echo $n . ': ' . $count . '/' . count($perms).' = '.number_format(100*($count/count($perms)),5).'%<br>';
    $perms = array();
}

/*
2: 1/2 = 50.00000%
3: 4/6 = 66.66667%
4: 15/24 = 62.50000%
5: 76/120 = 63.33333%
6: 455/720 = 63.19444%
7: 3186/5040 = 63.21429%
8: 25487/40320 = 63.21181%
9: 229384/362880 = 63.21208%
10: 2293839/3628800 = 63.21205%
*/
?>
