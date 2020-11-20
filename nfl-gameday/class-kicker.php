<?php
class Kicker {
    public $kick_results;
    public $kick_results_relationship;
    
    public function __construct(){
        $this->kick_results = array(
            'gd' => 'Good',
            'ng' => 'No Good',
            'td' => 'Fake FG / Touchdown',
            'f5' => 'Fake FG 1st Down / Ball On 5 YD Line',
            'b2' => 'Blocked, Run Back 20 Yards',
            'bt' => 'Blocked, Run Back For Defensive TD',
        );
        $this->kick_results_relationship = array(
            '10y' => array(1=>'gd',2=>'gd',3=>'ng',4=>'gd',5=>'gd',6=>'td'),
            '20y' => array(1=>'gd',2=>'ng',3=>'gd',4=>'gd',5=>'ng',6=>'f5'),
            '30y' => array(1=>'gd',2=>'ng',3=>'gd',4=>'ng',5=>'gd',6=>'b2'),
            '40y' => array(1=>'ng',2=>'ng',3=>'gd',4=>'gd',5=>'ng',6=>'bt'),
        );
    }
    
    //takes a value like 5,10,15,20, returns a value like 'gd' or 'ng'
    public function kick($range){
        $key = (10*round($range/10))+'y';
        $roll = mt_rand(1,6);
        return $this->kick_results_relationship[$key][$roll];
    }
    
    public function getTitle($key){
        return $this->kick_results[$key];
    }
}
?>
