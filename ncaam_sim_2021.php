<?php 
/*
Kyle Tripp
3/14/21
NCAAM Sim 
*/

//whoWins();
//Gonzaga wins about 30% of the time, other 1 and 2 seeds always do well, and other high ranked teams sometimes make miracle runs

playTournament();
//see output


/* dont worry about this stuff */

/*
* Simulate a bunch of tournaments and keep track of who wins them
*/
function whoWins(){
    $winners = array();
    $n = 100;
    for($i=0;$i<=$n;$i++){
        $winner = playTournament();
        if(!isset($winners[$winner])){
            $winners[$winner] = 1;
        } else {
            $winners[$winner] += 1;
        }
    }
    arsort($winners);
    foreach($winners as $key=>$value){
        echo $key.' '.$value."\n";
    }
}

/*
* This method will simulate the 4 playin games followed by all tournament games and ultimately return a winning team
*/
function playTournament(){
    $teams = getTeams();
    $playin = getPlayinTeams();
    $teams[25] = compete2($playin[0],$playin[1]);
    $teams[17] = compete2($playin[2],$playin[3]);
    $teams[9] = compete2($playin[4],$playin[5]);
    $teams[1] = compete2($playin[6],$playin[7]);
    while(count($teams)>1){
        $newteams = array();
        for($i=0;$i<count($teams);$i+=2){
            $newteams[] = compete2($teams[$i],$teams[$i+1]);
        }
        $teams = $newteams;
    }
    return $teams[0]['name'];
}

/*
* This method gives each team a percent chance to win based on relative elo ratings and generates a random number to determine winner 
*/
function compete($a,$b){
    $p = intval(100 * getWinProb($a['elo']-$b['elo']));
    $expected = ($a['elo']>=$b['elo']) ? $a['name'] : $b['name'];
    $percent = ($a['elo']>=$b['elo']) ? $p.'%' : (100-$p).'%';
    $n = mt_rand(1,100);
    $winner = ($n<=$p) ? $a['name'] : $b['name'];
    
    //echo sprintf("Now Playing: %s (%s) vs. %s (%s) \n", $a['name'], $a['elo'], $b['name'], $b['elo']);
    //echo sprintf("Expected winner: %s (%s)\n", $expected, $percent);
    //echo sprintf("Actual winner: %s\n\n", $winner);
    return ($n<=$p) ? $a : $b;
}

/*
* This method takes a teams offensive and defensive ratings and simulates a number of posessions where each posession is an opportunity to earn 0-3 points with increasing difficulty
* At the end, the team with the most points is deemed the winner
*/
function compete2($a,$b){
    $p = intval(100 * getWinProb($a['elo']-$b['elo']));
    $expected = ($a['elo']>=$b['elo']) ? $a['name'] : $b['name'];
    $percent = ($a['elo']>=$b['elo']) ? $p.'%' : (100-$p).'%';
    $tempo = round(($a['tempo']+$b['tempo'])/2);
    $a_score = 0;
    $b_score = 0;
    echo sprintf("Now Playing: %s (%s) vs. %s (%s) \n", $a['name'], $a['elo'], $b['name'], $b['elo']);
    echo sprintf("Expected winner: %s (%s)\n", $expected, $percent);
    
    //echo sprintf("------------\n");
    //echo sprintf("| Game Log |\n");
    //echo sprintf("------------\n");
    //echo sprintf("Playing %s possessions\n\n",$tempo);
    for($i=0;$i<$tempo;$i++){
        $a_points = playPosession($a['off'],$b['def']);
        $a_score += $a_points;
        $b_points = playPosession($b['off'],$a['def']);
        $b_score += $b_points;
        //echo sprintf("Possession #%s\n",$i);
        //echo sprintf("%s scored %s points\n",$a['name'],$a_points);
        //echo sprintf("%s scored %s points\n",$b['name'],$b_points);
        //echo sprintf("Score is %s %s-%s %s\n\n", $a['name'], $a_score, $b_score, $b['name']);
    }
    while($a_score==$b_score){
        $ot = round($tempo/8);
        //echo sprintf("Overtime\n");
        //echo sprintf("Playing %s possessions\n", $ot);
        for($i=0;$i<$ot;$i++){
            $a_points = playPosession($a['off'],$b['def']);
            $a_score += $a_points;
            $b_points = playPosession($b['off'],$a['def']);
            $b_score += $b_points;
            //echo sprintf("Possession #%s\n",$i);
            //echo sprintf("%s scored %s points\n",$a['name'],$a_points);
            //echo sprintf("%s scored %s points\n",$b['name'],$b_points);
            //echo sprintf("Score is %s %s-%s %s\n\n", $a['name'], $a_score, $b_score, $b['name']);
        }
    }
    $winner = ($a_score>$b_score) ? $a['name'] : $b['name'];
    $a_ppp = number_format($a_score/$tempo,3);
    $b_ppp = number_format($b_score/$tempo,3);
    echo sprintf("Final score is %s-%s, %s wins\n\n", max($a_score,$b_score), min($a_score,$b_score), $winner);
    //echo sprintf("Points per Possession: %s: %s %s: %s\n\n", $a['name'], $a_ppp, $b['name'], $b_ppp);
    return ($a_score>$b_score) ? $a : $b;
}

