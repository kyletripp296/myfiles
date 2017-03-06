<?php
/*
we want iterations[0] = array('0'=>array(0),'1'=>array(1),'2'=>array(2,3)) etc.
then our answer will be the smallest value in iterations[15]
*/

//set up initial state variables
$n=0;
$used = array(0);
$iterations = array('0'=>array(0));
//iterate 15 times
for($i=1;$i<=15;$i++){
	//look at all the values from the previous iteration
	foreach($iterations[$i-1] as $thisn){
		//add 1 to this number, have we seen that number yet?
		if(!in_array($thisn+1,$used)){
			//if not then now we mark it as seen
			$used[]=$thisn+1;
			//and we saw it on this iteration
			$iterations[$i][] = $thisn+1;
		}
		//do the same thing for 3 times this number
		if(!in_array($thisn*3,$used)){
			$used[]=$thisn*3;
			$iterations[$i][] = $thisn*3;
		}
	}
}
//done iterating, grab $iterations[15]
$temp = $iterations[15];
//sort it, putting the lowest values first
sort($temp);
//echo the first number
echo $temp[0];
//prints 404