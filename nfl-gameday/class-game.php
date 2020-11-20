<?php 
class Game {
    public $kicker;
    public $team1;
    public $team2;
    public $posession;
    public $odeck;
    public $ddeck;
    public $sdeck;
    public $kickoff;
    public $scrimmage;
    public $first_down;
    public $card_results;
    public $card_results_relationship;
    
    public function __construct(){
        $this->kicker = new Kicker();
        
        //user input, team names
        $this->team1 = new Team('Dolphins');
        $this->team2 = new Team('Raiders');
        
        $this->posession = false; //false means team 1 has it, true means team 2
        
        $this->odeck = new OffenseDeck();
        $this->ddeck = new DefenseDeck();
        $this->sdeck = new SpecialTeamsDeck();
        
        $this->card_results = array(
            '2xg' => '2X Gain',
            'flg' => 'Full Gain',
            'hfg' => 'Half Gain',
            'nog' => 'No Gain',
            '5yg' => '5 YRD Gain',
            '1yg' => '10 YRD Gain',
            '2yg' => '20 YRD Gain',
            '5yl' => '-5 YRDS',
            '1yl' => '-10 YRDS',
            '1yd' => '-10 YRDS Lose Down',
            '5yr' => '+5 YRDS Replay Down',
            '5yd' => '-5 YRDS Replay Down',
            '1yr' => '-10 YRDS Replay Down',
            'f10' => 'Full Gain +10 YRDS',
            'tov' => 'Turnover',
            'dtd' => 'Defense Scores TD'
        );
        
        $this->card_results_relationship = array(
            'pdf' => array('p'=>'nog','r'=>'flg','d'=>'flg','a'=>'hfg','s'=>'5yg'),
            'rdf' => array('p'=>'flg','r'=>'nog','d'=>'nog','a'=>'flg','s'=>'1yg'),
            'fbl' => array('p'=>'2xg','r'=>'tov','d'=>'dtd','a'=>'tov','s'=>'1yg'),
            'int' => array('p'=>'tov','r'=>'2xg','d'=>'flg','a'=>'tov','s'=>'dtd'),
            'zbl' => array('p'=>'1yl','r'=>'f10','d'=>'f10','a'=>'5yl','s'=>'nog'),
            'aob' => array('p'=>'f10','r'=>'1yl','d'=>'nog','a'=>'1yl','s'=>'2yg'),
            'pfl' => array('p'=>'1yd','r'=>'1yr','d'=>'5yd','a'=>'flg','s'=>'5yr'),
        );
    }
    
    public function play(){
        echo 'Starting game between '.($this->team1->getName()).' and '.($this->team2->getName()).'<br>';
        do {
            $r1 = mt_rand(1,6);
            $r2 = mt_rand(1,6);
        } while($r1==$r2);
        echo ($this->team1->getName()).' roll a '.$r1.'<br>';
        echo ($this->team2->getName()).' roll a '.$r2.'<br>';
        echo ($r1>$r2) ? $this->team1->getName() : $this->team2->getName(), ' gets the ball first<br>';
        //user input, select offense or defense (currently always selects offense)
        $this->posession = ($r1>$r2);
        for($h=1;$h<=2;$h++){
            echo 'Starting the ',($h==1)?'1st':'2nd',' half<br>';
            $this->team1->cards = array();
            $this->team2->cards = array();
            $this->odeck->cards = $this->odeck->getNewDeck();
            $this->ddeck->cards = $this->ddeck->getNewDeck();
            $this->sdeck->cards = $this->sdeck->getNewDeck();
            $this->drawCards();
            echo 'Team 1 cards: '.implode(',',$this->team1->getCards()).'<br>';
            echo 'Team 2 cards: '.implode(',',$this->team2->getCards()).'<br>';
            $this->scrimmage = false;
            $this->kickoff = true;
            /*
            while(count($this->odeck->cards)){
                if($this->kickoff){
                    $this->doKickoff();
                }
            }
            */
        }
    }
    
    public function getResult($off,$def){
        return $this->card_results_relationship[$def][$off[2]];
    }
  
    public function drawCards(){
        while(count($this->team1->cards)<5){
            if($this->posession){
                $this->team1->cards[] = $this->odeck->drawCard();
            } else {
                $this->team1->cards[] = $this->ddeck->drawCard();
            }
        }
        while(count($this->team2->cards)<5){
            if($this->posession){
                $this->team2->cards[] = $this->ddeck->drawCard();
            } else {
                $this->team2->cards[] = $this->odeck->drawCard();
            }
        }
    }
  
