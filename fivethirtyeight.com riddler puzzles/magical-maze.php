<?php

/*

If you come to a hex with a consonant, you must turn left: either a mild left (60 degrees, or one hex to the left), or a sharp left (120 degrees).
If you come to a hex with a vowel (“Y” is a vowel), you must turn right: either a mild right (60 degrees, or one hex to the right), or a sharp right (120 degrees).
You may never proceed straight or back directly up.
If you travel outside of the pictured hexes, or enter the dreaded gray hex, you must return to the start.
You must pass through the letter “M” (node 13) before you are allowed to finish.

*/

//GLOBALS
global $matrix;
$matrix = array(
    0 => array('vcw'=>'w','letter'=>'Win!','nodes'=>array(null,null,2,1,null,null)),
    1 => array('vcw'=>'v','letter'=>'I','nodes'=>array(0,2,3,null,null,null)),
    2 => array('vcw'=>'c','letter'=>'F','nodes'=>array(null,null,4,3,1,0)),
    3 => array('vcw'=>'c','letter'=>'B','nodes'=>array(2,4,8,null,null,1)),
    4 => array('vcw'=>'c','letter'=>'L','nodes'=>array(null,5,9,8,3,2)),
    5 => array('vcw'=>'v','letter'=>'U','nodes'=>array(null,6,10,9,4)),
    6 => array('vcw'=>'v','letter'=>'E','nodes'=>array(null,null,11,10,5,null)),
    7 => array('vcw'=>'c','letter'=>'Z','nodes'=>array(null,null,13,12,null,null)),
    8 => array('vcw'=>'v','letter'=>'A','nodes'=>array(4,9,null,14,null,3)),
    9 => array('vcw'=>'c','letter'=>'S','nodes'=>array(5,10,15,null,8,4)),
    10 => array('vcw'=>'c','letter'=>'K','nodes'=>array(6,11,16,15,9,5)),
    11 => array('vcw'=>'c','letter'=>'S','nodes'=>array(null,null,null,16,10,6)),
    12 => array('vcw'=>'v','letter'=>'A','nodes'=>array(7,13,18,17,null,null)),
    13 => array('vcw'=>'c','letter'=>'M','nodes'=>array(null,14,19,18,12,7)),
    14 => array('vcw'=>'v','letter'=>'Y','nodes'=>array(8,null,20,19,13,null)),
    15 => array('vcw'=>'v','letter'=>'E','nodes'=>array(10,16,22,21,null,9)),
    16 => array('vcw'=>'v','letter'=>'E','nodes'=>array(11,null,null,22,15,10)),
    17 => array('vcw'=>'c','letter'=>'D','nodes'=>array(12,18,23,null,null,null)),
    18 => array('vcw'=>'v','letter'=>'A','nodes'=>array(13,19,24,23,17,12)),
    19 => array('vcw'=>'c','letter'=>'N','nodes'=>array(14,20,25,24,18,13)),
    20 => array('vcw'=>'c','letter'=>'C','nodes'=>array(null,21,26,25,19,14)),
    21 => array('vcw'=>'v','letter'=>'E','nodes'=>array(15,22,27,26,20,null)),
    22 => array('vcw'=>'c','letter'=>'S','nodes'=>array(16,null,null,27,21,15)),
    23 => array('vcw'=>'c','letter'=>'Q','nodes'=>array(18,24,null,null,null,17)),
    24 => array('vcw'=>'v','letter'=>'U','nodes'=>array(19,25,28,null,23,18)),
    25 => array('vcw'=>'v','letter'=>'E','nodes'=>array(20,26,null,28,24,19)),
    26 => array('vcw'=>'v','letter'=>'E','nodes'=>array(21,27,29,null,25,20)),
    27 => array('vcw'=>'c','letter'=>'N','nodes'=>array(22,null,null,29,26,21)),
    28 => array('vcw'=>'c','letter'=>'Z','nodes'=>array(25,null,null,null,null,24)),
    29 => array('vcw'=>'v','letter'=>'O','nodes'=>array(27,null,null,30,null,26)),
    30 => array('vcw'=>'v','letter'=>'O','nodes'=>array(29,null,null,null,null,null))
);


//MAIN
$start = microtime(true);
$walker = new Walker();
$walker->walk();

