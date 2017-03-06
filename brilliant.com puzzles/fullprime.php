<?php
/* find the sum of all full primes */
/* a full prime is any number like 2113 */
/* where 2113 is prime, as are 113, 13 and 3 */
set_time_limit(0);
    $primes_arr = array(2,3,5,7);
    echo '<pre>'.print_r($primes_arr,true).'</pre>';
    echo 'total primes so far: '.count($primes_arr).'<br>';
    
    findNextDigitPrimes($primes_arr);
    
    echo '<pre>'.print_r($primes_arr,true).'</pre>';
    echo 'total primes: '.count($primes_arr).'<br>';
    echo 'total sum: '.array_sum($primes_arr).'<br>';
    
    exit('finished');
    
    
    
    
    
    function findNextDigitPrimes($arr){
        global $primes_arr;
        for($i=1;$i<=9;$i++){
            for($j=0;$j<count($arr);$j++){
                $test = $i.$arr[$j];
                if(isPrime($test)){
                    $new_primes[] = $test;
                }
            }
        }
        $primes_arr = array_merge($primes_arr,$new_primes);
        if(count($new_primes)){
            //echo 'calling find next recursively, found these new primes<br>';
            echo '<pre>'.print_r($new_primes,true).'</pre>';
            echo 'total primes so far: '.count($primes_arr).'<br>';
            //echo 'count($new_primes): '.count($new_primes).'<br>';
            //echo 'current sum of $primes: '.array_sum($primes_arr).'<br>';
            findNextDigitPrimes($new_primes);
        }
    }
    
 function isPrime($n) {
  $i = 2;
 
  if ($n == 2) {
   return true;	
  }
 
  $sqrtN = sqrt($n);
  while ($i <= $sqrtN) {
   if ($n % $i == 0) {
    return false;
   }
   $i++;
  }
 
  return true;
 }
