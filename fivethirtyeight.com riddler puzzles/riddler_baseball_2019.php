<?php

$games_to_play = 16200; //thats 100 seasons worth of baseball!


$runs = array();
for($i=0;$i<$games_to_play;$i++){
    $bg = new Ballgame();
    $runs[] = $bg->play_ballgame();
}
echo 'average runs scored: '.array_sum($runs)/count($runs).'<br>'; //spits out ~14.5
echo 'standard deviation: '.std_dev($runs).'<br>'; //spits out ~6



class Ballgame {
    protected $runs;
    
    public function __construct(){
        $this->runs = 0;
    }
    
    public function play_ballgame(){
        for($i=1; $i<=9; $i++){
            $inning = new Inning();
            $inning_runs = $inning->play_inning();
            $this->runs += $inning_runs;
        }
        return $this->runs;
    }
}




class Inning {
    protected $outs;
    protected $runs_scored;
    protected $bases;
    
    public function __construct(){
        $this->outs = 0;
        $this->runs_scored = 0;
        $this->bases = array('1st'=>false,'2nd'=>false,'3rd'=>false);
    }
    
    public function play_inning(){
        while($this->outs < 3){
            $batter = new Batter();
            $atbat_result = $batter->play_atbat();
            $this->update_runners($atbat_result);
        }
        return $this->runs_scored;
    }
    
    protected function update_runners($result){
        switch($result){
            case 'walk':
            case 'error':
            if($this->third()){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            if($this->second()){
                $this->bases['2nd'] = false;
                $this->bases['3rd'] = true;
            }
            if($this->first()){
                $this->bases['1st'] = false;
                $this->bases['2nd'] = true;
            }
            $this->bases['1st'] = true;
            break;
            
            case 'single':
            if($this->third()){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            if($this->second()){
                $this->bases['2nd'] = false;
                $this->runs_scored++;
            }
            if($this->first()){
                $this->bases['1st'] = false;
                $this->bases['2nd'] = true;
            }
            $this->bases['1st'] = true;
            break;
            
            case 'double':
            if($this->third()){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            if($this->second()){
                $this->bases['2nd'] = false;
                $this->runs_scored++;
            }
            if($this->first()){
                $this->bases['1st'] = false;
                $this->bases['3rd'] = true;
            }
            $this->bases['2nd'] = true;
            break;
            
            case 'triple':
            if($this->third()){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            if($this->second()){
                $this->bases['2nd'] = false;
                $this->runs_scored++;
            }
            if($this->first()){
                $this->bases['1st'] = false;
                $this->runs_scored++;
            }
            $this->bases['3rd'] = true;
            break;
            
            case 'home run':
            if($this->third()){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            if($this->second()){
                $this->bases['2nd'] = false;
                $this->runs_scored++;
            }
            if($this->first()){
                $this->bases['1st'] = false;
                $this->runs_scored++;
            }
            $this->runs_scored++;   
            break;
            
            case 'strikeout':
            case 'foul out':
            $this->outs++;
            break;
            
            case 'fly out':
            $this->outs++;
            if($this->outs < 3 && $this->bases['3rd']){
                $this->bases['3rd'] = false;
                $this->runs_scored++;
            }
            break;
            
            case 'out at 1st':
            $this->outs++;
            if($this->outs < 3){
                if($this->third()){
                    $this->bases['3rd'] = false;
                    $this->runs_scored++;
                }
                if($this->second()){
                    $this->bases['2nd'] = false;
                    $this->bases['3rd'] = true;
                }
                if($this->first()){
                    $this->bases['1st'] = false;
                    $this->bases['2nd'] = true;
                }
            }
            break;
            
            case 'double play':
            if($this->first() || $this->second() || $this->third()){
                if($this->first()){
                    $this->outs++;
                    $this->bases['1st'] = false;
                    $this->outs++;
                    if($this->outs < 3){
                        if($this->third()){
                            $this->bases['3rd'] = false;
                            $this->runs_scored++;
                        }
                        if($this->second()){
                            $this->bases['2nd'] = false;
                            $this->bases['3rd'] = true;
                        }
                    }
                }
                elseif($this->second()){
                    $this->outs++;
                    $this->bases['2nd'] = false;
                    $this->outs++;
                    if($this->outs < 3 && $this->third()){
                        $this->bases['3rd'] = false;
                        $this->runs_scored++;
                    }
                }
                else {
                    $this->outs++;
                    $this->bases['3rd'] = false;
                    $this->outs++;
                }
                if($this->outs > 3){
                    $this->outs = 3;
                }
            } else {
                $this->outs++;
            }
            break;
            default: return;
        }
    }    
     
    protected function first(){
        return $this->bases['1st'];
    }
    protected function second(){
        return $this->bases['2nd'];
    }
    protected function third(){
        return $this->bases['3rd'];
    }
}




class Batter {
    protected $strikes;
    
    public function __construct(){
        $this->strikes = 0;
    }
    
    public function play_atbat(){
        do {
            $roll = $this->roll_dice();
            $result = $this->evaluate_roll($roll);
            if($result=='strike'){
                $this->strikes++;
                if($this->strikes==3){
                    return 'strikeout';
                }
            }
        } while($result=='strike');
        return $result;
    }
    
    protected function roll_dice(){
        $dice = array(rand(1,6), rand(1,6));
        sort($dice);
        return implode($dice);
    }   
    
    function evaluate_roll($roll){
        switch($roll){
            case '11': $s = 'double';break;
            case '12': $s = 'single';break;
            case '13': $s = 'single';break;
            case '14': $s = 'single';break;
            case '15': $s = 'error';break;
            case '16': $s = 'walk';break;
            case '22': $s = 'strike';break;
            case '23': $s = 'strike';break;
            case '24': $s = 'strike';break;
            case '25': $s = 'strike';break;
            case '26': $s = 'foul out';break;
            case '33': $s = 'out at 1st';break;
            case '34': $s = 'out at 1st';break;
            case '35': $s = 'out at 1st';break;
            case '36': $s = 'out at 1st';break;
            case '44': $s = 'fly out';break;
            case '45': $s = 'fly out';break;
            case '46': $s = 'fly out';break;
            case '55': $s = 'double play';break;
            case '56': $s = 'triple';break;
            case '66': $s = 'home run';break;
            default: $s = false;
        }
        return $s;
    }
}


function std_dev($arr) { 
    $num_of_elements = count($arr); 
    $variance = 0.0;
    $average = array_sum($arr)/$num_of_elements; 
    foreach($arr as $i) { 
        $variance += pow(($i - $average), 2); 
    } 
    return (float)sqrt($variance/$num_of_elements); 
} 

?>
