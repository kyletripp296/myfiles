<?php
class Deck {
    public $cards;
    public $card_titles;
    public $card_quantities;
    
    public function __construct(){
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
?>
