<?php 

set_time_limit(-1);
include_once '../config.php';

/*
//create table - done
$sql = "drop table ncaam_2019";
$mysqli->query($sql);
$sql = "create table ncaam_2019 (winner varchar(256) NOT NULL, loser varchar(256) NOT NULL)";
$mysqli->query($sql);

//insert rows - done
$myfile = fopen("ncaam.txt", "r") or die("Unable to open file!");
while($line = fgets($myfile)){
	list($winner,$loser) = explode(' | ',$line);
	$winner_sql = $mysqli->real_escape_string(trim($winner));
	$loser_sql = $mysqli->real_escape_string(trim($loser));
	$sql = "insert into ncaam_2019 values ('$winner_sql','$loser_sql')";
	$mysqli->query($sql);
}
*/

$allteams = array();
$sql = "select distinct loser from ncaam_2019 order by loser asc";
$result = $mysqli->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$allteams[$row['loser']] = true;
	}
}
echo 'Processing '.count($allteams).' teams...<br>';

$sql = "select count(1) as count from ncaam_2019";
$result = $mysqli->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		echo 'Processing '.$row['count'].' games...<br>';
	}
}

$allteams['Virginia'] = false;
$tiers = array(array('Virginia'));

for($i=0;$i<=count($tiers);$i++){
	if(count($tiers[$i])){
		foreach($tiers[$i] as $team){
			$team_sql = $mysqli->real_escape_string($team);
			$sql = "select distinct winner from ncaam_2019 where loser='$team_sql'";
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
}

$c = 0;
foreach($allteams as $key=>$value){
	if($value){
		$c++;
	}
}

echo $c.' teams are NOT transitive champions<br>';
echo 'Tiers: <pre>'.print_r($tiers,true).'</pre>';
