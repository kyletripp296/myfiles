<?php
$sp = new StreakParser();
for($i=1;$i<=30;$i++){
    $headers = ($i==1) ? true : false;
    $sp->update_config(array('add_headers'=>$headers));
    $t = mktime(0,0,0,1,$i,2020);
    $s = date('Ymd',$t);
    $sp->store_questions_for_day($s); //Right now this just prints out a csv for all days in January 2020
}


class StreakParser {
    private $regex;
    private $base_url;
    private $headers;
    
    public function __construct(){
        //The main point of this whole class, parse their html pulling specific data out for us to use
        $this->regex = '@matchup-container[^>]+><[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+>(.*)(?=</div></td>)<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>(.*)(?=</span>)<[^>]+><[^>]+>(.*)(?=</span>)<[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>([^<]+)<[^>]+>.*had ([^%]+%)[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>[^<]*<[^>]+><[^>]+><[^>]+><[^>]+><[^>]+>(.*)(?=</span>)<[^>]+><[^>]+>(.*)(?=</span><)<[^>]+><[^>]+>([^<]+)<[^>]+><[^>]+><[^>]+><[^>]+>([^<]+)<@U';
        //The base url to get, we'll have to append a date string onto it when we curl
        $this->base_url = 'http://fantasy.espn.com/streak/en/entry?date=';
        //hard coded headers
        $this->headers = array(
            'Question',
            'Date',
            'Time',
            'Network',
            'Sport',
            'Option 1 Result',
            'Option 1 Value',
            'Option 1 Score',
            'Status',
            'Option 1 Percent',
            'Active Pick Percent',
            'Option 2 Result',
            'Option 2 Value',
            'Option 2 Score',
            'Option 2 Percent',
        );
        //set default configs
        $this->config = array(
            'add_headers' => true,
            'separator' => ',',
        );
    }
    
    public function update_config($config){   
        if(is_array($config) && count($config)){
            foreach($config as $key=>$value){
                $this->config[$key] = $value;
            }
        }
    }
    
    public function store_questions_for_day($t){
        $html = $this->curl_url($this->base_url.$t);
        $matches = $this->parse_html($html);
        $results = $this->process_results($matches,$t);
        $csv = $this->array_to_csv($results);
        echo $csv;
    }
    
    protected function curl_url($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    
    protected function parse_html($html){
        preg_match_all($this->regex,$html,$matches);
        return $matches;
    }
    
    protected function process_results($matches,$t){
        $results = array();
        if($this->config['add_headers']){
            $results[] = $this->headers;
        }
        if(isset($matches[1]) && count($matches[1])){
            for($i=0;$i<count($matches[1]);$i++){
                $temp = array();
                for($j=1;$j<count($matches);$j++){
                    $temp[] = trim(htmlspecialchars(str_replace('"',"'",$matches[$j][$i])));
                    if($j==1){
                        $temp[] = $t;
                    }
                }
                $results[] = $temp;
            }
        }
        return $results;
    }
    
    protected function array_to_csv($array){
        ob_start();
        if(count($array)){
            foreach($array as $key=>$value){
                echo '"'.implode('"'.$this->config['separator'].'"',$value).'"<br>';
            }
        }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

?>
