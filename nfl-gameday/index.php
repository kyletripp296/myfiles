<?php

$game = new Game();
$game->play();

class Game {
    public $kicker;
    public $team1;
    public $team2;
    public $half;
    public $posession;
    public $odeck;
    public $ddeck;
    public $sdeck;
    public $kickoff;
    public $scrimmage;
    
    public function __construct(){
        $this->kicker = new Kicker();
        //user input, team names
        $this->team1 = new Team('Dolphins');
        $this->team2 = new Team('Raiders');
        $this->posession = false; //false means team 1 has it, true means team 2
        $this->odeck = new OffenseDeck();
        $this->ddeck = new DefenseDeck();
        $this->sdeck = new SpecialTeamsDeck();
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
            while(count($this->odeck)){
                if($this->kickoff){
                    $this->doKickoff();
                }
            }
            */
        }
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

class Team {
    public $name;
    public $score;
    public $timeouts;
    public $cards;
    
    public function __construct($name){
        $this->name = $name;
        $this->score = 0;
        $this->timeouts = 3;
        $this->cards = array();
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getScore(){
        return $this->score;
    }
    
    public function getTimeouts(){
        return $this->timeouts;
    }
    
    public function getCards(){
        return $this->cards;
    }
    
    public function canCallTimeout(){
        return $this->timeouts > 0;
    }
    
    public function callTimeout(){
        $this->timeouts -= 1;
    } 
}

class Kicker {
    public $kick_results;
    public $kick_distances;
    public $kick_results_relationship;
    public $in_range;
    
    public function __construct(){
        $this->kick_results = array(
            'gd' => 'Good',
            'ng' => 'No Good',
            'td' => 'Fake FG / Touchdown',
            'f5' => 'Fake FG 1st Down / Ball On 5 YD Line',
            'b2' => 'Blocked, Run Back 20 Yards',
            'bt' => 'Blocked, Run Back For Defensive TD',
        );
        $this->kick_distances = array(
            '10y' => '5 / 10 Yard Line',
            '20y' => '15 / 20 yard Line',
            '30y' => '25 / 30 Yard Line',
            '40y' => '35 / 40 Yard Line'
        );
        $this->kick_results_relationship = array(
            '10y' => array(1=>'gd',2=>'gd',3=>'ng',4=>'gd',5=>'gd',6=>'td'),
            '20y' => array(1=>'gd',2=>'ng',3=>'gd',4=>'gd',5=>'ng',6=>'f5'),
            '30y' => array(1=>'gd',2=>'ng',3=>'gd',4=>'ng',5=>'gd',6=>'b2'),
            '40y' => array(1=>'ng',2=>'ng',3=>'gd',4=>'gd',5=>'ng',6=>'bt'),
        );
    }
    
    public function kick($yard_line){
        $key = (10*round($yard_line/10))+'y';
        $roll = mt_rand(1,6);
        return $this->kick_results_relationship[$key][$roll];
    }
    
    public function getTitle($key){
        return $this->kick_results[$key];
    }
}

class Deck {
    public $cards;
    public $card_titles;
    public $card_quantities;
    public $card_results;
    public $card_results_relationship;
    
    public function __construct(){
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
            'pdf' => array('nog','flg','flg','hfg','5yg'),
            'rdf' => array('flg','nog','nog','flg','1yg'),
            'fbl' => array('2xg','tov','dtd','tov','1yg'),
            'int' => array('tov','2xg','flg','tov','dtd'),
            'zbl' => array('1yl','f10','f10','5yl','nog'),
            'aob' => array('f10','1yl','nog','1yl','2yg'),
            'pfl' => array('1yd','1yr','5yd','flg','5yr'),
        );
        
        $this->card_titles = $this->getCardTitles();
        $this->card_quantities = $this->getCardQuantities();
        $this->cards = $this->newDeck();
    }
    
    public function newDeck(){
        $cards = array();
        foreach($this->card_quantities as $key=>$value){
            for($i=0;$i<$value;$i++){
                $cards[] = $key;
            }
        }
        shuffle($cards);
        return $cards;
    }
    
    public function getTitle($key){
        return $this->card_titles[$key];
    }
    
    public function drawCard(){
        return array_shift($this->cards);
    }
    
    public function getNewDeck(){
        $this->cards = $this->newDeck();
        return $this->cards;
    }
    
}

class OffenseDeck extends Deck {
    public function getCardTitles(){
        return array(
            '10p' => '10 Yard Pass',
            '20p' => '20 Yard Pass',
            '30p' => '30 Yard Pass',
            '40p' => '40 Yard Pass',
            '50p' => '50 Yard Pass',
            '10r' => '10 Yard Run',
            '15r' => '15 Yard Run',
            '20r' => '20 Yard Run',
            '25r' => '25 Yard Run',
            '10d' => '10 Yard Draw',
            '15d' => '15 Yard Draw',
            '20d' => '20 Yard Draw',
            '10a' => '10 Yard Play Action',
            '30a' => '30 Yard Play Action',
            '50a' => '50 Yard Play Action',
            'scr' => 'Screen Pass'
        );
    }
    public function getCardQuantities(){
        return array(
            '10p' => 7,
            '20p' => 6,
            '30p' => 4,
            '40p' => 3,
            '50p' => 2,
            '10r' => 6,
            '15r' => 5,
            '20r' => 4,
            '25r' => 3,
            '10d' => 3,
            '15d' => 2,
            '20d' => 1,
            '10a' => 5,
            '30a' => 3,
            '50a' => 1,
            'scr' => 5
        );
    }
}

class DefenseDeck extends Deck {
    public function getCardTitles(){
        return array(
            'pdf' => 'Pass Defense',
            'rdf' => 'Run Defense',
            'fbl' => 'Fumble',
            'int' => 'Interception',
            'zbl' => 'Zone Blitz',
            'aob' => 'All Out Blitz',
            'pfl' => 'Penalty Flag'
        );
    }
    public function getCardQuantities(){
        return array(
            'pdf' => 28,
            'rdf' => 16,
            'fbl' => 4,
            'int' => 4,
            'zbl' => 3,
            'aob' => 3,
            'pfl' => 2
        );
    }
}

class SpecialTeamsDeck extends Deck {
    public function getCardTitles(){
        return array(
            '10y' => 'Kickoff Start At Own 10 / 35 Yard Punt',
            '20y' => 'Kickoff Start At Own 20 / 40 Yard Punt',
            '30y' => 'Kickoff Start At Own 30 / 45 Yard Punt',
            '40y' => 'Kickoff Start At Own 40 / 50 Yard Punt',
            '50y' => 'Kickoff Start At Own 50 / 20 Yard Punt',
            'otd' => 'Receiving Team Scores TD',
            'dtd' => 'Receiving Team Fumbles, Kicking Team Scores TD',
        );
    }
    public function getCardQuantities(){   
        return array(
            '10y' => 3,
            '20y' => 8,
            '30y' => 3,
            '40y' => 2,
            '50y' => 2,
            'otd' => 1,
            'dtd' => 1
        );
    }
}


?>
