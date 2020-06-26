<?php
//generate arrays
$a = array('');
$b = array('');
while(strlen($a[0])<10){
    $newa = array();
    $newb = array();
    foreach($a as $thisa){
        $newa[] = $thisa.'0';
        $newa[] = $thisa.'1';
    }
    foreach($b as $thisb){
        $newb[] = $thisb.'0';
        $newb[] = $thisb.'1';
        $newb[] = $thisb.'2';
    }
    $a = $newa;
    $b = $newb;
}
//test arrays
$acount = 0;
foreach($a as $path){
    if(testPath($path)){
        $acount++;
    }
}
$bcount = 0;
foreach($b as $path){
    if(testPath($path)){
        $bcount++;
    }
}
//print results
echo 'Left/Right: '.$acount.'/'.count($a).' = '.number_format(100*($acount/count($a)),2).'%<br>';
echo 'Left/Right/Straight: '.$bcount.'/'.count($b).' = '.number_format(100*($bcount/count($b)),2).'%<br>';
/*
Left/Right: 512/1024 = 50.00%
Left/Right/Straight: 14763/59049 = 25.00%
*/

//start at direction 0, given a string of 0's, 1's and 2's if 0 turn left, if 1 turn right, if 2 continue straight, return true if direction is still 0 at end of string
function testPath($s){
    $dir = 0;
    foreach(str_split($s) as $n){
        if($n==0){
            $dir = ($dir+3)%4;
        } elseif($n==1) {
            $dir = ($dir+5)%4;
        }
    }
    if($dir==0){
        return true;
    }
    return false;
}

?>
