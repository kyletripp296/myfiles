<?php 

//start with just '('
$test_arr = array('(');

//keep adding '(' and ')' to the end of the string
//until we have all possible strings of length 20
$length = 20;
while(strlen($test_arr[0])<$length){
	$temp_arr = array();
	foreach($test_arr as $this_string){
		if(substr_count($this_string,')')<$length/2){
			$temp_arr[] = $this_string.')';
		}
		if(substr_count($this_string,'(')<$length/2){
			$temp_arr[] = $this_string.'(';
		}
	}
	$test_arr = $temp_arr;
}

//now test each of those strings to see if it is balanced
//if it passes the balanced test, add it to our final array
$balanced_arr = array();
foreach($test_arr as $this_string){
	if(is_balanced($this_string)){
		$balanced_arr[] = $this_string;
	}
}

//done with everything, print results
echo count($balanced_arr).'<br>';
echo '<pre>'.print_r($balanced_arr,true).'</pre>';

//check if parenthesis are balanced
//constantly replace '()' with ''
//if the length of the string is 0 when we are done doing this then we return true that it is balanced
function is_balanced($string){
	$regex = '@\(\)@';
	while(preg_match($regex,$string)){
		$string = preg_replace($regex,'',$string);
	}
	return (strlen($string)==0) ? true : false;
}