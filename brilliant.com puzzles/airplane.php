<?php 

$tests_ran = 0;
$success = 0;
for($a=0;$a<20;$a++){
	for($b=0;$b<20;$b++){
		for($c=0;$c<20;$c++){
			for($d=0;$d<20;$d++){
				for($e=0;$e<20;$e++){
					if($a&&$b&&$c&&$d&&$e){
						$success++;
					}
					$tests_ran++;
				}
			}
		}
	}
}

$prob = 1-($success/$tests_ran);
echo 'Probability: '.$prob.' ('.number_format($prob*100,2).'%)<br>';