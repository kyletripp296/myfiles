<?php 

for($i=1;$i<10000;$i++){
	$runner1 = (sqrt(5)*$i)%1000;
	$runner2 = (M_E*$i)%1000;
	$runner3 = (3*$i)%1000;
	$runner4 = (M_PI*$i)%1000;
	echo 'i:'.$i.', 1:'.$runner1.', 2:'.$runner2.', 3:'.$runner3.', 4:'.$runner4.'<br>';
	if(abs($runner1-$runner2)>=250 && abs($runner2-$runner3)>=250 && abs($runner2-$runner4)>=250){
		echo $i;exit;
	}
}