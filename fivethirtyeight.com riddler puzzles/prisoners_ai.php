<?php

class AI {
    protected $prisoners;
    
    public function __construct($n){
        echo $n.' prisoners<br>';
        for($i=0;$i<$n;$i++){
            $this->prisoners[] = new Prisoner();
        }
    }
    
    public function playGame(){
        $data = array();
        
        $b = $this->generateBits();
        echo 'bits: '.implode('',$b).'<br>';
        $data['bits'] = implode('',$b);
        
        $flips = array();
        $c = 0;
        foreach($this->prisoners as $key=>$p){
            $d = $p->decideToFlip($b[$key]);
            $flips[] = ($d) ? 1 : 0;
            $c += ($d) ? 1 : 0;
        }
        echo 'flips: '.implode('',$flips).'<br>';
        $data['flips'] = implode('',$flips);
        $data['num_flips'] = $c;
        
        // d stands for death, 0 is alive and any other number tracks the cause of death
        $d = 0;
        if(!$c){
            echo 'Nobody flipped, you all die<br>';
            $d = count($flips)+1;
        } else {
            foreach($flips as $k=>$v){
                if($v){
                    if(mt_rand(0,1)){
                        echo 'Prisoner '.$k.' flipped heads<br>';
                    } else {
                        echo 'Prisoner '.$k.' flipped tails, you all die<br>';
                        $d = $k+1;
                        break;
                    }
                }
            }
        }
        $data['state'] = $d;
        
        // prisoner decides to flip, prisoners survive, reward ai 
        // prisoner decides to flip, prisoners die, punish ai 
        // prisoner doesnt flip, prisoners survive, reward ai 
        // prisoner doesnt flip, prisoners survive, punish ai 
        if(!$d){
            echo 'Prisoners survived!<br>';
            foreach($this->prisoners as $key=>$p){
                $p->setProb( $flips[$key], $p->getProb($flips[$key]) + 11 );
            }
        } else {
            foreach($this->prisoners as $key=>$p){
                $p->setProb( $flips[$key], $p->getProb($flips[$key]) - 4 );
            }
        }
        
        return $data;  
    }
    
    public function generateBits(){
        //array of 4 values; 0 or 1
        $arr = array();
        for($i=0;$i<=3;$i++){
            $arr[] = mt_rand(0,1);
        }
        return $arr;
    }
    
    public function printStats(){
        //print prisoner probabilites
        foreach($this->prisoners as $key=>$p){
            echo $key.' 0:'.$p->getProb(0).' 1:'.$p->getProb(1).'<br>';
        }
    }
}
    
//each prisoner only sees a number 1 or 0 and must decide to flip a coin yes or no
class Prisoner {
    private $prob;
    
    public function __construct(){
        $this->prob = array(0,0);
    }
    
    public function setProb($bit,$val){
        return $this->prob[$bit] = $val;
    }
    
    public function getProb($bit){
        return $this->prob[$bit];
    }
    
    public function decideToFlip($bit){
        return (0.5 + ($this->getProb($bit)/1000)) > (mt_rand(0,999)/1000);
    }
}


//heres what youre allowed to play with 
$num_prisoners = 4;
$games_to_play = 20000;
$debug = false;



//main.php
ob_start();
$games = array();
$ai = new AI($num_prisoners);
for($i=0;$i<$games_to_play;$i++){
    echo 'Game '.$i.'<br>';
    $games[] = $ai->playGame();
    echo '<br>';
}
$c = ob_get_contents();
ob_end_clean();
if($debug){
    echo $c;
}

echo 'Final Stats<br>';
$ai->printStats();

echo 'Distribution of random bits assigned and count of each<br>';
$bits = array_count_values(array_column($games, 'bits'));
arsort($bits);
echo '<pre>'.print_r($bits,true).'</pre>';

echo 'Distribution of how many flips were attempted in each game<br>';
$nflips = array_count_values(array_column($games, 'num_flips'));
ksort($nflips);
echo '<pre>'.print_r($nflips,true).'</pre>';

echo 'Distribution of which combinations of prisoners flipped the most often<br>';
$flips = array_count_values(array_column($games, 'flips'));
arsort($flips);
echo '<pre>'.print_r($flips,true).'</pre>';

echo 'Distribution of final state (0 is survive, 1-n is tails, n+1 is no flips)<br>';
$states = array_count_values(array_column($games, 'state'));
arsort($states);
echo '<pre>'.print_r($states,true).'</pre>';

echo 'Final Survival Rate: '.($states[0]/array_sum($states) * 100).'%';

?>
