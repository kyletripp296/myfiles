<?php
set_time_limit(0);
for($i=1;$i<1000000;$i++){
	$p = $i.strrev($i);
	if($p%11!=0){
		exit($p);
	}
}
echo 'No results found';exit;