    public function redrawCards(){
        if($this->posession){
            $this->odeck->cards = array_merge($this->odeck->cards,$this->team1->cards);
            $this->ddeck->cards = array_merge($this->ddeck->cards,$this->team2->cards);
        } else {
            $this->ddeck->cards = array_merge($this->ddeck->cards,$this->team1->cards);
            $this->odeck->cards = array_merge($this->odeck->cards,$this->team2->cards);
        }
        shuffle($this->odeck->cards);
        shuffle($this->odeck->cards);
        $this->team1->cards = array();
        $this->team2->cards = array();
        $this->drawCards();
    }
    
    function doKickoff(){
        //draw a card
        $card = $this->sdeck->drawCard();
        //evaluate card
        if($card=='10y'){
            $this->scrimmage = 10;
        } elseif($card=='20y'){
            $this->scrimmage = 20;
        } elseif($card=='30y'){
            $this->scrimmage = 30;
        } elseif($card=='40y'){
            $this->scrimmage = 40;
        } elseif($card=='50y'){
            $this->scrimmage = 50;
        } elseif($card=='otd'){
            if($this->posession){
                $this->doTouchdown($this->team1);
            } else {
                $this->doTouchdown($this->team2);
            }
        } elseif($card=='dtd'){
            if(!$this->posession){
                $this->doTouchdown($this->team1);
            } else {
                $this->doTouchdown($this->team2);
            }
        }
    }
    
    function doPunt(){
        //draw a card
        $card = $this->sdeck->drawCard();
        //evaluate card
        if($card=='10y'){
            $this->scrimmage += 35;
        } elseif($card=='20y'){
            $this->scrimmage += 40;
        } elseif($card=='30y'){
            $this->scrimmage += 45;
        } elseif($card=='40y'){
            $this->scrimmage += 50;
        } elseif($card=='50y'){
            $this->scrimmage += 20;
        } elseif($card=='otd'){
            if($this->posession){
                $this->doTouchdown($this->team1);
            } else {
                $this->doTouchdown($this->team2);
            }
        } elseif($card=='dtd'){
            if(!$this->posession){
                $this->doTouchdown($this->team1);
            } else {
                $this->doTouchdown($this->team2);
            }
        }
    }
    
    // extra point is good if 1-5, bad if 6
    function doExtraPoint(){
        return (mt_rand(1,6)!=6);
    }
    
    // on touchdown
    function doTouchdown(&$team){
        echo 'Touchdown '.($team->getName()).'<br>';
        echo 'Score is '.($this->team1->getName()).' '.($this->team1->getScore()).' '.($this->team2->getName()).' '.($this->team2->getScore()).'<br>';
        //add to team score
        $team->score += 6;
        //attempt extra point
        if($this->doExtraPoint()){
            $team->score += 1;
            echo 'Extra point is good<br>';
        } else {
            echo 'Extra point is no good<br>';
        }
        echo 'Score is '.($this->team1->getName()).' '.($this->team1->getScore()).' '.($this->team2->getName()).' '.($this->team2->getScore()).'<br>';
        //change posessions
        $this->posession = !$this->posession;
        //do kickoff
        $this->kickoff = true;
    }
    
    // on safety 
    function doSafety(&$team){
        echo 'Safety '.($team->getName()).'<br>';
        echo 'Score is '.($this->team1->getName()).' '.($this->team1->getScore()).' '.($this->team2->getName()).' '.($this->team2->getScore()).'<br>';
        //add to team score
        $team->score += 2;
        //change posessions
        $this->posession = !$this->posession;
        //do kickoff
        $this->kickoff = true;
    }
    
    public function doFirstDown(){
        $this->down = 1;
        $this->first_down = $this->scrimmage+10;
        if($this->first_down = 'goal'){
        }
    }
    
    public function doTimeout(){
        $this->redrawCards();
    }
    
    public function downAndDistance(){
        if($this->down==1){
            $down = '1st';
        } elseif($this->down==2){
            $down = '2nd';
        } elseif($this->down==3){
            $down = '3rd';
        } else {
            $down = '4th';
        }
        if($this->first_down>100){
            $distance = 'goal';
        } else {
            $distance = $this->first_down-$this->scrimmage;
        }
        echo $down.' down and '.$distance.' to go<br>';
    }
    
}
?>
