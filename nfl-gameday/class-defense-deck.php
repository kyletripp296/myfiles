<?php
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
?>
