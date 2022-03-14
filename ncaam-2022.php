<html><body><table>
    <thead>
        <tr>
            <th>Matchup</th>
            <th>Score</th>
            <th>Winner</th>
        </tr>
    </thead>
    <tbody>
<?php 
main();
?>
</tbody>
</table></body></html>

<?php 
function main(){
    $teams = buildBracket();
    while(count($teams)>1){
        $winners = [];
        for($i=0;$i<count($teams);$i+=2){
            echo '<tr><td>'.$teams[$i]->name.' vs '.$teams[$i+1]->name.'</td><td>';
            if(battle2($teams[$i]->rating,$teams[$i+1]->rating)){
                $winners[] = $teams[$i];
                echo $teams[$i]->name;
            } else {
                $winners[] = $teams[$i+1];
                echo $teams[$i+1]->name;
            }
            echo "</td></tr>\n";
        }
        $teams = $winners;
        echo "\n";
    }
    echo 'winner: '.$teams[0]->name;
}

function buildBracket(){
    $bracket = [];
    //west
    $bracket[] = createTeam(['name'=>'Gonzaga','rating'=>952]);
    $bracket[] = createTeam(['name'=>'Georgia St','rating'=>736]);
    $bracket[] = createTeam(['name'=>'Boise St','rating'=>831]);
    $bracket[] = createTeam(['name'=>'Memphis','rating'=>850]);
    $bracket[] = createTeam(['name'=>'UCONN','rating'=>871]);
    $bracket[] = createTeam(['name'=>'New Mexico St','rating'=>789]);
    $bracket[] = createTeam(['name'=>'Arkansas','rating'=>869]);
    $bracket[] = createTeam(['name'=>'Vermont','rating'=>791]);
    $bracket[] = createTeam(['name'=>'Alabama','rating'=>852]);
    $bracket[] = createTeam(['name'=>'Rutg/ND','rating'=>816]);
    $bracket[] = createTeam(['name'=>'Texas Tech','rating'=>891]);
    $bracket[] = createTeam(['name'=>'Montana St','rating'=>736]);
    $bracket[] = createTeam(['name'=>'Michigan St','rating'=>849]);
    $bracket[] = createTeam(['name'=>'Davidson','rating'=>817]);
    $bracket[] = createTeam(['name'=>'Duke','rating'=>895]);
    $bracket[] = createTeam(['name'=>'CS Fullerton','rating'=>721]);
    //east
    $bracket[] = createTeam(['name'=>'Baylor','rating'=>911]);
    $bracket[] = createTeam(['name'=>'Norfolk St','rating'=>702]);
    $bracket[] = createTeam(['name'=>'UNC','rating'=>850]);
    $bracket[] = createTeam(['name'=>'Marquette','rating'=>823]);
    $bracket[] = createTeam(['name'=>'St Marys','rating'=>853]);
    $bracket[] = createTeam(['name'=>'Wyo/Ind','rating'=>792]);
    $bracket[] = createTeam(['name'=>'UCLA','rating'=>897]);
    $bracket[] = createTeam(['name'=>'Akron','rating'=>743]);
    $bracket[] = createTeam(['name'=>'Texas','rating'=>865]);
    $bracket[] = createTeam(['name'=>'Va Tech','rating'=>849]);
    $bracket[] = createTeam(['name'=>'Purdue','rating'=>898]);
    $bracket[] = createTeam(['name'=>'Yale','rating'=>741]);
    $bracket[] = createTeam(['name'=>'Murray St','rating'=>808]);
    $bracket[] = createTeam(['name'=>'USF','rating'=>840]);
    $bracket[] = createTeam(['name'=>'Kentucky','rating'=>904]);
    $bracket[] = createTeam(['name'=>'St Peters','rating'=>736]);
    //south
    $bracket[] = createTeam(['name'=>'Arizona','rating'=>921]);
    $bracket[] = createTeam(['name'=>'Wrst/Bry','rating'=>698]);
    $bracket[] = createTeam(['name'=>'Seton Hall','rating'=>840]);
    $bracket[] = createTeam(['name'=>'TCU','rating'=>826]);
    $bracket[] = createTeam(['name'=>'Houston','rating'=>910]);
    $bracket[] = createTeam(['name'=>'UAB','rating'=>819]);
    $bracket[] = createTeam(['name'=>'Illinois','rating'=>882]);
    $bracket[] = createTeam(['name'=>'Chattanooga','rating'=>790]);
    $bracket[] = createTeam(['name'=>'Colorado St','rating'=>837]);
    $bracket[] = createTeam(['name'=>'Michigan','rating'=>852]);
    $bracket[] = createTeam(['name'=>'Tennessee','rating'=>903]);
    $bracket[] = createTeam(['name'=>'Longwood','rating'=>719]);
    $bracket[] = createTeam(['name'=>'Ohio St','rating'=>853]);
    $bracket[] = createTeam(['name'=>'Loyola Chi','rating'=>838]);
    $bracket[] = createTeam(['name'=>'Villanova','rating'=>897]);
    $bracket[] = createTeam(['name'=>'Deleware','rating'=>731]);
    //midwest
    $bracket[] = createTeam(['name'=>'Kansas','rating'=>913]);
    $bracket[] = createTeam(['name'=>'Txso/Tamc','rating'=>694]);
    $bracket[] = createTeam(['name'=>'San Diego St','rating'=>842]);
    $bracket[] = createTeam(['name'=>'Creighton','rating'=>825]);
    $bracket[] = createTeam(['name'=>'Iowa','rating'=>893]);
    $bracket[] = createTeam(['name'=>'Richmond','rating'=>801]);
    $bracket[] = createTeam(['name'=>'Providence','rating'=>838]);
    $bracket[] = createTeam(['name'=>'S Dak St','rating'=>801]);
    $bracket[] = createTeam(['name'=>'LSU','rating'=>864]);
    $bracket[] = createTeam(['name'=>'Iowa St','rating'=>814]);
    $bracket[] = createTeam(['name'=>'Wisconsin','rating'=>853]);
    $bracket[] = createTeam(['name'=>'Colgate','rating'=>741]);
    $bracket[] = createTeam(['name'=>'USC','rating'=>845]);
    $bracket[] = createTeam(['name'=>'Miami Fl','rating'=>810]);
    $bracket[] = createTeam(['name'=>'Auburn','rating'=>893]);
    $bracket[] = createTeam(['name'=>'Jax St','rating'=>728]);
    return $bracket;
}