/*
* This function takes one teams offense rating and the other teams defensive rating and rolls random numbers, points are awarded based on how much higher the offense is
*/
function playPosession($off,$def){
    $off_roll = mt_rand(1,10*$off);
    $def_roll = mt_rand(1,10*(200-$def));
    $n = $off_roll - $def_roll;
    if($n>800){
        return 3;
    } elseif($n>200){
        return 2;
    }
    return ($n>0) ? 1 : 0;
}

/*
* A formula to get a probability between 0 and 1 of a person winning a match based on their rating and their opponents rating
*/
function getWinProb($n){
    return 1 / (1 + pow(10,((-1*$n)/400)));
}

/*
* An array containing the first 4 games worth of teams, after these teams play they will be assigned spots in the main bracket
*/
function getPlayinTeams(){
    return array(
        //winner to [25]
        array('name'=>'UCLA','elo'=>1542,'off'=>114.1,'def'=>96.8,'tempo'=>64.7),
        array('name'=>'Michigan State','elo'=>1596,'off'=>107.7,'def'=>92.2,'tempo'=>68.6),
        //winner to [17]
        array('name'=>'Texas Southern','elo'=>1395,'off'=>99.7,'def'=>104.3,'tempo'=>72.0),
        array('name'=>'Mt. St Marys','elo'=>1278,'off'=>96.1,'def'=>99.7,'tempo'=>62.2),
        //winner to [9]
        array('name'=>'Drake','elo'=>1540,'off'=>114.6,'def'=>98.6,'tempo'=>66.8),
        array('name'=>'Wichita State','elo'=>1592,'off'=>110.7,'def'=>97.7,'tempo'=>67.6),
        //winner to [1]
        array('name'=>'Appalachian State','elo'=>1378,'off'=>100.1,'def'=>103.0,'tempo'=>65.7),
        array('name'=>'Norfolk St','elo'=>1310,'off'=>101.3,'def'=>103.6,'tempo'=>67.7),
    );
}

