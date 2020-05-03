<?php
for($a=2;$a<=20;$a++){
    $o = array();
    for($i=0;$i<pow(2,$a);$i++){
        $n = base_convert($i,10,2);
        if(!$n){
            $o[] = 0;
        } else {
            $o[] = 1/pow(2,substr_count($n,'1'));
        }
    }
    echo $a.': '.number_format(array_sum($o)/count($o) * 100, 2).'%<br>';
}

/*
# prisoners: % chance of survival 
2: 31.25%
3: 29.69%
4: 25.39%
5: 20.61%
6: 16.24%
7: 12.57%
8: 9.62%
9: 7.31%
10: 5.53%
11: 4.17%
12: 3.14%
13: 2.36%
14: 1.78%
15: 1.33%
16: 1.00%
17: 0.75%
18: 0.56%
19: 0.42%
20: 0.32%
*/
?>