function createTeam($atts){
    $obj = (object)[];
    if(count($atts)){
        foreach($atts as $key=>$val){
            $obj->$key = $val;
        }
    }
    return $obj;
}

function battle($a,$b){
    $d = 4; //score to reach
    $e = 0; //start at 0 (score goes up or down each loop)
    do {
        //get two distinct random numbers
        do {
            $a1 = mt_rand(0,$a);
            $b1 = mt_rand(0,$b);
        } while($a1==$b1);
        //adjust score
        if($a1>$b1){
            $e++;
        } else {
            $e--;
        }
    } while(-1*$d<$e && $d>$e); //play until one team reaches score
    return $e>0; //return true if $a won
}

function battle2($a,$b){
    $a_score = 0;
    $b_score = 0;
    $p = 120;
    for($i=0;$i<$p;$i++){
        //a gets a posession
        if(mt_rand(0,$a)>mt_rand(0,$b)){
            $a_score++;
        }
        //b gets a posession
        if(mt_rand(0,$b)>mt_rand(0,$a)){
            $b_score++;
        }
    }
    while($a_score==$b_score){
        for($i=0;$i<$p/5;$i++){
            //a gets a posession
            if(mt_rand(0,$a)>mt_rand(0,$b)){
                $a_score++;
            }
            //b gets a posession
            if(mt_rand(0,$b)>mt_rand(0,$a)){
                $b_score++;
            }
        }
    }
    echo $a_score.'-'.$b_score.'</td><td>';
    return $a_score>$b_score;
}

function percentage($a,$b){
    return number_format(($a/$b)*100,2).'%';
}
