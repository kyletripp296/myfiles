<?php 
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
