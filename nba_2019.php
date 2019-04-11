<?php
// ELO based on https://projects.fivethirtyeight.com/2019-nba-predictions/
$teams = array(
    array('name'=>'GSW','elo'=>1842),
    array('name'=>'LAC','elo'=>1500),
    array('name'=>'HOU','elo'=>1749),
    array('name'=>'UTH','elo'=>1701),
    array('name'=>'POR','elo'=>1573),
    array('name'=>'OKC','elo'=>1683),
    array('name'=>'DEN','elo'=>1682),
    array('name'=>'SAN','elo'=>1536),
    array('name'=>'MIL','elo'=>1727),
    array('name'=>'DET','elo'=>1542),
    array('name'=>'BOS','elo'=>1602),
    array('name'=>'IND','elo'=>1543),
    array('name'=>'PHI','elo'=>1681),
    array('name'=>'BKN','elo'=>1477),
    array('name'=>'TOR','elo'=>1768),
    array('name'=>'ORL','elo'=>1536),
);
$bestof = 7;


//dont edit after this point
$rwins = floor($bestof/2)+1;
$round_num = 1;
do {
    echo 'Round '.$round_num.'<br>-------------<br>';
    $newarr = array();
    for($i=0;$i<count($teams);$i+=2){
        $t1 = $teams[$i];
        $t2 = $teams[$i+1];
        echo $t1['name'].' vs '.$t2['name'].'<br>';
        $t1wins = 0;
        $t2wins = 0;
        $game_num = 1;
        do {
                  $playto = mt_rand(98,120);
            $t1points = 0;
            $t2points = 0;
            do {
                $t1rand = rand(0,$t1['elo']);
                $t2rand = rand(0,$t2['elo']);
                if($t1rand>$t2rand){
                    $t1points++;
                } elseif($t1rand<$t2rand){
                    $t2points++;
                }
            } while($t1points<$playto && $t2points<$playto);
            if($t1points==$playto){
                $t1wins++;
                echo 'Game '.$game_num.': '.$t1['name'].' wins '.$t1points.'-'.$t2points.'<br>';
            } else {
                $t2wins++;
                echo 'Game '.$game_num.': '.$t2['name'].' wins '.$t2points.'-'.$t1points.'<br>';
            }
            $game_num++;
        } while($t1wins<$rwins && $t2wins<$rwins);
        $newarr[] = ($t1wins>$t2wins) ? $t1 : $t2;
        echo ($t1wins>$t2wins) ? $t1['name'].' beats '.$t2['name'].' '.$t1wins.'-'.$t2wins : $t2['name'].' beats '.$t1['name'].' '.$t2wins.'-'.$t1wins;
        echo '<br><br>';
    }
    $teams = $newarr;
    echo '<br>';
    $round_num++;
} while(count($teams)>1);
echo $teams[0]['name'].' wins!';

?>
