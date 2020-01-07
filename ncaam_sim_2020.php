<?php

$ncaam = new TrippSim();



class TrippSim {
    private $playin_games;
    private $bracket;
    private $kenpom_rankings;
    
    public function __construct(){
        $start = microtime(true);
        //work db in to cache results and only call to update once per day max
        $this->kenpom_rankings = $this->curl_kenpom();
        $this->print_kenpom();
        //same as above
        list($this->playin_games,$this->bracket) = $this->curl_bracketology();
        $this->print_bracket();
        echo '<br>===';
        echo '<br>Script finished in '.(microtime(true) - $start).' seconds';
        echo '<br>Memory usage: '.$this->convert(memory_get_usage(true));
    }
    
    public function convert($size){
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
    
    public function print_kenpom(){
        $th = array('Team');
        $tr = array();
        foreach($this->kenpom_rankings as $k=>$v){
            if($v['rank']['value']==1){
                foreach($v as $y=>$z){
                    $th[] = $z['name'];
                }
            }
            $td = array($k);
            foreach($v as $y=>$z){
                $td[] = $z['value'];
            }
            $tr[] = '<td>'.implode('</td><td>',$td).'</td>';
        }
        $thead = '<th>'.implode('</th><th>',$th).'</th>';
        $tbody = '<tr>'.implode('</tr><tr>',$tr).'</tr>';
        echo '<table><thead><tr>';
        echo $thead;
        echo '</tr></thead><tbody>';
        echo $tbody;
        echo '</tbody></table>';
    }
    
    public function print_bracket(){
    }
    
    protected function curl_bracketology(){
        $html = $this->curl_url('http://www.espn.com/mens-college-basketball/bracketology');

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
    
    protected function curl_kenpom(){
        $html = $this->curl_url('https://kenpom.com/index.php');
        $regex = '@<td class="hard_left">(\d*)</td><td[^>]+><a[^>]*>([^<]+)</a></td><td[^>]*><a[^>]*>([^<]+)</a></td><td[^>]*>([^<]+)</td><td>([^<]+)</td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td><td[^>]*><span[^>]*>([^<]+)</span></td><td[^>]*>([^<]+)</td>@';
        preg_match_all($regex,$html,$matches);
        $matches = array_filter($matches);
        $kenpom = array();
        if(count($matches[0])){
            for($i=0;$i<count($matches[0]);$i++){
                $name = $matches[2][$i];
                $temp = array(
                    'rank' => array(
                        'name'=>'Rank',
                        'value'=> $matches[1][$i],
                    ),
                    'conference' => array(
                        'name'=>'Conference',
                        'value'=> $matches[3][$i],
                    ),
                    'record' => array(
                        'name'=>'Record',
                        'value'=> $matches[4][$i],
                    ),
                    'adjem' => array(
                        'name'=>'Adjusted Efficiency Margin (AdjEM)',
                        'value'=> $matches[5][$i],
                    ),
                    'adjo' => array(
                        'name'=>'Adjusted Offense',
                        'value'=> $matches[6][$i],
                    ),
                    'offr' => array(
                        'name'=>'Offense Rank',
                        'value'=> $matches[7][$i],
                    ),
                    'adjd' => array(
                        'name'=>'Adjusted Defense',
                        'value'=> $matches[8][$i],
                    ),
                    'defr' => array(
                        'name'=>'Defense Rank',
                        'value'=> $matches[9][$i],
                    ),
                    'adjt' => array(
                        'name'=>'Adjusted Tempo',
                        'value'=> $matches[10][$i],
                    ),
                    'temr' => array(
                        'name'=>'Tempo Rank',
                        'value'=> $matches[11][$i],
                    ),
                    'luck' => array(
                        'name'=>'Luck',
                        'value'=> $matches[12][$i],
                    ),
                );
                $kenpom[$name] = $temp;
            }
        }
        return $kenpom;
    }
    
    protected function curl_url($url){
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
}
?>