$history = $walker->get_history();
foreach($history as $k=>$v){
    $history[$k] = $matrix[$v]['letter'];
}
echo 'Found path '.implode(',',$history).' in '.(microtime(true)-$start).' seconds<br>';





//CLASSES
class Walker {
    private $history;
    private $opts_history;
    private $tile;
    private $direction;
    private $vcw;
    private $backtracks;
    
    public function __construct(){
        $this->history = array();
        $this->opts_history = array();
        $this->set_tile(17); //start on tile 17
        $this->direction = 2; //direction 0 points up, 3 points down, ordered clockwise, so we start facing down to the right
        $this->backtracks = 0;
    }
    
    protected function get_tile(){
        return $this->tile;
    }
    
    protected function get_direction(){
        return $this->direction;
    }
    
    public function get_history(){
        return $this->history;
    }
    
    protected function get_vcw(){
        return $this->vcw;
    }
    
    protected function set_tile($tile){
        if(!in_array($tile,range(0,30))){
            exit('fatal error: !$tile');
        }
        $this->tile = $tile;
        $this->history[count($this->history)] = $tile;
        $this->set_vcw();
    }
    
    protected function set_direction($direction){
        if(!in_array($direction,range(0,5))){
            exit('fatal error: !$direction');
        }
        $this->direction = $direction;
    }
    
    protected function set_vcw(){
        global $matrix;
        if(!$matrix){
            exit('fatal error: !$matrix');
        }
        $tile = $this->get_tile();
        $this->vcw = $matrix[$tile]['vcw'];
    }
    
    public function walk(){
        do {
            $opts = $this->get_available_directions();
            if(empty($opts)){
                $opts[0] = $this->unwalk();
            }
            $move = $opts[0];
            $this->make_move($move);
        } while(!$this->check_win_conditions());
    }
    
    protected function unwalk(){
        while(!stristr($this->opts_history[count($this->opts_history)-1],',')) {
            unset($this->history[count($this->opts_history)-1]);
            unset($this->opts_history[count($this->opts_history)-1]);
        }
        $this->tile = $this->history[count($this->opts_history)-1];
        $this->opts_history[count($this->opts_history)-1] = preg_replace('@\d,@','',$this->opts_history[count($this->opts_history)-1]);
        $move = trim($this->opts_history[count($this->opts_history)-1],'()');
        $this->backtracks++;
        return $move;
    }
    
    protected function check_win_conditions(){
        //vcw has to be 'w'
        if($this->get_vcw()!='w'){
            return false;
        }
        //we must have node 13 in our history
        if(!in_array('13',$this->get_history())){
            return false;
        }
        return true;
    }
    
    protected function get_available_directions(){
        //first get current orientation
        $tile = $this->get_tile();
        $dir = $this->get_direction();
        $vcw = $this->get_vcw();
        //if vcw is 'v' we have to look right (cw) 1 or 2 directions 
        if($vcw=='v'){
            $dir1 = ($dir+1)%6;
            $dir2 = ($dir+2)%6;
        //otherwise it will be 'c' and we have to look left (ccw) 1 or 2 directions 
        } else {
            $dir1 = (6+$dir-1)%6;
            $dir2 = (6+$dir-2)%6;
        }
        //would we land on a tile at either of these?
        $opts = array();
        if(!is_null($this->check_matrix($dir1))){
            $opts[] = $dir1;
        }
        if(!is_null($this->check_matrix($dir2))){
            $opts[] = $dir2;
        }
        $this->opts_history[count($this->opts_history)] = '('.implode(',',$opts).')';
        return $opts;
    }
    
    protected function make_move($direction){
        $new_tile = $this->check_matrix($direction);
        $this->set_tile($new_tile);
        $this->set_direction($direction);
    }
    
    protected function check_matrix($direction){
        global $matrix;
        if(!$matrix){
            exit('fatal error: !$matrix');
        }
        $tile = $this->get_tile();
        return $matrix[$tile]['nodes'][$direction];
    }   
}

/*
Result: Found path D,A,A,D,Q,A,N,Y,C,E,N,S,E,K,U,E,K,E,E,S,A,L,F,B,A,Y,M,A,D,Q,A,N,Y,C,E,N,S,E,K,U,E,K,E,E,S,A,L,F,I,Win! in 0.00090217590332031 seconds
*/
?>
