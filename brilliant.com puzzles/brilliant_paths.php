<?php
/*
Visual version
OOOOOO
OXOOXO
OOOOOO
OOOOOO
OXOOXO
OOOOOO

Numerical version
[00][01][02][03][04][05]
[06][07][08][09][10][11]
[12][13][14][15][16][17]
[18][19][20][21][22][23]
[24][25][26][27][28][29]
[30][31][32][33][34][35]

you must move from the top left circle [00] to the bottom right circle [35]
you can move to the right (+1) as long as you are not in the right column (square%6==5)
or you can move down (+6) as long as you are not in the bottom row (square>=30)
you cannot cross the squares with x's [07],[10],[25],[28]
*/

$return_arr = array();
$x_squares = array(7,10,25,28);
iterate(array(array(0)));
echo count($return_arr); //this prints 34, our answer

function iterate($array){
	global $x_squares;
	global $return_arr;
	$next_iteration = array();
	for($i=0;$i<count($array);$i++){
		//so at this point $array[$i] will contain a path of moves so far such as (0,1,2,8) or something
		$moves = $array[$i];
		//the last number in that array is the position we are at
		$current = $moves[count($moves)-1];
		//from that position check right and down
		//if we can make either of those moves, add that to the end of our array and throw that into $next_iteration
		$right = $current+1;
		if($right%6!=0&&!in_array($right,$x_squares)){
			$temp = $moves;
			$temp[]=$right;
			$next_iteration[]=$temp;
		}
		$down = $current+6;
		if($down<=35&&!in_array($down,$x_squares)){
			$temp = $moves;
			$temp[]=$down;
			$next_iteration[]=$temp;
		}
	}
	if(count($next_iteration)){
		iterate($next_iteration);
	} else {
		$return_arr = $array;
	}
}