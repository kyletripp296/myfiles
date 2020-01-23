<?php
$t = 101;
$s = array();
for($i=0;$i<$t;$i++){
    $s[$i] = new Strategy($i);
}

$round_robins = 1;
for($a=0;$a<$round_robins;$a++){
    for($b=1;$b<count($s);$b++){
        for($c=0;$c<$b;$c++){
            //echo $b.' vs '.$c.'<br>';
            $s[$b]->add_points( score($s[$b]->select_offense(), $s[$c]->select_defense($s[$b]->get_for_one())) );
            $s[$c]->add_points( score($s[$c]->select_offense(), $s[$b]->select_defense($s[$c]->get_for_one())) );
        }
    }
}

usort($s,function($a,$b){
    return $b->get_points() - $a->get_points();
});

echo '<pre>'.print_r($s,true).'</pre>';

function score($off,$def){
    if($off==1){
        if($def==1){
            //90% chance for 1 point
            if(mt_rand(0,9)){
                return 1;
            }
        } else {
            //100% chance for 1 point
            return 1;
        }
    } else {
        if($def==1){
            //60% chance for 2 points
            if(mt_rand(0,9)>3){
                return 2;
            }
        } else {
            //40% chance for 2 points
            if(mt_rand(0,9)>5){
                return 2;
            }
        }
    }
    return 0;
}

function score_expected($off,$def){
    if($off==1){
        if($def==1){
            //90% chance for 1 point
            return 0.9;
        } else {
            //100% chance for 1 point
            return 1;
        }
    } else {
        if($def==1){
            //60% chance for 2 points
            return 1.2;
        } else {
            //40% chance for 2 points
            return 0.8;
        }
    }
}


class Strategy {
    protected $for_one;
    protected $for_two;
    protected $points;
    
    public function __construct($n){
        if(!is_int($n) && $n<0 && $n>100){
            exit('improper use');
        }
        $this->for_one = $n;
        $this->for_two = 100 - $n;
        $this->points = 0;
    }
    
    public function get_for_one(){
        return $this->for_one;
    }
    
    public function get_points(){
        return (float)$this->points;
    }
    
    public function add_points($n){
        $this->points += $n;
    }
    
    public function select_offense(){
        $n = mt_rand(0,99);
        if($n<$this->for_one){
            return 1;
        } else {
            return 2;
        }
    }
    
    public function select_defense($for_one){
        $n = mt_rand(0,99);
        if($n<$for_one){
            return 1;
        } else {
            return 2;
        }
    }
}

?>