/*
* An array containing teams and information or stats associated with them
*/
// [1,9,17,25] are empty
function getTeams(){
    return array(
        //Gonzaga ~96%
        array('name'=>'Gonzaga','elo'=>1870,'off'=>126.8,'def'=>88.8,'tempo'=>74.8),
        array(),
        //Missouri 53%
        array('name'=>'Oklahoma','elo'=>1534,'off'=>112.1,'def'=>94.1,'tempo'=>67.7),
        array('name'=>'Missouri','elo'=>1548,'off'=>110.9,'def'=>94.9,'tempo'=>68.7),
        //Creighton 70%
        array('name'=>'Creighton','elo'=>1650,'off'=>115.6,'def'=>92.8,'tempo'=>69.1),
        array('name'=>'UCSB','elo'=>1496,'off'=>109.9,'def'=>96.3,'tempo'=>66.1),
        //Virginia 73%
        array('name'=>'Virginia','elo'=>1682,'off'=>116.3,'def'=>92.3,'tempo'=>60.1),
        array('name'=>'Ohio','elo'=>1507,'off'=>113.7,'def'=>101.2,'tempo'=>69.3),
        // USC 55%
        array('name'=>'USC','elo'=>1628,'off'=>113.6,'def'=>89.9,'tempo'=>67,3),
        array(),
        //Kansas 79%
        array('name'=>'Kansas','elo'=>1701,'off'=>110.3,'def'=>87.9,'tempo'=>68.3),
        array('name'=>'E Washington','elo'=>1469,'off'=>108.4,'def'=>100.3,'tempo'=>72.4),
        //Oregon 65%
        array('name'=>'Oregon','elo'=>1669,'off'=>115.1,'def'=>96.0,'tempo'=>67.2),
        array('name'=>'VCU','elo'=>1558,'off'=>106.2,'def'=>88.8,'tempo'=>69.8),
        //Iowa 86%
        array('name'=>'Iowa','elo'=>1710,'off'=>124.2,'def'=>93.9,'tempo'=>70.0),
        array('name'=>'Grand Canyon','elo'=>1383,'off'=>104.2,'def'=>95.8,'tempo'=>65.5),
        //Michigan 87%
        array('name'=>'Michigan','elo'=>1727,'off'=>120.1,'def'=>87.9,'tempo'=>66.8),
        array(),
        //LSU 55%
        array('name'=>'LSU','elo'=>1633,'off'=>120.5,'def'=>99.4,'tempo'=>70.8),
        array('name'=>'St Bonaventure','elo'=>1597,'off'=>112.0,'def'=>89.8,'tempo'=>65.2),
        //Colorado 56%
        array('name'=>'Colorado','elo'=>1639,'off'=>115.1,'def'=>92.0,'tempo'=>66.4),
        array('name'=>'Georgetown','elo'=>1593,'off'=>108.4,'def'=>92.9,'tempo'=>69.6),
        //Florida State 65%
        array('name'=>'Florida State','elo'=>1632,'off'=>117.1,'def'=>93.6,'tempo'=>70.6),
        array('name'=>'UNCG','elo'=>1519,'off'=>104.8,'def'=>95.2,'tempo'=>68.5),
        //BYU 62%
        array('name'=>'BYU','elo'=>1632,'off'=>113.8,'def'=>91.4,'tempo'=>68.0),
        array(),
        //Texas 78%
        array('name'=>'Texas','elo'=>1705,'off'=>114.5,'def'=>92.5,'tempo'=>69.2),
        array('name'=>'Abilene Christian','elo'=>1481,'off'=>103.7,'def'=>92.2,'tempo'=>70.0),
        //UCONN 59%
        array('name'=>'UCONN','elo'=>1610,'off'=>114.3,'def'=>90.9,'tempo'=>66.0),
        array('name'=>'Maryland','elo'=>1546,'off'=>111.6,'def'=>91.5,'tempo'=>65.3),
        //Alabama 90%
        array('name'=>'Alabama','elo'=>1753,'off'=>112.4,'def'=>86.0,'tempo'=>73.9),
        array('name'=>'Iona','elo'=>1371,'off'=>101.1,'def'=>100.7,'tempo'=>68.3),
        //Baylor 92%
        array('name'=>'Baylor','elo'=>1806,'off'=>124.0,'def'=>93.0,'tempo'=>68.4),
        array('name'=>'Hartford','elo'=>1359,'off'=>98.2,'def'=>99.5,'tempo'=>66.7),
        //UNC 60%
        array('name'=>'North Carolina','elo'=>1624,'off'=>110.8,'def'=>89.3,'tempo'=>71.8),
        array('name'=>'Wisconsin','elo'=>1553,'off'=>113.2,'def'=>89.1,'tempo'=>64.9),
        //Villanova 60%
        array('name'=>'Villanova','elo'=>1634,'off'=>119.3,'def'=>95.3,'tempo'=>65.1),
        array('name'=>'Winthrop','elo'=>1558,'off'=>105.8,'def'=>95.4,'tempo'=>73.6),
        //Purdue 68%
        array('name'=>'Purdue','elo'=>1622,'off'=>114.3,'def'=>90.6,'tempo'=>66.5),
        array('name'=>'North Texas','elo'=>1484,'off'=>106.1,'def'=>92.9,'tempo'=>63.1),
        //Texas Tech 52%
        array('name'=>'Texas Tech','elo'=>1595,'off'=>113.1,'def'=>90.7,'tempo'=>65.4),
        array('name'=>'Utah State','elo'=>1576,'off'=>106.4,'def'=>88.5,'tempo'=>68.9),
        //Arkansas 66%
        array('name'=>'Arkansas','elo'=>1679,'off'=>112.2,'def'=>89.2,'tempo'=>73.1),
        array('name'=>'Colgate','elo'=>1559,'off'=>111.6,'def'=>99.9,'tempo'=>72.5),
        //Florida 52%
        array('name'=>'Florida','elo'=>1550,'off'=>111.7,'def'=>92.7,'tempo'=>68.7),
        array('name'=>'Virginia Tech','elo'=>1530,'off'=>110.7,'def'=>94.1,'tempo'=>66.2),
        //Ohio St 85%
        array('name'=>'Ohio State','elo'=>1681,'off'=>123.0,'def'=>96.1,'tempo'=>67.1),
        array('name'=>'Oral Roberts','elo'=>1373,'off'=>109.4,'def'=>106.7,'tempo'=>71.8),
        //Illinois 92%
        array('name'=>'Illinois','elo'=>1807,'off'=>119.7,'def'=>87.6,'tempo'=>70.7),
        array('name'=>'Drexel','elo'=>1362,'off'=>107.8,'def'=>104.8,'tempo'=>64.2),
        //Georgia Tech 51%
        array('name'=>'Loyola Chicago','elo'=>1655,'off'=>111.1,'def'=>85.9,'tempo'=>64.2),
        array('name'=>'Georgia Tech','elo'=>1659,'off'=>114.0,'def'=>94.1,'tempo'=>67.8),
        //Tennessee 51%
        array('name'=>'Tennessee','elo'=>1602,'off'=>109.5,'def'=>87.0,'tempo'=>67.3),
        array('name'=>'Oregon State','elo'=>1591,'off'=>110.0,'def'=>98.4,'tempo'=>65.4),
        //Oklahoma St 74%
        array('name'=>'Oklahoma State','elo'=>1700,'off'=>110.8,'def'=>90.6,'tempo'=>72.0),
        array('name'=>'Liberty','elo'=>1517,'off'=>110.8,'def'=>101.0,'tempo'=>63.1),
        //SD State 66%
        array('name'=>'San Diego State','elo'=>1697,'off'=>111.5,'def'=>88.8,'tempo'=>66.1),
        array('name'=>'Syracuse','elo'=>1574,'off'=>114.5,'def'=>96.9,'tempo'=>69.2),
        //West Virginia 69%
        array('name'=>'West Virginia','elo'=>1633,'off'=>116.8,'def'=>95.1,'tempo'=>69.6),
        array('name'=>'Morehead State','elo'=>1492,'off'=>100.9,'def'=>95.5,'tempo'=>65.6),
        //Clemson 52%
        array('name'=>'Clemson','elo'=>1571,'off'=>107.6,'def'=>90.0,'tempo'=>64.2),
        array('name'=>'Rutgers','elo'=>1556,'off'=>109.3,'def'=>89.8,'tempo'=>67.8),
        //Houston 88%
        array('name'=>'Houston','elo'=>1742,'off'=>119.6,'def'=>89.4,'tempo'=>64.9),
        array('name'=>'Cleveland State','elo'=>1395,'off'=>101.5,'def'=>98.7,'tempo'=>66.3),
    );
}
