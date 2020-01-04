<?php

class FlagColors{
    private $countries;
    private $base_flags;
    private $test_flags;
    private $max_diff;

    public function __construct(){
        //only keep countries if they follow one of the following color patterns:
        // 1. red + white + blue
        // 2. mainly green
        // 3. blue + green + red + white
        // Normally I'd just do all flags but at 1.5 second per flag and 250 flags it would take our script ~6 minutes to finish. Most php scripts time out after about 30 seconds
        $this->countries = array(
            'Australia',
            'Brazil',
            'Canada',
            'Chile',
            'France',
            'Iceland',
            'Kenya',
            'Kuwait',
            'Liberia',
            'Mexico',
            'Malaysia',
            'New Zealand',
            'Netherlands',
            'Norway',
            'Puerto Rico',
            'Portugal',
            'Russia',
            'Saudi Arabia',
            'Serbia',
            'Slovakia',
            'Slovenia',
            'South Africa',
            'Taiwan',
            'Thailand',
            'United Kingdom',
            'United States',
            'Zambia'
        );

        $this->base_flags = array();

        $this->test_flags = array(
            'Flag 1' => array('url'=>'https://fivethirtyeight.com/wp-content/uploads/2020/01/flag_1.png'),
            'Flag 2' => array('url'=>'https://fivethirtyeight.com/wp-content/uploads/2020/01/flag_2.png'),
            'Flag 3' => array('url'=>'https://fivethirtyeight.com/wp-content/uploads/2020/01/flag_3.png'),
        );

        $this->max_diff = 255*sqrt(3);
    }

    public function run(){
        echo 'Working...<br><br>';
        $this->parse_cia_flags();
        $this->parse_base_flags();
        $this->parse_test_flags();
        $this->make_guesses();
        echo 'Done!';
    }

    public function parse_cia_flags(){
        $url = 'https://www.cia.gov/library/publications/the-world-factbook/docs/flagsoftheworld.html';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);  

        $regex = '@<img alt="(.*) Flag" title="[^"]*" src="../attachments/flags/(.*)" />@';
        preg_match_all($regex,$output,$matches);
        if(count($matches)){
            foreach($matches[1] as $key=>$value){
                if(in_array($value,$this->countries)){
                    $this->base_flags[$value] = array('url'=>'https://www.cia.gov/library/publications/the-world-factbook/attachments/flags/'.$matches[2][$key]);
                }
            }
        }
    }

    protected function parse_base_flags(){
        if(count($this->base_flags)){
            foreach($this->base_flags as $key=>$flag){
                $this->base_flags[$key]['rgb'] = $this->get_average_color($flag['url']);
            }
        }
    }

    protected function parse_test_flags(){
        if(count($this->test_flags)){
            foreach($this->test_flags as $key=>$f){
                $avg = $this->get_average_color($f['url']);
                $this->test_flags[$key]['rgb'] = $avg;
                $diffs = array();
                foreach($this->base_flags as $country=>$c){
                    $diff = $this->hex_diff($avg,$c['rgb']);
                    $diffs[$country] = number_format((100*(($this->max_diff - $diff)/$this->max_diff)),2);
                }
                arsort($diffs);
                $this->test_flags[$key]['guesses'] = $diffs;
            }
        }
    }

    protected function make_guesses(){
        foreach($this->test_flags as $key=>$value){
            echo $key.'<br>';
            $count = 1;
            foreach($value['guesses'] as $country=>$percent){
                echo 'Guess #'.$count.': '.$country.' ('.$percent.'% match)<br>';
                $count++;
                if($count>3){
                    break;
                }
            }
            echo '<br>';
        }
    }

    protected function get_average_color($value){
        $info = getimagesize($value);
        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image_create_func = 'imagecreatefromjpeg';
                break;
            case 'image/png':
                $image_create_func = 'imagecreatefrompng';
                break;
            case 'image/gif':
                $image_create_func = 'imagecreatefromgif';
                break;
        }
        $avg = $image_create_func($value);
        list($width, $height) = getimagesize($value);
        $tmp = imagecreatetruecolor(1, 1);
        imagecopyresampled($tmp, $avg, 0, 0, 0, 0, 1, 1, $width, $height);
        $rgb = imagecolorat($tmp, 0, 0);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        return array($r,$g,$b);
    }

    protected function hex_diff($rgb1,$rgb2){
        return sqrt( pow($rgb1[0]-$rgb2[0],2) + pow($rgb1[1]-$rgb2[1],2) + pow($rgb1[2]-$rgb2[2],2) );
    }
}

$fc = new FlagColors();
$fc->run();

/*
Output

Working...

Flag 1
Guess #1: France (99.45% match)
Guess #2: Slovenia (99.15% match)
Guess #3: Russia (99.04% match)

Flag 2
Guess #1: Brazil (99.77% match)
Guess #2: South Africa (91.23% match)
Guess #3: Zambia (87.22% match)

Flag 3
Guess #1: South Africa (95.75% match)
Guess #2: Kuwait (88.56% match)
Guess #3: Iceland (88.50% match)

Done!

*/

?>
