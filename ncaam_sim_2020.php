<?php

$kenpom = curl_kenpom();
echo '<pre>'.print_r($kenpom,true).'</pre>';

function curl_bracketology(){
    $html = curl_url('http://www.espn.com/mens-college-basketball/bracketology');
    
    $bracket = array();
    $regions_regex = '@<h3><b>([^<]+)</b></h3>@';
    preg_match_all($regions_regex,$html,$regions);
    if(count($regions[1])){
        foreach($regions[1] as $r){
            $bracket[$r] = array();
        }
    }
    
    $playin_regex = '@<span class="rank">(\d+)</span><a[^>]+>([^<]+)</a> / <span class="rank">\d+</span><a[^>]+>([^<]+)</a>@';
    preg_match_all($playin_regex,$html,$playins);
    $playin_games = array();
    if(count($playins[0])){
        for($i=0;$i<count($playins[0]);$i++){
            
        }
    }
    
}
function curl_kenpom(){
    $html = curl_url('https://kenpom.com/index.php');
    $regex = '@<td class="hard_left">(\d*)</td><td[^>]+><a[^>]*>([^<]+)</a></td><td[^>]*><a[^>]*>([^<]+)</a></td><td[^>]*>([^<]+)</td><td>([^<]+)</td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td>@';
    preg_match_all($regex,$html,$matches);
    $matches = array_filter($matches);
    $kenpom = array();
    if(count($matches[0])){
        for($i=0;$i<count($matches[0]);$i++){
            $name = $matches[2][$i];
            $temp = array(
                'Rank' => $matches[1][$i],
                'Conference' => $matches[3][$i],
                'Record' => $matches[4][$i],
                'Adjusted Efficiency Margin (AdjEM)' => $matches[5][$i],
                'Adjusted Offense' => $matches[6][$i],
                'Offense Rank' => $matches[7][$i],
                'Adjusted Defense' => $matches[8][$i],
                'Defense Rank' => $matches[9][$i],
                'Adjusted Tempo' => $matches[10][$i],
                'Tempo Rank' => $matches[11][$i],
                'Luck' => $matches[12][$i],
            );
            $kenpom[$name] = $temp;
        }
    }
    return $kenpom;
}

function curl_url($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
?>
