<?php 
//start with an array of numbers between 0-4
$arr = range(0,4);

//create a new array which is all permutations of the original array
//perms follow: 01234, 01243, 01324, 01342, etc.
//with an original array size of 5, the size of $arr2 will be 5! or 120
$arr2 = pc_permute($arr);

//if we uncomment this we can print the above array
//echo '<pre>'.print_r($arr2,true).'</pre>';

//create this array that we can save reuslts into later
$results = array();

//loop through all permutations
foreach($arr2 as $key=>$val){
	//get the index for each number
	//for example, the number 14203; $index0=1, $index1=4, $index2=2 etc.
	for($i=0;$i<count($val);$i++){
		${'index'.$val[$i]} = $i;
	}
	//we only keep this permutation if none of the original people are next to each other
	if(!next_to($index0,$index1) && !next_to($index1,$index2) && !next_to($index2,$index3) && !next_to($index3,$index4)){
		$results[] = implode('',$val);
	}
}

//sort and count the results array, then print it
sort($results);
echo 'Answer: '.count($results).'<br>';
echo '<pre>'.print_r($results,true).'</pre>';

//found this online, creates an array of all permutations
function pc_permute($items, $perms = array(), &$ret = array()) {
   if (empty($items)) {
       $ret[] = $perms;
   } else {
       for ($i = count($items) - 1; $i >= 0; --$i) {
           $newitems = $items;
           $newperms = $perms;
           list($foo) = array_splice($newitems, $i, 1);
           array_unshift($newperms, $foo);
           pc_permute($newitems, $newperms,$ret);
       }
   }
   return $ret;
}

//we define two numbers, $a and $b, to be 'next to' each other if the absolute value of $a-$b equals 1
function next_to($a,$b){
	return (abs($a-$b)==1)?true:false;
}