<?php
$sims = array();
foreach(array(true,false,false,false) as $cwt){ //25% of customers only want tiffany
  for($t=0;$t<=15;$t++){ //tiffany could have anywhere between 0 and 15 minutes left in her haircut
      for($h1=0;$h1<=15;$h1++){ //hairdresser 1, 2 and 3 couuld also each have anywhere between 0 and 15 minutes left in their haircut
            for($h2=0;$h2<=15;$h2++){
                for($h3=0;$h3<=15;$h3++){
                    $m = run_sim($cwt,$t,$h1,$h2,$h3); //return how many minutes we'd be waiting in the current scenario
                    $sims[] = $m; //save into array
                }
            }
        }
    }
}

//Output for after sims are done
echo count($sims).' trials<br>';
echo 'Average wait: '.array_sum($sims)/count($sims).'<br>';
rsort($sims);
echo 'Median wait: '.$sims[round(count($sims)/2)-1].'<br>';
$v = array_count_values($sims); 
arsort($v);
$v = array_keys($v);
echo 'Mode wait: '.$v[0].'<br>';
echo 'Longest wait: '.$sims[0].'<br>';
echo 'Shortest wait: '.$sims[count($sims)-1].'<br>';

//a repeatable function, returns different numbers based on parameters passed
function run_sim($cwt,$t,$h1,$h2,$h3){
  $m = 0; //start with a 0 minute wait
  $cif = true; //always a customer in front of us to start
    $seen = false;
    while(!$seen){
          if($t==0 && !$cif){ //if tiffany finishes and theres no customer in front, we've been seen
            $seen = true;
        } elseif($t==0 && $cif){ //worst case is tiffany finishes first and sees the other customer in front of us
            $cif = false;
            $t+=15;
        } elseif($h1==0 && $cif && !$cwt){ //customer in front might see a different hairdresser if they finish first and he's not specific
            $cif = false;
        } elseif($h2==0 && $cif && !$cwt){
            $cif = false;
        } elseif($h3==0 && $cif && !$cwt){
            $cif = false;
        }
        //every minute we havent been seen, take a minute off each hairdresser time and add minute passed
        if(!$seen){
            $t--;
            $h1--;
            $h2--;
            $h3--;
            $m++;
        }
    }
  //return total minutes passed
    return $m;
}

/*
262144 trials
Average wait: 14.425048828125
Median wait: 15
Mode wait: 15
Longest wait: 30
Shortest wait: 1
*/

?>
