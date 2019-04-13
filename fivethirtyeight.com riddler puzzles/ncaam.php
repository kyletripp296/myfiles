<?php 

include_once '../config.php';

$tournament_winner = 'Virginia';
$table_name = 'ncaam_2019';
$txt_file = 'ncaam.txt';
$reset_table = false;

//timer
$start = microtime(true);

if($reset_table){
	//reset table
	$sql = "drop table $table_name";
	$mysqli->query($sql);
	$sql = "create table $table_name (winner varchar(256) NOT NULL, loser varchar(256) NOT NULL)";
	$mysqli->query($sql);

	//open file for reading
	$myfile = fopen($txt_file, "r") or die("Unable to open file!");
	
	//read lines of file in chunks, inserting into db
	$values_arr = array();
	$i = 0;
	while($line = fgets($myfile)){
		list($winner,$loser) = explode(' | ',$line);
		$winner_sql = $mysqli->real_escape_string(trim($winner));
		$loser_sql = $mysqli->real_escape_string(trim($loser));
		$values_arr[] = "('$winner_sql','$loser_sql')";
	}
	$values = implode(',',$values_arr);
	$sql = "insert into $table_name values $values";
	$mysqli->query($sql);
}


//query database for all teams, keep an array in the style of ['team_name'] => true
$allteams = array();
$sql = "select distinct loser from $table_name order by loser asc";
$result = $mysqli->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$allteams[$row['loser']] = true;
	}
}

//query database for a count of how many games were played
$sql = "select count(1) as count from $table_name";
$result = $mysqli->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$num_games = $row['count'];
	}
}

//print some stuff to the screen before this next part... to keep the user interested
echo 'Processing '.count($allteams).' teams...<br>';
echo 'Processing '.$num_games.' games...<br>';

//unset the winner from allteams, to exclude them from this search
$allteams[$tournament_winner] = false;

//create an array of arrays, known as tiers. the first array contains only the winner of the tournament.
$tiers = array(array($tournament_winner));

//build our tier view 
for($i=0;$i<=count($tiers)-1;$i++){
	if(count($tiers[$i])){
		$tierteams = "'".implode("','",$tiers[$i])."'";
		$sql = "select distinct winner from $table_name where loser in ($tierteams)";
		$result = $mysqli->query($sql);
		if($result->num_rows){
			while($row = $result->fetch_assoc()){
				if($allteams[$row['winner']]){
					if(!isset($tiers[$i+1])){
						$tiers[$i+1] = array();
					}
					$tiers[$i+1][] = $row['winner'];
					$allteams[$row['winner']] = false;
				}
			}
		}
	}
}

//once that whole thing is done, count how many teams havent been added yet, that is our answer
$c = 0;
$rteams = array();
foreach($allteams as $key=>$value){
	if($value){
		$c++;
		$rteams[] = $key;
	}
}

//print to the screen again and we're done
echo $c.' teams are NOT transitive champions<br>';
echo 'Tiers: <pre>'.print_r($tiers,true).'</pre>';
echo 'Remaining teams: <pre>'.print_r($rteams,true).'</pre>';
echo 'Time: '.(microtime(true)-$start).' seconds<br>';
