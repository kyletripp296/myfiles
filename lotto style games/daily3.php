<?php 
/**************
Daily 3
pick 3 numbers between 0-9
straight -> numbers must match in order (highest payout)
box -> numbers can come in any order (mid payout)
straight/box -> straight win pays half of regular straight win plus half of box win, box pays half of regular box (lowest payout)

advance play -> users can play 1-7 or 14 times with the same card
**************/

//require files
require_once('functions.php');
require_once('config.php');
//global variables
$straightprize = mt_rand(300,700);
$boxprize = mt_rand(100,200);
$straightboxprize = ceil(($straightprize+$boxprize)/2);
$boxonlyprize = ceil($boxprize/2);
$straightwin = false;
$boxwin = false;
$winmsg = '';
$win_amount = 0;
//check for form submit
if(isset($_POST['submitgame'])){
	$usergametype = $_POST['usergametype'];
	$usernumber1 = $_POST['usernumber1'];
	$usernumber2 = $_POST['usernumber2'];
	$usernumber3 = $_POST['usernumber3'];
	$numbers = draw_numbers(3,0,9,false);
	if($usergametype == 's' || $usergametype == 'sb'){
	//straight game type
		if($usernumber1 == $numbers[0] && $usernumber2 == $numbers[1] && $usernumber3 == $numbers[2]){
			//all three numbers must match in order
			$win_amount += $straightprize;
			$straightwin = true;
		}
	}
	if ($usergametype == 'b' || $usergametype == 'sb' || $straightwin == true){
	//boxed game type
		if($numbers[0] == $numbers[1] && $numbers[0] == $numbers[2]){
			//all numbers are the same
			$boxed = array(array($numbers[0],$numbers[1],$numbers[2]));
			$allsamejackpot = true;
		} else if ($numbers[0] == $numbers[1]){
			//two numbers are the same
			$temp1 = array($numbers[0],$numbers[0],$numbers[2]);
			$temp2 = array($numbers[0],$numbers[2],$numbers[0]);
			$temp3 = array($numbers[2],$numbers[0],$numbers[0]);
			$boxed = array($temp1,$temp2,$temp3);
		} else if ($numbers[0] == $numbers[2]){
			//two numbers are the same
			$temp1 = array($numbers[0],$numbers[0],$numbers[1]);
			$temp2 = array($numbers[0],$numbers[1],$numbers[0]);
			$temp3 = array($numbers[1],$numbers[0],$numbers[0]);
			$boxed = array($temp1,$temp2,$temp3);
		} else if ($numbers[1] == $numbers[2]){
			//two numbers are the same
			$temp1 = array($numbers[1],$numbers[1],$numbers[0]);
			$temp2 = array($numbers[1],$numbers[0],$numbers[1]);
			$temp3 = array($numbers[0],$numbers[1],$numbers[1]);
			$boxed = array($temp1,$temp2,$temp3);
		} else {
			//three different numbers
			$temp1 = array($numbers[0],$numbers[1],$numbers[2]);
			$temp2 = array($numbers[0],$numbers[2],$numbers[1]);
			$temp3 = array($numbers[1],$numbers[0],$numbers[2]);
			$temp4 = array($numbers[1],$numbers[2],$numbers[0]);
			$temp5 = array($numbers[2],$numbers[0],$numbers[1]);
			$temp6 = array($numbers[2],$numbers[1],$numbers[0]);
			$boxed = array($temp1,$temp2,$temp3,$temp4,$temp5,$temp6);
		}
		foreach($boxed as $thisperm){
			//check all permutations of the winning numbers for a match
			if($usernumber1 == $thisperm[0] && $usernumber2 == $thisperm[1] && $usernumber3 == $thisperm[2]){
				$win_amount += $boxprize;
				$boxwin = true;
			}
		}
	}
	//all three numbers were the same, ex. 777
	//this is a special case where we mark everyone as a straight winner
	//example of this happening on calottery.com:
	//http://www.calottery.com/play/draw-games/daily-3/winning-numbers/?DrawDate=Aug%2023,%202014&DrawNumber=12479
	if($allsamejackpot == true){
		$boxwin = false;
		$straightwin = true;
		$usergametype = 's';
		$straightprize = $straightboxprize;
		$boxprize = 0;
		$straightboxprize = 0;
		$boxonlyprize = 0;
		$win_amount = $straightprize;
	}
	if($win_amount > 0){
		$won = 'y';
		$wins = 1;
		if($gametype == 'sb'){
			$win_amount = ceil($win_amount/2);
		}
		$winmsg = 'Congratulations! You won $'.$win_amount.'.';
	} else {
		$won = 'n';
		$wins = 0;
		$winmsg = 'Sorry, not this time... Play again soon!';
	}
	if(isset($_COOKIE['lotto_un'])){
		$user_id = strrev(base64_decode(str_rot13($_COOKIE['lotto_un'])));
	} else {
		$user_id = 0;
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	$longip = ip2long($ip);
	$picks = $mysqli->real_escape_string($usernumber1.','.$usernumber2.','.$usernumber3);
	$results = $mysqli->real_escape_string($numbers[0].','.$numbers[1].','.$numbers[2]);
	$sql = "insert ignore into daily3_plays values(NULL,'$user_id','$longip','$picks','$results','$usergametype','$won','$win_amount')";
	lotto_query($sql);
	if($user_id != 0){
		$sql = "insert ignore into daily3_userplays values('$user_id','1','$wins','$win_amount') on duplicate key update plays=plays+1, wins=wins+$wins, totalwon=totalwon+$win_amount";
		lotto_query($sql);
	}
}

require_once 'lotto-header.php';
?>
	<div class="site-wrapper">
		<?php if(!isset($_POST['submitgame'])){ ?>
		<form action="http://www.kyletripp.com/lotto/daily3.php" method="post" name="daily3form">
			<div id="pickgametype">
				<h1>Welcome to Kyle Tripp's Daily 3 Simulator</h1>
				<h3>Based on the California Lottery game of the same name: <a href="http://www.calottery.com/play/draw-games/daily-3" target="_blank">here</a></h3>
				<h3>Please begin by choosing a game type below</h3>
				<div class="gametype gametype-s">Straight</div>
				<div class="gametype gametype-b">Box</div>
				<div class="gametype gametype-sb">Straight/Box</div>
				<input type="hidden" class="usergametype" name="usergametype" value="">
				<div class="descriptions">
					<div class="s-descrip hidden">Your numbers must match the winning numbers in <b>EXACTLY</b> the same order.</div>
					<div class="b-descrip hidden">Your numbers must match the winning numbers in <b>ANY</b> order.</div>
					<div class="sb-descrip hidden"><p>This is a combination of both the Straight and Box playstyles and your bet is split evenly between the two ways of winning.</p><p>If you match the winning numbers in exact order, you win approximately half the Straight prize plus approximately half the Box prize.</p><p>If you match the winning numbers in any order, you win approximately half the Box prize only.</p></div>
				</div>
				<table class="leaderboard">
					<thead>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:50%;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2" class="h2">Top Winners</td>
						</tr>
						<?php
						$sql = "select up.totalwon,lu.username from daily3_userplays up, lotto_user lu where up.user_id=lu.id and lu.banned='n' order by totalwon desc limit 10";
						$result = lotto_query($sql);
						if($result->num_rows > 0){
							while($row = $result->fetch_assoc()){
								$this_amount = $row['totalwon'];
								$this_un = $row['username'];
								echo '<tr><td>'.$this_un.'</td><td>$'.$this_amount.'</td></tr>';
							}
						}?>
					</tbody>
				</table>
				<table class="leaderboard">
					<thead>
						<tr>
							<td style="width:50%;"></td>
							<td style="width:50%;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="2" class="h2">Most Plays</td>
						</tr>
						<?php
						$sql = "select up.plays,lu.username from daily3_userplays up, lotto_user lu where up.user_id=lu.id and lu.banned='n' order by plays desc limit 10";
						$result = lotto_query($sql);
						if($result->num_rows > 0){
							while($row = $result->fetch_assoc()){
								$this_amount = $row['plays'];
								$this_un = $row['username'];
								echo '<tr><td>'.$this_un.'</td><td>'.$this_amount.'</td></tr>';
							}
						}?>
					</tbody>
				</table>
				<div class="lastwin">
					<h2>Last Winner</h2>
					<?php
					$sql = "select user_id,amount from daily3_plays where won='y' order by id desc limit 1";
					$result = lotto_query($sql);
					if($result->num_rows == 0){
						echo '<p>No winners yet.</p>';
					} else{
						$row = $result->fetch_assoc();
						$this_id = $row['user_id'];
						$this_amount = $row['amount'];
						if($this_id == '0'){
							$this_un = 'Guest';
						} else {
							$sql = "select username from lotto_user where id='$this_id'";
							$result = lotto_query($sql);
							$row = $result->fetch_assoc();
							$this_un = $row['username'];
						}
						echo '<p>'.$this_un.' won $'.$this_amount.'</p>';
					}
					?>
				</div>
				<div class="member">
				<?php if(isset($_COOKIE['lotto_un']) && isset($_COOKIE['lotto_name'])){
					echo '<p style="width:100%;text-align:center;">Welcome '.$_COOKIE['lotto_name'].'! <a href="http://www.kyletripp.com/lotto/logout.php">Logout</a></p>';
				} else {
					echo '<p style="width:100%;text-align:center;"><a href="http://www.kyletripp.com/lotto/register.php">Register</a> to track your stats</p><p style="width:100%;text-align:center;"><a href="http://www.kyletripp.com/lotto/login.php">Login</a> to continue playing.</p>';
				} ?>
				</div>
			</div>
			<div id="shownumbers" class="hidden">
				<table class="display-cont">
					<thead>
						<tr>
							<td width="33%;"></td>
							<td width="33%;"></td>
							<td width="33%;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><div class="displaynumber displaynumber1"></div></td>
							<td><div class="displaynumber displaynumber2"></div></td>
							<td><div class="displaynumber displaynumber3"></div></td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" class="usernumber usernumber1" name="usernumber1" value="">
				<input type="hidden" class="usernumber usernumber2" name="usernumber2" value="">
				<input type="hidden" class="usernumber usernumber3" name="usernumber3" value="">
			</div>
			<table id="picknumbers" class="hidden">
				<thead>
					<tr>
						<td width="33%;"></td>
						<td width="33%;"></td>
						<td width="33%;"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="picknumber number1"><p>1</p></div></td>
						<td><div class="picknumber number2"><p>2</p></div></td>
						<td><div class="picknumber number3"><p>3</p></div></td>
					</tr>
					<tr>
						<td><div class="picknumber number4"><p>4</p></div></td>
						<td><div class="picknumber number5"><p>5</p></div></td>
						<td><div class="picknumber number6"><p>6</p></div></td>
					</tr>
					<tr>
						<td><div class="picknumber number7"><p>7</p></div></td>
						<td><div class="picknumber number8"><p>8</p></div></td>
						<td><div class="picknumber number9"><p>9</p></div></td>
					</tr>
					<tr>
						<td><div class="unpicknumber"><p>&larr;</p></div></td>
						<td><div class="picknumber number0"><p>0</p></div></td>
						<td><input type="submit" class="lottosubmit hidden" name="submitgame" value="GO"></td>
					</tr>
				</tbody>
			</table>
		</form>
		
		
		
		
		<?php } else { ?>
		
		
		
		
			<div id="shownumbers">
				<table class="display-cont">
					<thead>
						<tr>
							<td width="25%;"></td>
							<td width="25%;"></td>
							<td width="25%;"></td>
							<td width="25%;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4" class="h2">Pay Table</td>
						</tr>
						<tr>
							<td>Straight</td>
							<td>Box</td>
							<td>Straight and Box</td>
							<td>Box only</td>
						</tr>
						<tr>
							<td><?php echo '$'.$straightprize;?></td>
							<td><?php echo '$'.$boxprize;?></td>
							<td><?php echo '$'.$straightboxprize;?></td>
							<td><?php echo '$'.$boxonlyprize;?></td>
						</tr>
					</tbody>
				</table>
				<table class="display-cont">
					<thead>
						<tr>
							<td width="33%;"></td>
							<td width="33%;"></td>
							<td width="33%;"></td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="3" class="h2">Your Numbers</td>
						</tr>
						<tr>
							<td><div class="displaynumber displaynumber1"><div class="selected"><?php echo $usernumber1;?></div></div></td>
							<td><div class="displaynumber displaynumber2"><div class="selected"><?php echo $usernumber2;?></div></div></td>
							<td><div class="displaynumber displaynumber3"><div class="selected"><?php echo $usernumber3;?></div></div></td>
						</tr>
						<tr style="height:20px;"></tr>
						<tr class="drawing">
							<td colspan="3" class="h2">Drawing numbers...</td>
						</tr>
						<tr class="winningheader hidden">
							<td colspan="3" class="h2">Winning Numbers</td>
						</tr>
						<tr class="winningnumbers">
							<td><div class="displaynumber winningnumber1 hidden"><div class="selected"><?php echo $numbers[0];?></div></div></td>
							<td><div class="displaynumber winningnumber2 hidden"><div class="selected"><?php echo $numbers[1];?></div></div></td>
							<td><div class="displaynumber winningnumber3 hidden"><div class="selected"><?php echo $numbers[2];?></div></div></td>
						</tr>
						<tr style="height:20px;"></tr>
						<tr class="checking hidden">
							<td colspan="3" class="h2">Checking...</td>
						</tr>
						<tr class="results hidden">
							<td colspan="3" class="h2"><?php echo $winmsg;?></td>
						</tr>
						<tr style="height:20px;"></tr>
						<tr class="results hidden">
							<td colspan="3"><a href="http://www.kyletripp.com/lotto/daily3.php" style="text-decoration:none;"><div class="playagain">Play Again</div></a></td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php } ?>
		
		
		
		
	</div>
	<script type="text/javascript">
		$('.gametype').click(function(){
			if($(this).hasClass('gametype-s')){
				//chose straight game type
				$('.usergametype').val('s');
			} else if($(this).hasClass('gametype-b')){
				//chose game type boxed
				$('.usergametype').val('b');
			} else {
				//chose gametype straight/boxed
				$('.usergametype').val('sb');
			}
			$('#pickgametype').addClass('hidden');
			$('#shownumbers').removeClass('hidden');
			$('#picknumbers').removeClass('hidden');
			var p = $('.picknumber').first().width();
			$('.picknumber, .unpicknumber, .displaynumber').css('height',(p/2)+'px');
		});
		$('.gametype').mouseenter(function(){
			if($(this).hasClass('gametype-s')){
				//chose straight game type
				$('.s-descrip').removeClass('hidden');
			} else if($(this).hasClass('gametype-b')){
				//chose game type boxed
				$('.b-descrip').removeClass('hidden');
			} else {
				//chose gametype straight/boxed
				$('.sb-descrip').removeClass('hidden');
			}
		});
		$('.gametype').mouseleave(function(){
			if($(this).hasClass('gametype-s')){
				//chose straight game type
				$('.s-descrip').addClass('hidden');
			} else if($(this).hasClass('gametype-b')){
				//chose game type boxed
				$('.b-descrip').addClass('hidden');
			} else {
				//chose gametype straight/boxed
				$('.sb-descrip').addClass('hidden');
			}
		});
		$('.picknumber').click(function(){
			console.log('clicked picknumber');
			if($(this).hasClass('number1')){
				var html= '<div class="selected">1</div>';
				var numval = '1';
			} else if($(this).hasClass('number2')){
				var html = '<div class="selected">2</div>';
				var numval = '2';
			} else if($(this).hasClass('number3')){
				var html = '<div class="selected">3</div>';
				var numval = '3';
			} else if($(this).hasClass('number4')){
				var html = '<div class="selected">4</div>';
				var numval = '4';
			} else if($(this).hasClass('number5')){
				var html = '<div class="selected">5</div>';
				var numval = '5';
			} else if($(this).hasClass('number6')){
				var html = '<div class="selected">6</div>';
				var numval = '6';
			} else if($(this).hasClass('number7')){
				var html = '<div class="selected">7</div>';
				var numval = '7';
			} else if($(this).hasClass('number8')){
				var html = '<div class="selected">8</div>';
				var numval = '8';
			} else if($(this).hasClass('number9')){
				var html = '<div class="selected">9</div>';
				var numval = '9';
			} else {
				var html = '<div class="selected">0</div>';
				var numval = '0';
			}
			if($('.usernumber1').val() == ''){
				$('.usernumber1').val(numval);
				$('.displaynumber1').html(html);
			} else if($('.usernumber2').val() == ''){
				$('.usernumber2').val(numval);
				$('.displaynumber2').html(html);
			} else if($('.usernumber3').val() == ''){
				$('.usernumber3').val(numval);
				$('.displaynumber3').html(html);
				$('.lottosubmit').removeClass('hidden');
			}
		});
		$('.unpicknumber').click(function(){
			console.log('clicked unpick number');
			if($('.usernumber3').val() != ''){
				$('.usernumber3').val('');
				$('.displaynumber3').html('');
				$('.lottosubmit').addClass('hidden');
			} else if($('.usernumber2').val() != ''){
				$('.usernumber2').val('');
				$('.displaynumber2').html('');
			} else if($('.usernumber1').val() != ''){
				$('.usernumber1').val('');
				$('.displaynumber1').html('');
			} else {
				//back to title screen
				$('#pickgametype').removeClass('hidden');
				$('#shownumbers').addClass('hidden');
				$('#picknumbers').addClass('hidden');
			}
		});
		<?php if(isset($_POST['submitgame'])){ ?>
		$(window).load(function(){
			var d = $('.displaynumber').first().width();
			$('.displaynumber').css('height',(d/2)+'px')
			setTimeout(function(){$('.drawing').addClass('hidden');$('.winningheader').removeClass('hidden');},1500);
			setTimeout(function(){$('.winningnumber1').removeClass('hidden');},2000);
			setTimeout(function(){$('.winningnumber2').removeClass('hidden');},2500);
			setTimeout(function(){$('.winningnumber3, .checking').removeClass('hidden');},3000);
			setTimeout(function(){$('.checking').addClass('hidden');$('.results').removeClass('hidden');},4000);
		});
		<?php } ?>
	</script>
</body>
</html>