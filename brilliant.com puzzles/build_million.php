<?php 
ini_set('memory_limit','2G');
set_time_limit(0);
$arr = array('1');
while(true){
    $newarr = array();
    foreach($arr as $thisarr){
        if(calc_string($thisarr) == 1000000){
            echo strlen($thisarr).' '.$thisarr;exit;
        }
        $newarr[] = $thisarr.'1';
        $newarr[] = $thisarr.'7';
    }
    $arr = $newarr;
}


function calc_string($str){
    $val = 0;
    $arr = str_split($str);
    for($i=0;$i<count($arr);$i++){
        if($arr[$i]=='1'){
            $val++;
        } else {
            $val *= 7;
        }
    }
    return $val;
}