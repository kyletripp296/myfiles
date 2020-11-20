<?php
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
?>
