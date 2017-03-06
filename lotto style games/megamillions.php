<?php 
if(isset($_POST['numtix']) && ctype_digit($_POST['numtix']) && isset($_POST['bankroll']) && ctype_digit($_POST['bankroll'])){
	$numtickets = $_POST['numtix'];
	for($i = 1; $i<=$numtickets; $i++){
		/*draw numbers*/
		do{
			$number1 = mt_rand(1,75);
			$number2 = mt_rand(1,75);
			$number3 = mt_rand(1,75);
			$number4 = mt_rand(1,75);
			$number5 = mt_rand(1,75);
		}while($number1==$number2 || $number1==$number3 || $number1==$number4 || $number1==$number5 || $number2==$number3 || $number2==$number4 || $number2==$number5 || $number3==$number4 || $number3==$number5 || $number4==$number5);
		${"ticket".$i} = array($number1,$number2,$number3,$number4,$number5);
		sort( ${"ticket".$i} );
		$mega = mt_rand(1,15);
		${"ticket".$i}[5] = $mega;
	}
	$i = 0;
	$bankaccount = $_POST['bankroll'];
	if($bankaccount > 1000000){
		echo 'One million dollars is the limit for a starting bankroll<br><br>';
		$bankaccount = 1000000;
	}
	$totalspent = 0;
	$totalwon = 0;
	while($bankaccount-$numtickets >= 0){
		$i++;
		echo 'drawing ';
		if($i<10) echo '0';
		echo $i.': ';
		$bankaccount -= $numtickets;
		$totalspent += $numtickets;
		/*draw numbers*/
		do{
			$number1 = mt_rand(1,75);
			$number2 = mt_rand(1,75);
			$number3 = mt_rand(1,75);
			$number4 = mt_rand(1,75);
			$number5 = mt_rand(1,75);
		}while($number1==$number2 || $number1==$number3 || $number1==$number4 || $number1==$number5 || $number2==$number3 || $number2==$number4 || $number2==$number5 || $number3==$number4 || $number3==$number5 || $number4==$number5);
		$finalnumbers = array($number1,$number2,$number3,$number4,$number5);
		sort($finalnumbers);
		$mega = mt_rand(1,15);
		$finalnumbers[5] = $mega;
		for($j=0;$j<=5;$j++){
			if($finalnumbers[$j] < 10){
				echo '0';
			}
			echo $finalnumbers[$j].' ';
		}
		echo '&emsp;';
		/*check tickets*/
		for($j=1;$j<=$numtickets;$j++){
			$nummatch = 0;
			$hitmega = false;
			for($k=0;$k<=4;$k++){
				for($l=0;$l<=4;$l++){
					if(${"ticket".$j}[$k] == $finalnumbers[$l]){
						$nummatch++;
					}
				}
			}
			if(${"ticket".$j}[5] == $finalnumbers[5]){
				$hitmega = true;
			}
			/*give out winnings*/
			if($nummatch == 5){
				if($hitmega){
					echo 'Jackpot! ticket'.$j;exit;
				}
				else{
					echo 'ticket'.$j.' wins 1 million. ';
					$bankaccount += 1000000;
					$totalwon += 1000000;
				}
			}
			elseif($nummatch == 4){
				if($hitmega){
					echo 'ticket'.$j.' wins $5000. ';
					$bankaccount += 5000;
					$totalwon += 5000;
				}
				else{
					echo 'ticket'.$j.' wins $500. ';
					$bankaccount += 500;
					$totalwon += 500;
				}
			}
			elseif($nummatch == 3){
				if($hitmega){
					echo 'ticket'.$j.' wins $50. ';
					$bankaccount += 50;
					$totalwon += 50;
				}
				else{
					echo 'ticket'.$j.' wins $5. ';
					$bankaccount += 5;
					$totalwon += 5;
				}
			}
			elseif($nummatch == 2){
				if($hitmega){
					echo 'ticket'.$j.' wins $5. ';
					$bankaccount += 5;
					$totalwon += 5;
				}
			}
			elseif($nummatch == 1){
				if($hitmega){
					echo 'ticket'.$j.' wins $2. ';
					$bankaccount += 2;
					$totalwon += 2;
				}
			}
			elseif($nummatch == 0){
				if($hitmega){
					echo 'ticket'.$j.' wins $1. ';
					$bankaccount += 1;
					$totalwon += 1;
				}
			}
		}
	echo '<br>';
	}
	echo 'You ended with $'.$bankaccount.' after spending $'.$totalspent.' and winning $'.$totalwon.', thats a '. 100*($totalwon/$totalspent) .'% payback rate';
}
?>
<br>
<h3>Select Number of Tickets and Starting Bankroll to play</h3>
<form action="" method="post">
	<select name="numtix">
	<?php for($a=1;$a<=100;$a++){
		echo '<option value="'.$a.'" ';
			if($_POST['numtix'] == $a) echo 'selected="selected"';
		echo '>'.$a.'</option>';
	}?>
	</select>
	ticket(s) and $<input type="text" name="bankroll" <?php if($_POST['bankroll']) echo 'value="'.$_POST['bankroll'].'"'?>>
	<input type="submit" value="Play">
</form>